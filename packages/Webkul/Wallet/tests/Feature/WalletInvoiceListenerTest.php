<?php

use Illuminate\Support\Facades\DB;
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
use Webkul\Wallet\Models\Customer as WalletCustomer;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

function makeWalletOrder(int $customerId, string $paymentMethod = 'wallet'): array
{
    $order = Order::factory()->create(['customer_id' => $customerId, 'status' => 'pending']);
    OrderPayment::create(['order_id' => $order->id, 'method' => $paymentMethod]);

    return [$order];
}

function makeInvoice(int $orderId, float $total = 200.00): Invoice
{
    $invoice = Invoice::create([
        'order_id'           => $orderId,
        'state'              => 'paid',
        'base_grand_total'   => $total,
        'grand_total'        => $total,
        'base_currency_code' => 'USD',
    ]);

    return $invoice->load('order.payment');
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
    $listener->handle($invoice); // second call — should be no-op

    expect($customer->fresh()->balanceFloatNum)->toBe(400.0);
    expect(OrderTransaction::where('invoice_id', $invoice->id)->where('payment_method', 'wallet')->count())->toBe(1);
});

it('rolls back invoice when balance is insufficient — simulating InvoiceRepository transaction', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    [$order] = makeWalletOrder($customer->id);

    // Simulate the DB::beginTransaction() pattern from InvoiceRepository::create()
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

    // Invoice must not exist after rollback
    expect(Invoice::where('order_id', $order->id)->exists())->toBeFalse();
    // Wallet balance must be unchanged
    expect($customer->fresh()->balanceFloatNum)->toBe(50.0);
    // No OrderTransaction must have been created
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

    $order = Order::factory()->create(['customer_id' => $customer->id, 'status' => 'pending']);
    OrderPayment::create(['order_id' => $order->id, 'method' => 'wallet']);
    $item = OrderItem::factory()->create([
        'order_id'     => $order->id,
        'qty_ordered'  => 1,
        'qty_invoiced' => 0,
        'qty_canceled' => 0,
    ]);

    // Mock InvoiceRepository so complex DB setup is not required;
    // only create() needs to simulate the wallet exception
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
