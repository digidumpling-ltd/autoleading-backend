<?php

use Webkul\Customer\Models\Customer;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Listeners\WalletPromotionListener;
use Webkul\CustomPromotions\Repositories\WalletPromotionRuleRepository;
use Webkul\CustomPromotions\Services\PromotionActionHandler;
use Webkul\Wallet\Events\WalletBalanceUpdated;

it('ignores non-topup-or-spend reasons', function () {
    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldNotReceive('getActiveRulesForCustomer');

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $handler = Mockery::mock(PromotionActionHandler::class);

    $listener = new WalletPromotionListener($ruleRepo, $handler, $customerRepo);

    $listener->handle(new WalletBalanceUpdated(1, 0, 100, 'refund'));
});

it('processes wallet_topup events and computes eventAmount', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rules = collect([]);

    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForCustomer')->once()->andReturn($rules);

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->with(1)->andReturn($customer);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldReceive('processRules')
        ->once()
        ->with($rules, $customer, ['topup_amount' => 1000.0, 'transaction_id' => null], ['eventAmount' => 1000.0]);

    $listener = new WalletPromotionListener($ruleRepo, $handler, $customerRepo);
    $listener->handle(new WalletBalanceUpdated(1, 0, 1000, 'wallet_topup'));
});

it('processes wallet_spend events using spend_amount key', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rules = collect([]);

    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForCustomer')->once()->andReturn($rules);

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->with(1)->andReturn($customer);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldReceive('processRules')
        ->once()
        ->with($rules, $customer, ['spend_amount' => 500.0, 'transaction_id' => null], ['eventAmount' => 500.0]);

    $listener = new WalletPromotionListener($ruleRepo, $handler, $customerRepo);
    $listener->handle(new WalletBalanceUpdated(1, 1000, 500, 'wallet_spend'));
});
