<?php

use Illuminate\Support\Facades\DB;
use Mockery as m;
use Webkul\Customer\Models\Customer;
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

it('awards fixed reward points', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $rewardRepo->expects('awardPoints')->once()->with(
        m::any(),
        50,
        m::type('string')
    );

    $productRepo = m::mock(ProductRepository::class);

    $handler = new PromotionActionHandler($rewardRepo, $productRepo);
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('reward_points', 'fixed', 50), $customer, ['eventAmount' => 1000]);
});

it('awards percentage reward points using floor', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    // 1000 * 5 / 100 = 50 points
    $rewardRepo->expects('awardPoints')->once()->with(m::any(), 50, m::type('string'));

    $productRepo = m::mock(ProductRepository::class);

    $handler = new PromotionActionHandler($rewardRepo, $productRepo);
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('reward_points', 'percentage', 5), $customer, ['eventAmount' => 1000]);
});

it('awards fixed wallet credit', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $productRepo = m::mock(ProductRepository::class);

    $walletCustomer = m::mock(WalletCustomer::class)->makePartial();
    $walletCustomer->id = 1;
    $walletCustomer->customer_group_id = null;
    $walletCustomer->allows('balanceFloatNum')->andReturn(0.0);
    $walletCustomer->allows('fresh')->andReturnSelf();
    $walletCustomer->expects('depositFloat')->once()->with(25.0, m::type('array'));

    $handler = m::mock(PromotionActionHandler::class, [$rewardRepo, $productRepo])
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

    $walletCustomer = m::mock(WalletCustomer::class)->makePartial();
    $walletCustomer->id = 1;
    $walletCustomer->customer_group_id = null;
    $walletCustomer->allows('balanceFloatNum')->andReturn(0.0);
    $walletCustomer->allows('fresh')->andReturnSelf();
    // 1000 * 15 / 100 = 150
    $walletCustomer->expects('depositFloat')->once()->with(150.0, m::type('array'));

    $handler = m::mock(PromotionActionHandler::class, [$rewardRepo, $productRepo])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();
    $handler->allows('resolveWalletCustomer')->with(1)->andReturn($walletCustomer);

    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $handler->execute((object) makeRule('wallet_credit', 'percentage', 15), $customer, ['eventAmount' => 1000]);
});

it('skips reward_product when order is missing', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $productRepo = m::mock(ProductRepository::class);

    $handler = new PromotionActionHandler($rewardRepo, $productRepo);
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    $productRepo->shouldNotReceive('find');
    $handler->execute((object) makeRule('reward_product', null, 999), $customer, []);
    expect(true)->toBeTrue();
});

it('extends booking by free_extension days', function () {
    $rewardRepo = m::mock(RewardPointRepository::class);
    $productRepo = m::mock(ProductRepository::class);

    $handler = new PromotionActionHandler($rewardRepo, $productRepo);
    $customer = m::mock(Customer::class)->makePartial();
    $customer->id = 1;

    DB::shouldReceive('table')->with('bookings')->once()->andReturn(
        m::mock()->shouldReceive('where')->once()->andReturn(
            m::mock()->shouldReceive('update')->once()->andReturn(1)->getMock()
        )->getMock()
    );

    $booking = m::mock();
    $booking->id = 10;
    $booking->to = '2026-07-14 00:00:00';

    $rule = (object) makeRule('free_extension', null, 16);
    $rule->id = 1;

    $handler->execute($rule, $customer, ['booking' => $booking]);
});
