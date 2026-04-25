<?php

use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;
use Webkul\Wallet\Payment\Wallet;

it('config payment_methods contains wallet key', function () {
    expect(config('payment_methods'))->toHaveKey('wallet')
        ->and(config('payment_methods.wallet.active'))->toBeTrue();
});

it('WalletPayment isAvailable returns false when unauthenticated', function () {
    CoreConfig::updateOrCreate(
        ['code' => 'sales.payment_methods.wallet.active', 'channel_code' => 'default'],
        ['value' => '1'],
    );

    expect((new Wallet)->isAvailable())->toBeFalse();
});

it('WalletPayment isAvailable returns true for authenticated customer regardless of verification status', function () {
    CoreConfig::updateOrCreate(
        ['code' => 'sales.payment_methods.wallet.active', 'channel_code' => 'default'],
        ['value' => '1'],
    );

    $customer = Customer::factory()->create(['verification_status' => 'pending']);

    $this->actingAs($customer, 'customer');

    expect((new Wallet)->isAvailable())->toBeTrue();
});

it('WalletPayment isAvailable returns true for verified and authenticated customer', function () {
    CoreConfig::updateOrCreate(
        ['code' => 'sales.payment_methods.wallet.active', 'channel_code' => 'default'],
        ['value' => '1'],
    );

    $customer = Customer::factory()->create(['verification_status' => 'approved']);

    $this->actingAs($customer, 'customer');

    expect((new Wallet)->isAvailable())->toBeTrue();
});

it('WalletPayment isAvailable returns false when disabled in admin', function () {
    CoreConfig::updateOrCreate(
        ['code' => 'sales.payment_methods.wallet.active', 'channel_code' => 'default'],
        ['value' => '0'],
    );

    $customer = Customer::factory()->create(['verification_status' => 'approved']);

    $this->actingAs($customer, 'customer');

    expect((new Wallet)->isAvailable())->toBeFalse();
});
