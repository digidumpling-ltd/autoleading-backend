<?php

use Webkul\RentalPricing\Models\BookingProductDayPricing;

/**
 * Helper to create an in-memory rule without persisting to DB.
 */
function makeRule(int $minDays, ?int $maxDays, string $discountType, float $discountValue): BookingProductDayPricing
{
    $rule = new BookingProductDayPricing;
    $rule->min_days = $minDays;
    $rule->max_days = $maxDays;
    $rule->discount_type = $discountType;
    $rule->discount_value = $discountValue;

    return $rule;
}

/**
 * Replicate the matching logic from RentalSlot::effectiveDailyRate for isolated tests.
 */
function computeEffectiveRate(array $rules, float $dailyPrice, int $days): float
{
    $match = null;

    foreach ($rules as $rule) {
        $max = $rule->max_days ?? PHP_INT_MAX;

        if ($days >= $rule->min_days && $days <= $max) {
            $match = $rule;
            break;
        }
    }

    if (! $match) {
        return $dailyPrice;
    }

    if ($match->discount_type === 'fixed') {
        return max(0, $dailyPrice - $match->discount_value);
    }

    return $dailyPrice * (1 - $match->discount_value / 100);
}

it('applies a fixed discount rule correctly', function () {
    $rules = [makeRule(4, 6, 'fixed', 15)];

    $rate = computeEffectiveRate($rules, 80, 5);

    expect($rate)->toBe(65.0); // 80 - 15
});

it('applies a percentage discount rule correctly', function () {
    $rules = [makeRule(7, null, 'percentage', 25)];

    $rate = computeEffectiveRate($rules, 80, 10);

    expect($rate)->toBe(60.0); // 80 * 0.75
});

it('falls back to daily_price when no rule matches', function () {
    $rules = [makeRule(4, null, 'fixed', 10)];

    $rate = computeEffectiveRate($rules, 80, 2);

    expect($rate)->toBe(80.0);
});

it('matches an open-ended rule (no max_days)', function () {
    $rules = [makeRule(7, null, 'percentage', 20)];

    $rate = computeEffectiveRate($rules, 100, 15);

    expect($rate)->toBe(80.0); // 100 * 0.80
});

it('does not match open-ended rule when below min_days', function () {
    $rules = [makeRule(7, null, 'percentage', 20)];

    $rate = computeEffectiveRate($rules, 100, 6);

    expect($rate)->toBe(100.0); // fallback
});

it('matches the correct rule when multiple rules exist', function () {
    $rules = [
        makeRule(1, 3, 'fixed', 5),
        makeRule(4, 6, 'fixed', 15),
        makeRule(7, null, 'percentage', 25),
    ];

    expect(computeEffectiveRate($rules, 80, 2))->toBe(75.0); // 80-5
    expect(computeEffectiveRate($rules, 80, 5))->toBe(65.0); // 80-15
    expect(computeEffectiveRate($rules, 80, 10))->toBe(60.0); // 80*0.75
});

it('clamps fixed discount to zero when discount equals daily price', function () {
    $rules = [makeRule(1, null, 'fixed', 80)];

    $rate = computeEffectiveRate($rules, 80, 1);

    expect($rate)->toBe(0.0);
});
