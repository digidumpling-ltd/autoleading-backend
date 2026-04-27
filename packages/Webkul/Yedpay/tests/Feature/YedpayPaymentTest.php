<?php

use Webkul\Core\Models\CoreConfig;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderTransaction;
use Webkul\Yedpay\Payment\Yedpay;
use Webkul\Yedpay\Services\YedpayService;

function yedpayConfig(string $key, string $value): void
{
    CoreConfig::updateOrCreate(
        ['code' => "sales.payment_methods.yedpay.{$key}", 'channel_code' => 'default'],
        ['value' => $value]
    );
}

beforeEach(function () {
    yedpayConfig('active', '1');
    yedpayConfig('sandbox', '1');
    yedpayConfig('api_key', 'fake-api-key-for-testing');
    yedpayConfig('sandbox_api_key', 'fake-sandbox-api-key');
    yedpayConfig('signing_key', '12345678901234567890123456789012');
});

it('reports credentials invalid when sandbox_api_key is empty', function () {
    yedpayConfig('sandbox_api_key', '');

    $yedpay = app(Yedpay::class);

    expect($yedpay->hasValidCredentials())->toBeFalse();
});

it('reports credentials invalid when signing_key is empty', function () {
    yedpayConfig('signing_key', '');

    $yedpay = app(Yedpay::class);

    expect($yedpay->hasValidCredentials())->toBeFalse();
});

it('redirects to cart when credentials are missing', function () {
    yedpayConfig('api_key', '');
    yedpayConfig('signing_key', '');

    $response = $this->get(route('yedpay.standard.redirect'));

    $response->assertRedirect(route('shop.checkout.cart.index'));
    $response->assertSessionHas('error');
});

it('redirects to cart when no cart exists', function () {
    \Webkul\Checkout\Facades\Cart::shouldReceive('getCart')->andReturn(null);

    $response = $this->get(route('yedpay.standard.redirect'));

    $response->assertRedirect(route('shop.checkout.cart.index'));
    $response->assertSessionHas('error');
});

it('redirects to yedpay payment url on valid redirect', function () {
    $cart = $this->createCartWithItems('yedpay');

    $this->mock(YedpayService::class)
        ->shouldReceive('createPayment')
        ->once()
        ->andReturn('https://api-staging.yedpay.com/pay/test-123');

    $response = $this->get(route('yedpay.standard.redirect'));

    $response->assertRedirect('https://api-staging.yedpay.com/pay/test-123');
});

it('redirects to cart when callback signature verification fails', function () {
    $this->mock(YedpayService::class)
        ->shouldReceive('verifyCallback')
        ->andReturn(false);

    $response = $this->get(route('yedpay.payment.success', [
        'transaction_id' => 'txn-123',
        'status'         => 'paid',
        'sign'           => 'bad-sign',
        'sign_type'      => 'HMAC_SHA256',
    ]));

    $response->assertRedirect(route('shop.checkout.cart.index'));
    $response->assertSessionHas('error');
});

it('redirects to cart on cancel', function () {
    $response = $this->get(route('yedpay.payment.cancel'));

    $response->assertRedirect(route('shop.checkout.cart.index'));
    $response->assertSessionHas('error');
});

it('redirects to cart when session cart id is missing on success callback', function () {
    $this->mock(YedpayService::class)
        ->shouldReceive('verifyCallback')
        ->andReturn(true);

    $response = $this->get(route('yedpay.payment.success', [
        'transaction_id' => 'txn-no-session',
        'status'         => 'paid',
        'sign'           => 'valid',
        'sign_type'      => 'HMAC_SHA256',
    ]));

    $response->assertRedirect(route('shop.checkout.cart.index'));
    $response->assertSessionHas('error');
});

it('creates order invoice and transaction on successful yedpay callback', function () {
    $cart = $this->createCartWithItems('yedpay');

    $this->mock(YedpayService::class)
        ->shouldReceive('verifyCallback')->andReturn(true)
        ->shouldReceive('isPaymentPaid')->andReturn(true);

    session([
        'yedpay_cart_id'   => $cart->id,
        'yedpay_custom_id' => 'bagisto-' . $cart->id . '-9999',
    ]);

    $response = $this->get(route('yedpay.payment.success', [
        'transaction_id' => 'txn-success-001',
        'status'         => 'paid',
        'sign'           => 'valid-sign',
        'sign_type'      => 'HMAC_SHA256',
    ]));

    $response->assertRedirect(route('shop.checkout.onepage.success'));
    $response->assertSessionHas('success');
    $response->assertSessionHas('order_id');

    $order = Order::where('customer_id', $cart->customer_id)->first();
    expect($order)->not->toBeNull()
        ->and($order->status)->toBe('processing');

    $invoice = Invoice::where('order_id', $order->id)->first();
    expect($invoice)->not->toBeNull();

    $transaction = OrderTransaction::where('transaction_id', 'txn-success-001')->first();
    expect($transaction)->not->toBeNull()
        ->and($transaction->payment_method)->toBe('yedpay')
        ->and($transaction->status)->toBe('paid')
        ->and($transaction->order_id)->toBe($order->id);

    $cart->refresh();
    expect($cart->is_active)->toBe(0);
});
