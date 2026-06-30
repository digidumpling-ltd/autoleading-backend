<?php

use Webkul\BookingProduct\Models\Booking;
use Webkul\Customer\Models\Customer;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Listeners\RentalBookingCompleteListener;
use Webkul\CustomPromotions\Repositories\RentalPromotionRuleRepository;
use Webkul\CustomPromotions\Services\PromotionActionHandler;

function makeBookingWithOrder(int $customerId, int|string $from, int|string $to, float $grandTotal = 500.0): object
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
    $handler = Mockery::mock(PromotionActionHandler::class);

    $listener = new RentalBookingCompleteListener($ruleRepo, $handler, $customerRepo);

    $order = Mockery::mock();
    $order->customer_id = null;
    $order->grand_total = 0;

    $booking = Mockery::mock(Booking::class)->makePartial();
    $booking->order = $order;
    $booking->from = strtotime('2026-07-01');
    $booking->to = strtotime('2026-07-07');

    $listener->handle($booking);
});

it('computes rental_total_days from timestamps', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rules = collect([]);

    $ruleRepo = Mockery::mock(RentalPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForBooking')->once()->andReturn($rules);

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->andReturn($customer);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldReceive('processRules')->once();

    $listener = new RentalBookingCompleteListener($ruleRepo, $handler, $customerRepo);

    // 14 days in Unix timestamps
    $from = strtotime('2026-07-01');
    $to = strtotime('2026-07-15');
    $listener->handle(makeBookingWithOrder(1, $from, $to));
});

it('passes correct event context to processRules', function () {
    $customer = Mockery::mock(Customer::class)->makePartial();
    $customer->id = 1;
    $customer->customer_group_id = 1;

    $rules = collect([]);

    $ruleRepo = Mockery::mock(RentalPromotionRuleRepository::class);
    $ruleRepo->shouldReceive('getActiveRulesForBooking')->andReturn($rules);

    $customerRepo = Mockery::mock(CustomerRepository::class);
    $customerRepo->shouldReceive('find')->andReturn($customer);

    $handler = Mockery::mock(PromotionActionHandler::class);
    $handler->shouldReceive('processRules')
        ->once()
        ->withArgs(function ($r, $c, $eventData, $eventContext) {
            return $eventData['rental_total_days'] === 13
                && $eventData['rental_total'] === 500.0
                && isset($eventContext['booking'])
                && isset($eventContext['order']);
        });

    $listener = new RentalBookingCompleteListener($ruleRepo, $handler, $customerRepo);

    $from = strtotime('2026-07-01');
    $to = strtotime('2026-07-14');
    $listener->handle(makeBookingWithOrder(1, $from, $to));
});
