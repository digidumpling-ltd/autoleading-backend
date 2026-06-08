<?php

use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Membership\Listeners\AssignMembershipTier;
use Webkul\Membership\Models\TierRule;
use Webkul\Membership\Repositories\TierRuleRepository;
use Webkul\Wallet\Events\WalletBalanceUpdated;

function makeBalanceEvent(int $customerId, float $newBalance, float $oldBalance = 0.0): WalletBalanceUpdated
{
    return new WalletBalanceUpdated(
        customerId: $customerId,
        oldBalance: $oldBalance,
        newBalance: $newBalance,
        reason: 'wallet_topup',
    );
}

function fakeCustomer(int $id, int $groupId): object
{
    return (object) ['id' => $id, 'customer_group_id' => $groupId];
}

function fakeTier(int $groupId): TierRule
{
    $tier = new TierRule;
    $tier->customer_group_id = $groupId;

    return $tier;
}

function makeAssignListener(object $tierRepo, object $customerRepo): AssignMembershipTier
{
    return new AssignMembershipTier($tierRepo, $customerRepo);
}

it('assigns correct group when balance falls in a tier range', function () {
    $customer = fakeCustomer(id: 1, groupId: 10);
    $tier     = fakeTier(groupId: 20);

    $tierRepo     = Mockery::mock(TierRuleRepository::class);
    $customerRepo = Mockery::mock(CustomerRepository::class);

    $tierRepo->shouldReceive('findMatchingTier')->with(250.0)->andReturn($tier);
    $customerRepo->shouldReceive('find')->with(1)->andReturn($customer);
    $customerRepo->shouldReceive('update')->with(['customer_group_id' => 20], 1)->once();

    makeAssignListener($tierRepo, $customerRepo)->handle(makeBalanceEvent(1, 250.0));
});

it('leaves group unchanged when no tier matches the balance', function () {
    $tierRepo     = Mockery::mock(TierRuleRepository::class);
    $customerRepo = Mockery::mock(CustomerRepository::class);

    $tierRepo->shouldReceive('findMatchingTier')->with(1500.0)->andReturn(null);
    $customerRepo->shouldNotReceive('update');

    makeAssignListener($tierRepo, $customerRepo)->handle(makeBalanceEvent(1, 1500.0));
});

it('skips update when customer is already in the target group', function () {
    $customer = fakeCustomer(id: 1, groupId: 20);
    $tier     = fakeTier(groupId: 20);

    $tierRepo     = Mockery::mock(TierRuleRepository::class);
    $customerRepo = Mockery::mock(CustomerRepository::class);

    $tierRepo->shouldReceive('findMatchingTier')->andReturn($tier);
    $customerRepo->shouldReceive('find')->with(1)->andReturn($customer);
    $customerRepo->shouldNotReceive('update');

    makeAssignListener($tierRepo, $customerRepo)->handle(makeBalanceEvent(1, 200.0));
});

it('does nothing when customer is not found', function () {
    $tier = fakeTier(groupId: 20);

    $tierRepo     = Mockery::mock(TierRuleRepository::class);
    $customerRepo = Mockery::mock(CustomerRepository::class);

    $tierRepo->shouldReceive('findMatchingTier')->andReturn($tier);
    $customerRepo->shouldReceive('find')->with(99)->andReturn(null);
    $customerRepo->shouldNotReceive('update');

    makeAssignListener($tierRepo, $customerRepo)->handle(makeBalanceEvent(99, 200.0));
});

it('does nothing when no tier rules exist', function () {
    $tierRepo     = Mockery::mock(TierRuleRepository::class);
    $customerRepo = Mockery::mock(CustomerRepository::class);

    $tierRepo->shouldReceive('findMatchingTier')->andReturn(null);
    $customerRepo->shouldNotReceive('find');
    $customerRepo->shouldNotReceive('update');

    makeAssignListener($tierRepo, $customerRepo)->handle(makeBalanceEvent(1, 200.0));
});
