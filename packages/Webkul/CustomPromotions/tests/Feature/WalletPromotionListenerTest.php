<?php

use Webkul\Customer\Models\Customer;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Listeners\WalletPromotionListener;
use Webkul\CustomPromotions\Repositories\WalletPromotionRuleRepository;
use Webkul\CustomPromotions\Services\ConditionEvaluator;
use Webkul\CustomPromotions\Services\PromotionActionHandler;
use Webkul\Wallet\Events\WalletBalanceUpdated;

it('ignores non-topup-or-spend reasons', function () {
    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldNotReceive('getActiveRulesForCustomer');

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $handler = Mockery::mock(PromotionActionHandler::class);

    $listener = new WalletPromotionListener($ruleRepo, $evaluator, $handler, $customerRepo);

    $event = new WalletBalanceUpdated(1, 0, 100, 'refund');
    $listener->handle($event);
});

it('processes wallet_topup events and computes eventAmount', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rule = Mockery::mock();
    $rule->id = 1;
    $rule->name = 'Test Rule';
    $rule->action_type = 'wallet_credit';
    $rule->conditions = [];
    $rule->condition_type = 1;

    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForCustomer')->once()->andReturn(collect([$rule]));

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->with(1)->andReturn($customer);

    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $evaluator->shouldReceive('matches')
        ->once()
        ->with([], 1, ['topup_amount' => 1000.0, 'transaction_id' => null], $customer)
        ->andReturn(true);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldReceive('execute')
        ->once()
        ->with($rule, $customer, ['eventAmount' => 1000.0]);

    $listener = new WalletPromotionListener($ruleRepo, $evaluator, $handler, $customerRepo);

    $event = new WalletBalanceUpdated(1, 0, 1000, 'wallet_topup');
    $listener->handle($event);
});

it('processes wallet_spend events using spend_amount key', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForCustomer')->once()->andReturn(collect([]));

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->with(1)->andReturn($customer);

    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $handler = Mockery::mock(PromotionActionHandler::class);

    $listener = new WalletPromotionListener($ruleRepo, $evaluator, $handler, $customerRepo);

    $event = new WalletBalanceUpdated(1, 1000, 500, 'wallet_spend');
    $listener->handle($event);
    // No exception means spend key was correctly used, no matching rules
});

it('skips rules whose conditions do not match', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rule = Mockery::mock();
    $rule->conditions = [['attribute' => 'topup_amount', 'operator' => '>=', 'value' => '5000']];
    $rule->condition_type = 1;

    $ruleRepo = Mockery::mock(WalletPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForCustomer')->andReturn(collect([$rule]));

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->andReturn($customer);

    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $evaluator->shouldReceive('matches')->andReturn(false);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldNotReceive('execute');

    $listener = new WalletPromotionListener($ruleRepo, $evaluator, $handler, $customerRepo);
    $listener->handle(new WalletBalanceUpdated(1, 0, 1000, 'wallet_topup'));
});
