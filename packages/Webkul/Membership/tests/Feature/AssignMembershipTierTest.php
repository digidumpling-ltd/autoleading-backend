<?php

use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\Customer\Models\CustomerGroup;
use Webkul\Membership\Listeners\AssignMembershipTier;
use Webkul\Membership\Models\TierRule;
use Webkul\Wallet\Events\WalletBalanceUpdated;

function ensureGroup(string $code, string $name): CustomerGroup
{
    return CustomerGroup::firstOrCreate(['code' => $code], ['name' => $name]);
}

function makeTierCustomer(string $groupCode = 'general'): BaseCustomer
{
    $group = ensureGroup($groupCode, ucfirst($groupCode));

    return BaseCustomer::factory()->create(['customer_group_id' => $group->id]);
}

function seedTierRules(): void
{
    $general  = ensureGroup('general',  'General');
    $gold     = ensureGroup('gold',     'Gold');
    $platinum = ensureGroup('platinum', 'Platinum');

    TierRule::insert([
        ['min_balance' => 0,   'max_balance' => 99.99,  'customer_group_id' => $general->id,  'sort_order' => 1, 'created_at' => now(), 'updated_at' => now()],
        ['min_balance' => 100, 'max_balance' => 399.99, 'customer_group_id' => $gold->id,     'sort_order' => 2, 'created_at' => now(), 'updated_at' => now()],
        ['min_balance' => 400, 'max_balance' => 999.99, 'customer_group_id' => $platinum->id, 'sort_order' => 3, 'created_at' => now(), 'updated_at' => now()],
    ]);
}

it('assigns correct group when balance falls in a tier range', function () {
    seedTierRules();

    $customer = makeTierCustomer('general');

    app(AssignMembershipTier::class)->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 0.0,
        newBalance: 250.0,
        reason: 'topup',
    ));

    expect($customer->fresh()->group->code)->toBe('gold');
});

it('leaves group unchanged when no tier matches the balance', function () {
    seedTierRules();

    $customer = makeTierCustomer('general');

    app(AssignMembershipTier::class)->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 0.0,
        newBalance: 1500.0,
        reason: 'topup',
    ));

    expect($customer->fresh()->group->code)->toBe('general');
});

it('skips update when customer is already in the target group', function () {
    seedTierRules();

    $customer = makeTierCustomer('gold');

    app(AssignMembershipTier::class)->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 100.0,
        newBalance: 200.0,
        reason: 'topup',
    ));

    expect($customer->fresh()->group->code)->toBe('gold');
});

it('applies inclusive boundary at min balance', function () {
    seedTierRules();

    $customer = makeTierCustomer('general');

    app(AssignMembershipTier::class)->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 50.0,
        newBalance: 100.0,
        reason: 'topup',
    ));

    expect($customer->fresh()->group->code)->toBe('gold');
});

it('applies inclusive boundary at max balance', function () {
    seedTierRules();

    $customer = makeTierCustomer('general');

    app(AssignMembershipTier::class)->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 200.0,
        newBalance: 399.99,
        reason: 'admin_deduct',
    ));

    expect($customer->fresh()->group->code)->toBe('gold');
});

it('does nothing when no tier rules exist', function () {
    $customer = makeTierCustomer('general');

    app(AssignMembershipTier::class)->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 0.0,
        newBalance: 200.0,
        reason: 'topup',
    ));

    expect($customer->fresh()->group->code)->toBe('general');
});
