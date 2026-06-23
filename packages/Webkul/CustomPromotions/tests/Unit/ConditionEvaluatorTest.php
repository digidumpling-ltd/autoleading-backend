<?php

use Webkul\CustomPromotions\Services\ConditionEvaluator;

beforeEach(function () {
    $this->evaluator = new ConditionEvaluator;
});

it('returns true when conditions are empty', function () {
    expect($this->evaluator->matches([], 1, []))->toBeTrue();
    expect($this->evaluator->matches([], 0, []))->toBeTrue();
});

it('evaluates equals operator correctly', function () {
    $conditions = [['attribute' => 'topup_amount', 'operator' => '==', 'value' => '100']];

    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 100]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 50]))->toBeFalse();
});

it('evaluates not-equals operator correctly', function () {
    $conditions = [['attribute' => 'topup_amount', 'operator' => '!=', 'value' => '100']];

    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 50]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 100]))->toBeFalse();
});

it('evaluates greater-than-or-equal operator correctly', function () {
    $conditions = [['attribute' => 'topup_amount', 'operator' => '>=', 'value' => '1000']];

    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 1000]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 1500]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 999]))->toBeFalse();
});

it('evaluates less-than-or-equal operator correctly', function () {
    $conditions = [['attribute' => 'rental_total_days', 'operator' => '<=', 'value' => '7']];

    expect($this->evaluator->matches($conditions, 1, ['rental_total_days' => 7]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['rental_total_days' => 3]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['rental_total_days' => 8]))->toBeFalse();
});

it('evaluates greater-than operator correctly', function () {
    $conditions = [['attribute' => 'topup_amount', 'operator' => '>', 'value' => '100']];

    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 101]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 100]))->toBeFalse();
});

it('evaluates less-than operator correctly', function () {
    $conditions = [['attribute' => 'topup_amount', 'operator' => '<', 'value' => '100']];

    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 99]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 100]))->toBeFalse();
});

it('uses ALL logic: all conditions must match', function () {
    $conditions = [
        ['attribute' => 'topup_amount', 'operator' => '>=', 'value' => '1000'],
        ['attribute' => 'is_first_topup', 'operator' => '==', 'value' => '1'],
    ];

    // Both match
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 1000, 'is_first_topup' => 1]))->toBeTrue();

    // Only one matches
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 999, 'is_first_topup' => 1]))->toBeFalse();
    expect($this->evaluator->matches($conditions, 1, ['topup_amount' => 1000, 'is_first_topup' => 0]))->toBeFalse();
});

it('uses ANY logic: at least one condition must match', function () {
    $conditions = [
        ['attribute' => 'topup_amount', 'operator' => '>=', 'value' => '1000'],
        ['attribute' => 'is_first_topup', 'operator' => '==', 'value' => '1'],
    ];

    // Both match
    expect($this->evaluator->matches($conditions, 0, ['topup_amount' => 1000, 'is_first_topup' => 1]))->toBeTrue();

    // One matches
    expect($this->evaluator->matches($conditions, 0, ['topup_amount' => 999, 'is_first_topup' => 1]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 0, ['topup_amount' => 1000, 'is_first_topup' => 0]))->toBeTrue();

    // None match
    expect($this->evaluator->matches($conditions, 0, ['topup_amount' => 999, 'is_first_topup' => 0]))->toBeFalse();
});

it('evaluates boolean attribute from eventData directly', function () {
    $conditions = [['attribute' => 'is_first_booking', 'operator' => '==', 'value' => '1']];

    expect($this->evaluator->matches($conditions, 1, ['is_first_booking' => 1]))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['is_first_booking' => 0]))->toBeFalse();
});

it('evaluates date comparison', function () {
    $conditions = [['attribute' => 'rental_start_date', 'operator' => '>=', 'value' => '2026-07-01']];

    expect($this->evaluator->matches($conditions, 1, ['rental_start_date' => '2026-07-01']))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['rental_start_date' => '2026-08-15']))->toBeTrue();
    expect($this->evaluator->matches($conditions, 1, ['rental_start_date' => '2026-06-30']))->toBeFalse();
});

it('returns wallet condition attributes', function () {
    $attrs = $this->evaluator->getWalletConditionAttributes();

    $codes = array_column($attrs, 'code');

    expect($codes)->toContain('topup_amount');
    expect($codes)->toContain('spend_amount');
    expect($codes)->toContain('is_first_topup');
});

it('returns rental condition attributes', function () {
    $attrs = $this->evaluator->getRentalConditionAttributes();

    $codes = array_column($attrs, 'code');

    expect($codes)->toContain('rental_total_days');
    expect($codes)->toContain('is_first_booking');
    expect($codes)->not->toContain('is_first_long_term');
});
