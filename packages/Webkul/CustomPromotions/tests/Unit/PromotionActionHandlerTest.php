<?php

use Illuminate\Support\Facades\DB;
use Mockery as m;
use Webkul\Customer\Models\Customer;
use Webkul\CustomPromotions\Services\ConditionEvaluator;
use Webkul\CustomPromotions\Services\PromotionActionHandler;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Rewards\Repositories\RewardPointRepository;
use Webkul\Wallet\Models\Customer as WalletCustomer;

function makeRule(string $actionType, ?string $rewardMode, float $rewardValue): stdClass
{
    $rule = new stdClass;
    $rule->id = 1;
    $rule->name = 'Test Rule';
    $rule->action_type = $actionType;
    $rule->reward_mode = $rewardMode;
    $rule->reward_value = $rewardValue;

    return $rule;
}

function makeHandler(): PromotionActionHandler
{
    return new PromotionActionHandler(
        m::mock(RewardPointRepository::class),
        m::mock(ProductRepository::class),
        m::mock(ConditionEvaluator::class),
    );
}

it('awards fixed reward points', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $rewardRepo->expects('awardPoints')->once()->with(m::any(), 50, m::type('string'));

    $handler = new PromotionActionHandler($rewardRepo, m::mock(ProductRepository::class), m::mock(ConditionEvaluator::class));
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('reward_points', 'fixed', 50), $customer, ['eventAmount' => 1000]);
});

it('awards percentage reward points using floor', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    // 1000 * 5 / 100 = 50 points
    $rewardRepo->expects('awardPoints')->once()->with(m::any(), 50, m::type('string'));

    $handler = new PromotionActionHandler($rewardRepo, m::mock(ProductRepository::class), m::mock(ConditionEvaluator::class));
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('reward_points', 'percentage', 5), $customer, ['eventAmount' => 1000]);
});

it('awards fixed wallet credit', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $productRepo = m::mock(ProductRepository::class);
    $evaluator = m::mock(ConditionEvaluator::class);

    $walletCustomer = m::mock(WalletCustomer::class)->makePartial();
    $walletCustomer->id = 1;
    $walletCustomer->customer_group_id = null;
    $walletCustomer->allows('balanceFloatNum')->andReturn(0.0);
    $walletCustomer->allows('fresh')->andReturnSelf();
    $walletCustomer->expects('depositFloat')->once()->with(25.0, m::type('array'));

    $handler = m::mock(PromotionActionHandler::class, [$rewardRepo, $productRepo, $evaluator])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
    $handler->allows('resolveWalletCustomer')->with(1)->andReturn($walletCustomer);

    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('wallet_credit', 'fixed', 25), $customer, ['eventAmount' => 1000]);
});

it('awards percentage wallet credit', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $productRepo = m::mock(ProductRepository::class);
    $evaluator = m::mock(ConditionEvaluator::class);

    $walletCustomer = m::mock(WalletCustomer::class)->makePartial();
    $walletCustomer->id = 1;
    $walletCustomer->customer_group_id = null;
    $walletCustomer->allows('balanceFloatNum')->andReturn(0.0);
    $walletCustomer->allows('fresh')->andReturnSelf();
    // 1000 * 15 / 100 = 150
    $walletCustomer->expects('depositFloat')->once()->with(150.0, m::type('array'));

    $handler = m::mock(PromotionActionHandler::class, [$rewardRepo, $productRepo, $evaluator])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
    $handler->allows('resolveWalletCustomer')->with(1)->andReturn($walletCustomer);

    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('wallet_credit', 'percentage', 15), $customer, ['eventAmount' => 1000]);
});

it('skips reward_product when order is missing', function () {
    $productRepo = m::mock(ProductRepository::class);
    $productRepo->shouldNotReceive('find');

    $handler = new PromotionActionHandler(m::mock(RewardPointRepository::class), $productRepo, m::mock(ConditionEvaluator::class));
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('reward_product', null, 999), $customer, []);
    expect(true)->toBeTrue();
});

it('extends booking by free_extension days', function () {
    $handler = new PromotionActionHandler(
        m::mock(RewardPointRepository::class),
        m::mock(ProductRepository::class),
        m::mock(ConditionEvaluator::class),
    );
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    DB::shouldReceive('table')->with('bookings')->once()->andReturn(
        m::mock()->shouldReceive('where')->once()->andReturn(
            m::mock()->shouldReceive('update')->once()->andReturn(1)->getMock()
        )->getMock()
    );

    $booking = m::mock();
    $booking->id = 10;
    $booking->to = strtotime('2026-07-14');

    $rule = (object) makeRule('free_extension', null, 16);
    $rule->id = 1;

    $handler->execute($rule, $customer, ['booking' => $booking]);
});

it('processRules skips rules when conditions do not match', function () {
    $evaluator = m::mock(ConditionEvaluator::class);
    $evaluator->shouldReceive('matches')->andReturn(false);

    $handler = m::mock(PromotionActionHandler::class, [
        m::mock(RewardPointRepository::class),
        m::mock(ProductRepository::class),
        $evaluator,
    ])->makePartial();
    $handler->shouldNotReceive('execute');

    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $rule = new stdClass;
    $rule->coupon_type = 0;
    $rule->conditions = [];
    $rule->condition_type = 1;
    $rule->end_other_rules = false;

    $handler->processRules(collect([$rule]), $customer, ['topup_amount' => 100], []);
});

it('processRules executes and stops on end_other_rules', function () {
    $evaluator = m::mock(ConditionEvaluator::class);
    $evaluator->shouldReceive('matches')->andReturn(true);

    $handler = m::mock(PromotionActionHandler::class, [
        m::mock(RewardPointRepository::class),
        m::mock(ProductRepository::class),
        $evaluator,
    ])->makePartial();

    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $rule1 = new stdClass;
    $rule1->coupon_type = 0;
    $rule1->conditions = [];
    $rule1->condition_type = 1;
    $rule1->end_other_rules = true;
    $rule1->action_type = 'reward_points';
    $rule1->reward_mode = 'fixed';
    $rule1->reward_value = 0;
    $rule1->name = 'Rule 1';

    $rule2 = new stdClass;
    $rule2->coupon_type = 0;
    $rule2->conditions = [];
    $rule2->condition_type = 1;
    $rule2->end_other_rules = false;
    $rule2->action_type = 'reward_points';
    $rule2->reward_mode = 'fixed';
    $rule2->reward_value = 0;
    $rule2->name = 'Rule 2';

    $handler->expects('execute')->once()->with($rule1, $customer, []);

    $handler->processRules(collect([$rule1, $rule2]), $customer, [], []);
});

it('processRules skips coupon rule when no session code', function () {
    $evaluator = m::mock(ConditionEvaluator::class);
    $evaluator->shouldNotReceive('matches');

    $handler = m::mock(PromotionActionHandler::class, [
        m::mock(RewardPointRepository::class),
        m::mock(ProductRepository::class),
        $evaluator,
    ])->makePartial();
    $handler->shouldNotReceive('execute');

    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $rule = new stdClass;
    $rule->coupon_type = 1;
    $rule->end_other_rules = false;

    session()->forget('custom_promo_coupon');

    $handler->processRules(collect([$rule]), $customer, [], []);
});
