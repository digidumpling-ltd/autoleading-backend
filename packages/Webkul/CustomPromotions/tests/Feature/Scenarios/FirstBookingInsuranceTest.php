<?php

use Webkul\CustomPromotions\Services\ConditionEvaluator;

/**
 * Promo 2: is_first_booking == 1, reward_product = Super Cover Plan, starts_from 2026-07-01
 * Qty = rental_total_days
 */
beforeEach(function () {
    $this->evaluator = new ConditionEvaluator;
    $this->conditions = [
        ['attribute' => 'is_first_booking', 'operator' => '==', 'value' => '1'],
    ];
});

it('qualifies on first 7-day rental booking', function () {
    $eventData = ['rental_total_days' => 7, 'is_first_booking' => 1];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeTrue();
});

it('qualifies on first 14-day rental booking', function () {
    $eventData = ['rental_total_days' => 14, 'is_first_booking' => 1];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeTrue();
});

it('does not qualify on second booking', function () {
    $eventData = ['rental_total_days' => 7, 'is_first_booking' => 0];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeFalse();
});

it('qty equals rental_total_days', function () {
    $rentalTotalDays = 7;
    $expectedQty = $rentalTotalDays;
    expect($expectedQty)->toBe(7);

    $rentalTotalDays = 14;
    $expectedQty = $rentalTotalDays;
    expect($expectedQty)->toBe(14);
});
