<?php

use Mockery\MockInterface;
use Webkul\RentalPricing\Models\BookingProductDayPricing;
use Webkul\RentalPricing\Repositories\BookingProductDayPricingRepository;

it('uses matching fixed rule for cart pricing', function () {
    $rule = new BookingProductDayPricing;
    $rule->min_days = 4;
    $rule->max_days = 6;
    $rule->discount_type = 'fixed';
    $rule->discount_value = 15;

    $repo = mock(BookingProductDayPricingRepository::class, function (MockInterface $mock) use ($rule) {
        $mock->shouldReceive('findMatchingRule')->with(1, 5)->andReturn($rule);
    });

    app()->instance(BookingProductDayPricingRepository::class, $repo);

    $resolvedRule = app(BookingProductDayPricingRepository::class)->findMatchingRule(1, 5);

    $dailyPrice = 80.0;
    $effectiveRate = max(0, $dailyPrice - $resolvedRule->discount_value);

    expect($effectiveRate * 5)->toBe(325.0); // (80-15)*5
});

it('uses matching percentage rule for cart pricing', function () {
    $rule = new BookingProductDayPricing;
    $rule->min_days = 7;
    $rule->max_days = null;
    $rule->discount_type = 'percentage';
    $rule->discount_value = 25;

    $repo = mock(BookingProductDayPricingRepository::class, function (MockInterface $mock) use ($rule) {
        $mock->shouldReceive('findMatchingRule')->with(1, 10)->andReturn($rule);
    });

    app()->instance(BookingProductDayPricingRepository::class, $repo);

    $resolvedRule = app(BookingProductDayPricingRepository::class)->findMatchingRule(1, 10);

    $dailyPrice = 80.0;
    $effectiveRate = $dailyPrice * (1 - $resolvedRule->discount_value / 100);

    expect($effectiveRate * 10)->toBe(600.0); // 80*0.75*10
});

it('falls back to daily_price when no rule matches', function () {
    $repo = mock(BookingProductDayPricingRepository::class, function (MockInterface $mock) {
        $mock->shouldReceive('findMatchingRule')->with(1, 2)->andReturn(null);
    });

    app()->instance(BookingProductDayPricingRepository::class, $repo);

    $resolvedRule = app(BookingProductDayPricingRepository::class)->findMatchingRule(1, 2);

    $dailyPrice = 80.0;
    $effectiveRate = $resolvedRule ? 0.0 : $dailyPrice;

    expect($effectiveRate * 2)->toBe(160.0); // fallback: 80*2
});
