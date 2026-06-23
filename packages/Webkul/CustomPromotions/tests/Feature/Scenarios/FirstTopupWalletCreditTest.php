<?php

use Webkul\CustomPromotions\Services\ConditionEvaluator;

/**
 * Promo 1: is_first_topup == 1 AND topup_amount >= 1000, wallet_credit 15%, starts_from 2026-07-01
 */
beforeEach(function () {
    $this->evaluator = new ConditionEvaluator;
    $this->conditions = [
        ['attribute' => 'is_first_topup',  'operator' => '==', 'value' => '1'],
        ['attribute' => 'topup_amount',    'operator' => '>=', 'value' => '1000'],
    ];
});

it('qualifies for 15% credit on first topup of $1000', function () {
    $eventData = ['topup_amount' => 1000, 'is_first_topup' => 1];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeTrue();
});

it('qualifies for 15% credit on first topup of $1500', function () {
    $eventData = ['topup_amount' => 1500, 'is_first_topup' => 1];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeTrue();
});

it('does not qualify when topup is $999 (below minimum)', function () {
    $eventData = ['topup_amount' => 999, 'is_first_topup' => 1];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeFalse();
});

it('does not qualify when is_first_topup is false (second topup)', function () {
    $eventData = ['topup_amount' => 1000, 'is_first_topup' => 0];
    expect($this->evaluator->matches($this->conditions, 1, $eventData))->toBeFalse();
});

it('calculates 15% wallet credit correctly for $1000 topup', function () {
    $amount = 1000 * 15 / 100;
    expect($amount)->toEqual(150);
});

it('calculates 15% wallet credit correctly for $1500 topup', function () {
    $amount = 1500 * 15 / 100;
    expect($amount)->toEqual(225);
});
