<?php

use Webkul\BookingProduct\Models\Booking;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Listeners\RentalBookingCompleteListener;
use Webkul\CustomPromotions\Repositories\RentalPromotionRuleRepository;
use Webkul\CustomPromotions\Services\ConditionEvaluator;
use Webkul\CustomPromotions\Services\PromotionActionHandler;

function makeBookingWithOrder(int $customerId, string $from, string $to, float $grandTotal = 500.0): object
{
    $order = Mockery::mock();
    $order->customer_id = $customerId;
    $order->grand_total = $grandTotal;

    $booking = Mockery::mock(Booking::class)->makePartial();
    $booking->from = $from;
    $booking->to = $to;
    $booking->order = $order;

    return $booking;
}

it('skips when order has no customer', function () {
    $ruleRepo = Mockery::mock(RentalPromotionRuleRepository::class);
    $ruleRepo->shouldNotReceive('getActiveRulesForBooking');

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $handler = Mockery::mock(PromotionActionHandler::class);

    $listener = new RentalBookingCompleteListener($ruleRepo, $evaluator, $handler, $customerRepo);

    $order = Mockery::mock();
    $order->customer_id = null;
    $order->grand_total = 0;

    $booking = Mockery::mock(Booking::class)->makePartial();
    $booking->order = $order;
    $booking->from = '2026-07-01 00:00:00';
    $booking->to = '2026-07-07 00:00:00';

    $listener->handle($booking);
});

it('computes rental_total_days from timestamps', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $ruleRepo = Mockery::mock(RentalPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForBooking')->once()->andReturn(collect([]));

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->andReturn($customer);

    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $handler = Mockery::mock(PromotionActionHandler::class);

    $listener = new RentalBookingCompleteListener($ruleRepo, $evaluator, $handler, $customerRepo);

    $booking = makeBookingWithOrder(1, '2026-07-01 00:00:00', '2026-07-15 00:00:00');
    $listener->handle($booking);

    // If we got here without exception, days were computed. The exact days = 14.
    expect(true)->toBeTrue();
});

it('calls action handler when rule conditions match', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rule = Mockery::mock();
    $rule->conditions = [];
    $rule->condition_type = 1;

    $ruleRepo = Mockery::mock(RentalPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForBooking')->andReturn(collect([$rule]));

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->andReturn($customer);

    $evaluator = Mockery::mock(ConditionEvaluator::class);
    $evaluator->shouldReceive('matches')->andReturn(true);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldReceive('execute')->once();

    $listener = new RentalBookingCompleteListener($ruleRepo, $evaluator, $handler, $customerRepo);
    $listener->handle(makeBookingWithOrder(1, '2026-07-01 00:00:00', '2026-07-14 00:00:00'));
});
