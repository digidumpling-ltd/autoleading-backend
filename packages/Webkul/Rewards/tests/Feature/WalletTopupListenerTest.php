<?php

use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Models\CustomerGroup;
use Webkul\Rewards\Listeners\WalletTopup;
use Webkul\Rewards\Models\RewardPoint;
use Webkul\Rewards\Models\WalletTopupRewardRule;
use Webkul\Wallet\Events\WalletBalanceUpdated;

function enableRewardsModule(): void
{
    CoreConfig::firstOrCreate(
        ['code' => 'reward.general.general.module-status'],
        ['value' => '1', 'channel_code' => null, 'locale_code' => null]
    );
}

function makeCustomerInGroup(?int $groupId = null): Customer
{
    if ($groupId === null) {
        $group = CustomerGroup::firstOrCreate(['code' => 'general'], ['name' => 'General']);
        $groupId = $group->id;
    }

    return Customer::factory()->create(['customer_group_id' => $groupId]);
}

function makeFixedRule(int $points, array $overrides = []): WalletTopupRewardRule
{
    return WalletTopupRewardRule::create(array_merge([
        'customer_group_id' => null,
        'trigger'           => 'wallet_topup',
        'mode'              => 'fixed',
        'value'             => $points,
        'min_amount'        => null,
        'max_amount'        => null,
        'priority'          => 0,
        'status'            => true,
    ], $overrides));
}

function makePercentRule(float $percent, array $overrides = []): WalletTopupRewardRule
{
    return WalletTopupRewardRule::create(array_merge([
        'customer_group_id' => null,
        'trigger'           => 'wallet_topup',
        'mode'              => 'percent',
        'value'             => $percent,
        'min_amount'        => null,
        'max_amount'        => null,
        'priority'          => 0,
        'status'            => true,
    ], $overrides));
}

function fireTopup(Customer $customer, float $oldBalance, float $newBalance, string $reason = 'wallet_topup'): void
{
    $event = new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: $oldBalance,
        newBalance: $newBalance,
        reason: $reason,
        customerGroupId: $customer->customer_group_id,
    );

    app(WalletTopup::class)->handle($event);
}

// ── Task 3.1 feature tests ───────────────────────────────────────────────────

it('awards fixed points on topup when a matching rule exists', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makeFixedRule(50);

    fireTopup($customer, 0.0, 100.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('status', RewardPoint::STATUS_APPROVED)
            ->where('reward_points', 50)
            ->exists()
    )->toBeTrue();
});

it('awards percent points on topup when a matching rule exists', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makePercentRule(10); // 10% of 200 = 20 points

    fireTopup($customer, 0.0, 200.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('status', RewardPoint::STATUS_APPROVED)
            ->where('reward_points', 20)
            ->exists()
    )->toBeTrue();
});

it('does not award points when no rule matches', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    // No rules created

    fireTopup($customer, 0.0, 100.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)->count()
    )->toBe(0);
});

it('does not award points for admin_grant reason', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makeFixedRule(50);

    fireTopup($customer, 0.0, 100.0, 'admin_grant');

    expect(
        RewardPoint::where('customer_id', $customer->id)->count()
    )->toBe(0);
});

it('does not award points when percent rate produces zero points', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makePercentRule(0.5); // 0.5% of 1.0 = floor(0.005) = 0

    fireTopup($customer, 0.0, 1.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)->count()
    )->toBe(0);
});

it('does not award points when rewards module is disabled', function () {
    // Disable module; flush repository cache so the update is visible to core()->getConfigData()
    \Webkul\Core\Models\CoreConfig::where('code', 'reward.general.general.module-status')->update(['value' => '0']);
    \Illuminate\Support\Facades\Cache::flush();

    $customer = makeCustomerInGroup();
    makeFixedRule(50);

    fireTopup($customer, 0.0, 100.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)->count()
    )->toBe(0);
});

// ── Task 3.2 rule-selection tests ────────────────────────────────────────────

it('group-specific rule overrides global rule for matching customer', function () {
    enableRewardsModule();

    $group    = CustomerGroup::firstOrCreate(['code' => 'vip'], ['name' => 'VIP']);
    $customer = makeCustomerInGroup($group->id);

    makeFixedRule(10); // global
    makeFixedRule(100, ['customer_group_id' => $group->id]); // group-specific

    fireTopup($customer, 0.0, 100.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('reward_points', 100)
            ->exists()
    )->toBeTrue();
});

it('higher priority rule wins when both match the same customer', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();

    makeFixedRule(5,  ['priority' => 1]);
    makeFixedRule(99, ['priority' => 10]); // higher priority

    fireTopup($customer, 0.0, 100.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('reward_points', 99)
            ->exists()
    )->toBeTrue();
});

it('narrowest range wins as tie-breaker when priority is equal', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();

    // Wide range: 0-1000
    makeFixedRule(5, [
        'min_amount' => 0,
        'max_amount' => 1000,
        'priority'   => 5,
    ]);
    // Narrower range: 50-200
    makeFixedRule(20, [
        'min_amount' => 50,
        'max_amount' => 200,
        'priority'   => 5,
    ]);

    fireTopup($customer, 0.0, 100.0);

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('reward_points', 20)
            ->exists()
    )->toBeTrue();
});

// ── Wallet spend tests ────────────────────────────────────────────────────────

it('awards fixed points on wallet spend when a matching spend rule exists', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makeFixedRule(30, ['trigger' => 'wallet_spend']);

    fireTopup($customer, 100.0, 50.0, 'wallet_spend');

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('status', RewardPoint::STATUS_APPROVED)
            ->where('reward_points', 30)
            ->exists()
    )->toBeTrue();
});

it('awards percent points on wallet spend based on amount spent', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makePercentRule(10, ['trigger' => 'wallet_spend']); // 10% of 80 = 8 points

    fireTopup($customer, 100.0, 20.0, 'wallet_spend');

    expect(
        RewardPoint::where('customer_id', $customer->id)
            ->where('status', RewardPoint::STATUS_APPROVED)
            ->where('reward_points', 8)
            ->exists()
    )->toBeTrue();
});

it('wallet_topup rule does not fire on wallet_spend event', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makeFixedRule(50, ['trigger' => 'wallet_topup']); // only topup rule

    fireTopup($customer, 100.0, 50.0, 'wallet_spend');

    expect(
        RewardPoint::where('customer_id', $customer->id)->count()
    )->toBe(0);
});

it('wallet_spend rule does not fire on wallet_topup event', function () {
    enableRewardsModule();
    $customer = makeCustomerInGroup();
    makeFixedRule(50, ['trigger' => 'wallet_spend']); // only spend rule

    fireTopup($customer, 0.0, 100.0, 'wallet_topup');

    expect(
        RewardPoint::where('customer_id', $customer->id)->count()
    )->toBe(0);
});
