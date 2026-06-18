<?php

use Illuminate\Support\Facades\Mail;
use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;
use Webkul\Rewards\Models\RewardPoint;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderPayment;

function enableRewardsForMobilePass(): void
{
    CoreConfig::updateOrCreate(
        ['code' => 'reward.general.general.module-status', 'channel_code' => null, 'locale_code' => null],
        ['value' => '1']
    );
}

function makeWalletOrder(int $customerId, string $status = 'completed', string $paymentMethod = 'wallet'): Order
{
    $order = Order::factory()->create([
        'customer_id' => $customerId,
        'status' => $status,
    ]);

    OrderPayment::create(['order_id' => $order->id, 'method' => $paymentMethod]);

    return $order->load('payment');
}

// Mail::fake() suppresses the Shop order email listener triggered by checkout.order.save.after,
// which fails with factory orders that have no real email address.
beforeEach(fn () => Mail::fake());

it('rewards are credited when a wallet payment order is completed', function () {
    enableRewardsForMobilePass();

    $customer = Customer::factory()->create();
    $order = makeWalletOrder($customer->id, 'completed');

    event('checkout.order.save.after', $order);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('note', 'Wallet payment reward order:'.$order->id)
            ->exists()
    )->toBeTrue();
});

it('rewards are credited when a wallet payment order is processing', function () {
    enableRewardsForMobilePass();

    $customer = Customer::factory()->create();
    $order = makeWalletOrder($customer->id, 'processing');

    event('checkout.order.save.after', $order);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('note', 'Wallet payment reward order:'.$order->id)
            ->exists()
    )->toBeTrue();
});

it('rewards are not credited when order is cancelled', function () {
    enableRewardsForMobilePass();

    $customer = Customer::factory()->create();
    $order = makeWalletOrder($customer->id, 'canceled');

    event('checkout.order.save.after', $order);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('note', 'Wallet payment reward order:'.$order->id)
            ->exists()
    )->toBeFalse();
});

it('rewards are not credited for non-wallet payment methods', function () {
    enableRewardsForMobilePass();

    $customer = Customer::factory()->create();
    $order = makeWalletOrder($customer->id, 'completed', 'cashondelivery');

    event('checkout.order.save.after', $order);

    expect(
        RewardPoint::where('customer_id', $customer->id)->exists()
    )->toBeFalse();
});

it('rewards are not credited twice for the same order', function () {
    enableRewardsForMobilePass();

    $customer = Customer::factory()->create();
    $order = makeWalletOrder($customer->id, 'completed');

    event('checkout.order.save.after', $order);
    event('checkout.order.save.after', $order);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('note', 'Wallet payment reward order:'.$order->id)
            ->count()
    )->toBe(1);
});
