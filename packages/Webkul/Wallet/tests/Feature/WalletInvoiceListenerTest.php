<?php

use Illuminate\Support\Facades\DB;
use Webkul\Core\Models\Channel;
use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Models\OrderTransaction;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\User\Models\Admin;
use Webkul\User\Models\Role;
use Webkul\Wallet\Listeners\WalletInvoiceListener;
use Webkul\Wallet\Models\Channel as WalletChannel;
use Webkul\Wallet\Models\Customer as WalletCustomer;

use function Pest\Laravel\actingAs;

function makeWalletOrder(int $customerId, string $paymentMethod = 'wallet'): array
{
    $channel = Channel::first();
    $order   = Order::factory()->create(['customer_id' => $customerId, 'status' => 'pending', 'channel_id' => $channel->id]);
    OrderPayment::create(['order_id' => $order->id, 'method' => $paymentMethod]);

    return [$order, $channel];
}

function makeInvoice(int $orderId, float $total = 200.00): Invoice
{
    return Invoice::create([
        'order_id'           => $orderId,
        'state'              => 'paid',
        'base_grand_total'   => $total,
        'grand_total'        => $total,
        'base_currency_code' => 'USD',
    ])->load('order.payment');
}

it('deducts wallet balance on invoice confirmation for wallet order', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrder($customer->id);
    $invoice = makeInvoice($order->id, 200.00);

    app(WalletInvoiceListener::class)->handle($invoice);

    expect($customer->fresh()->balanceFloatNum)->toBe(300.0);
    expect(OrderTransaction::where('invoice_id', $invoice->id)->where('payment_method', 'wallet')->exists())->toBeTrue();
});

it('credits the channel wallet when customer pays', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order, $channel] = makeWalletOrder($customer->id);
    $invoice = makeInvoice($order->id, 200.00);

    $walletChannel = WalletChannel::find($channel->id);
    $initialBalance = $walletChannel->balanceFloatNum;

    app(WalletInvoiceListener::class)->handle($invoice);

    expect($walletChannel->fresh()->balanceFloatNum)->toBe($initialBalance + 200.0);
});

it('creates wallet payment transactions for customer and channel', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrder($customer->id);
    $invoice = makeInvoice($order->id, 150.00);
    $channel = WalletChannel::find($order->channel_id);

    app(WalletInvoiceListener::class)->handle($invoice);

    $customerTx = $customer->transactions()
        ->where('type', 'withdraw')
        ->where('meta->type', 'wallet_payment')
        ->where('meta->invoice_id', $invoice->id)
        ->first();

    $channelTx = $channel->transactions()
        ->where('type', 'deposit')
        ->where('meta->type', 'wallet_payment')
        ->where('meta->invoice_id', $invoice->id)
        ->first();

    expect($customerTx)->not->toBeNull();
    expect($channelTx)->not->toBeNull();
});

it('creates order transaction with correct fields after deduction', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrder($customer->id);
    $invoice = makeInvoice($order->id, 150.00);

    app(WalletInvoiceListener::class)->handle($invoice);

    $tx = OrderTransaction::where('invoice_id', $invoice->id)->first();
    expect($tx)->not->toBeNull()
        ->and($tx->payment_method)->toBe('wallet')
        ->and($tx->status)->toBe('paid')
        ->and($tx->type)->toBe('capture')
        ->and((float) $tx->amount)->toBe(150.0)
        ->and($tx->order_id)->toBe($order->id);
});

it('throws exception and leaves wallet unchanged when balance is insufficient', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    [$order] = makeWalletOrder($customer->id);
    $invoice = makeInvoice($order->id, 200.00);

    expect(fn () => app(WalletInvoiceListener::class)->handle($invoice))
        ->toThrow(\Exception::class);

    expect($customer->fresh()->balanceFloatNum)->toBe(50.0);
    expect(OrderTransaction::where('invoice_id', $invoice->id)->exists())->toBeFalse();
});

it('is idempotent — second call does not double-deduct', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrder($customer->id);
    $invoice = makeInvoice($order->id, 100.00);

    $listener = app(WalletInvoiceListener::class);
    $listener->handle($invoice);
    $listener->handle($invoice);

    expect($customer->fresh()->balanceFloatNum)->toBe(400.0);
    expect(OrderTransaction::where('invoice_id', $invoice->id)->where('payment_method', 'wallet')->count())->toBe(1);
});

it('rolls back invoice when balance is insufficient — simulating InvoiceRepository transaction', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    [$order] = makeWalletOrder($customer->id);

    try {
        DB::beginTransaction();

        $invoice = Invoice::create([
            'order_id'           => $order->id,
            'state'              => 'paid',
            'base_grand_total'   => 200.00,
            'base_currency_code' => 'USD',
        ]);
        $invoice->load('order.payment');

        app(WalletInvoiceListener::class)->handle($invoice);

        DB::commit();
    } catch (\Exception $e) {
        DB::rollBack();
    }

    expect(Invoice::where('order_id', $order->id)->exists())->toBeFalse();
    expect($customer->fresh()->balanceFloatNum)->toBe(50.0);
    expect(OrderTransaction::where('order_id', $order->id)->exists())->toBeFalse();
});

it('does nothing for non-wallet payment methods', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrder($customer->id, 'cashondelivery');
    $invoice = makeInvoice($order->id, 200.00);

    app(WalletInvoiceListener::class)->handle($invoice);

    expect($customer->fresh()->balanceFloatNum)->toBe(500.0);
    expect(OrderTransaction::where('invoice_id', $invoice->id)->where('payment_method', 'wallet')->exists())->toBeFalse();
});

it('admin sees error flash when wallet invoice creation fails due to insufficient balance', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    $channel = Channel::first();
    $order   = Order::factory()->create(['customer_id' => $customer->id, 'status' => 'pending', 'channel_id' => $channel->id]);
    OrderPayment::create(['order_id' => $order->id, 'method' => 'wallet']);
    $item = OrderItem::factory()->create([
        'order_id'     => $order->id,
        'qty_ordered'  => 1,
        'qty_invoiced' => 0,
        'qty_canceled' => 0,
    ]);

    $this->mock(InvoiceRepository::class)
        ->shouldReceive('haveProductToInvoice')->once()->andReturn(true)
        ->shouldReceive('isValidQuantity')->once()->andReturn(true)
        ->shouldReceive('create')->once()
        ->andThrow(new \Exception('Insufficient wallet balance. Cannot create invoice.'));

    $role  = Role::create(['name' => 'Test Role ' . uniqid(), 'permission_type' => 'all', 'permissions' => []]);
    $admin = Admin::factory()->create(['role_id' => $role->id]);

    actingAs($admin, 'admin')
        ->post(route('admin.sales.invoices.store', $order->id), [
            'invoice' => ['items' => [$item->id => 1]],
        ])
        ->assertRedirect()
        ->assertSessionHas('error', 'Insufficient wallet balance. Cannot create invoice.');
});
