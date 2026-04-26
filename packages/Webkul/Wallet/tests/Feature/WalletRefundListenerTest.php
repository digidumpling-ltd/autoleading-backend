<?php

use Webkul\Core\Models\Channel;
use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Sales\Models\Refund;
use Webkul\Wallet\Listeners\WalletInvoiceListener;
use Webkul\Wallet\Listeners\WalletRefundListener;
use Webkul\Wallet\Models\Channel as WalletChannel;
use Webkul\Wallet\Models\Customer as WalletCustomer;

function makeWalletOrderForRefund(int $customerId, string $paymentMethod = 'wallet'): array
{
    $channel = Channel::first();
    $order   = Order::factory()->create(['customer_id' => $customerId, 'channel_id' => $channel->id]);

    OrderPayment::create(['order_id' => $order->id, 'method' => $paymentMethod]);

    return [$order->load('payment'), $channel];
}

function makeRefund(int $orderId, float $total): Refund
{
    return Refund::create([
        'order_id'              => $orderId,
        'state'                 => 'refunded',
        'total_qty'             => 1,
        'base_currency_code'    => 'USD',
        'channel_currency_code' => 'USD',
        'order_currency_code'   => 'USD',
        'base_grand_total'      => $total,
        'grand_total'           => $total,
    ])->load('order.payment');
}

function payInvoiceViaWallet(Order $order, float $amount): void
{
    $invoice = Invoice::create([
        'order_id'           => $order->id,
        'state'              => 'paid',
        'base_grand_total'   => $amount,
        'grand_total'        => $amount,
        'base_currency_code' => 'USD',
    ])->load('order.payment');

    app(WalletInvoiceListener::class)->handle($invoice);
}

it('restores full wallet balance when wallet order is fully refunded', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrderForRefund($customer->id);
    payInvoiceViaWallet($order, 200.00);

    expect($customer->fresh()->balanceFloatNum)->toBe(300.0);

    $refund = makeRefund($order->id, 200.00);
    app(WalletRefundListener::class)->handle($refund);

    expect($customer->fresh()->balanceFloatNum)->toBe(500.0);
});

it('restores only the refunded amount on partial refund', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    [$order] = makeWalletOrderForRefund($customer->id);
    payInvoiceViaWallet($order, 200.00);

    $refund = makeRefund($order->id, 80.00);
    app(WalletRefundListener::class)->handle($refund);

    expect($customer->fresh()->balanceFloatNum)->toBe(380.0);
});

it('reduces channel wallet balance by the refunded amount', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(300.00);

    [$order, $channel] = makeWalletOrderForRefund($customer->id);
    payInvoiceViaWallet($order, 100.00);

    $walletChannel = WalletChannel::find($channel->id);
    $afterPaymentBalance = $walletChannel->balanceFloatNum;

    $refund = makeRefund($order->id, 100.00);
    app(WalletRefundListener::class)->handle($refund);

    expect($walletChannel->fresh()->balanceFloatNum)->toBe($afterPaymentBalance - 100.0);
});

it('is idempotent — second handle() call does not double-credit', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(400.00);

    [$order] = makeWalletOrderForRefund($customer->id);
    payInvoiceViaWallet($order, 100.00);

    $refund   = makeRefund($order->id, 100.00);
    $listener = app(WalletRefundListener::class);
    $listener->handle($refund);
    $listener->handle($refund);

    expect($customer->fresh()->balanceFloatNum)->toBe(400.0);

    expect(
        $customer->transactions()
            ->where('type', 'deposit')
            ->where('meta->type', 'wallet_refund')
            ->where('meta->refund_id', $refund->id)
            ->count()
    )->toBe(1);
});

it('does nothing for non-wallet payment methods', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(300.00);

    [$order] = makeWalletOrderForRefund($customer->id, 'cashondelivery');
    $refund  = makeRefund($order->id, 120.00);

    app(WalletRefundListener::class)->handle($refund);

    expect($customer->fresh()->balanceFloatNum)->toBe(300.0);
});

it('does nothing when refund total is zero', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(200.00);

    [$order] = makeWalletOrderForRefund($customer->id);
    $refund  = makeRefund($order->id, 0.00);

    app(WalletRefundListener::class)->handle($refund);

    expect($customer->fresh()->balanceFloatNum)->toBe(200.0);
});
