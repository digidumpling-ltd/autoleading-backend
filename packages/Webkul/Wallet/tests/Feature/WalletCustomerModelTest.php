<?php

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\Wallet\Models\Customer as WalletCustomer;

it('wallet Customer model implements Wallet and WalletFloat interfaces', function () {
    $customer = new WalletCustomer;

    expect($customer)->toBeInstanceOf(Wallet::class)
        ->and($customer)->toBeInstanceOf(WalletFloat::class);
});

it('customer can deposit to wallet', function () {
    $base = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);

    $customer->depositFloat(100.00);

    expect($customer->fresh()->balanceFloatNum)->toBe(100.0);
});

it('customer can withdraw from wallet', function () {
    $base = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);

    $customer->depositFloat(100.00);
    $customer->withdrawFloat(40.00);

    expect($customer->fresh()->balanceFloatNum)->toBe(60.0);
});

it('canWithdrawFloat returns false when balance is insufficient', function () {
    $base = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);

    $customer->depositFloat(50.00);

    expect($customer->canWithdrawFloat(200.00))->toBeFalse()
        ->and($customer->fresh()->balanceFloatNum)->toBe(50.0);
});

it('deposit creates a transaction record', function () {
    $base = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);

    $transaction = $customer->depositFloat(75.50);

    expect($transaction)->not->toBeNull()
        ->and($transaction->type)->toBe('deposit')
        ->and((float) $transaction->amountFloat)->toBe(75.5);
});
