<?php

/**
 * Replicates the validation logic from DayPricingController::validateRules for isolated tests.
 */
function validateDayPricingRules(array $rules, float $dailyPrice = 100.0): array
{
    $errors = [];
    $openEndedCount = 0;
    $ranges = [];

    foreach ($rules as $i => $rule) {
        $minDays = (int) ($rule['min_days'] ?? 0);
        $maxDays = isset($rule['max_days']) && $rule['max_days'] !== '' && $rule['max_days'] !== null
            ? (int) $rule['max_days']
            : null;
        $discountType = $rule['discount_type'] ?? '';
        $discountValue = (float) ($rule['discount_value'] ?? 0);

        if ($minDays < 1) {
            $errors[] = "Rule #{$i}: min_days must be >= 1";
        }

        if ($maxDays !== null && $maxDays < $minDays) {
            $errors[] = "Rule #{$i}: max_days must be >= min_days";
        }

        if ($discountType === 'fixed' && $discountValue > $dailyPrice) {
            $errors[] = "Rule #{$i}: fixed discount exceeds daily_price";
        }

        if ($discountType === 'percentage' && ($discountValue < 0 || $discountValue > 100)) {
            $errors[] = "Rule #{$i}: percentage out of range";
        }

        if ($maxDays === null) {
            $openEndedCount++;
        }

        if ($openEndedCount > 1) {
            $errors[] = 'Multiple open-ended rules not allowed';
            break;
        }

        foreach ($ranges as $j => $existing) {
            $existingMax = $existing['max'] ?? PHP_INT_MAX;
            $currentMax = $maxDays ?? PHP_INT_MAX;

            if ($minDays <= $existingMax && $currentMax >= $existing['min']) {
                $errors[] = "Rules #{$j} and #{$i} overlap";
            }
        }

        $ranges[] = ['min' => $minDays, 'max' => $maxDays ?? PHP_INT_MAX];
    }

    return $errors;
}

it('accepts valid non-overlapping rules', function () {
    $rules = [
        ['min_days' => 1, 'max_days' => 3, 'discount_type' => 'fixed', 'discount_value' => 10],
        ['min_days' => 4, 'max_days' => 6, 'discount_type' => 'percentage', 'discount_value' => 20],
        ['min_days' => 7, 'max_days' => null, 'discount_type' => 'percentage', 'discount_value' => 30],
    ];

    expect(validateDayPricingRules($rules))->toBeEmpty();
});

it('rejects overlapping ranges', function () {
    $rules = [
        ['min_days' => 1, 'max_days' => 5, 'discount_type' => 'fixed', 'discount_value' => 5],
        ['min_days' => 4, 'max_days' => 8, 'discount_type' => 'fixed', 'discount_value' => 10],
    ];

    expect(validateDayPricingRules($rules))->not->toBeEmpty();
});

it('rejects multiple open-ended rules', function () {
    $rules = [
        ['min_days' => 1, 'max_days' => null, 'discount_type' => 'fixed', 'discount_value' => 5],
        ['min_days' => 5, 'max_days' => null, 'discount_type' => 'fixed', 'discount_value' => 10],
    ];

    expect(validateDayPricingRules($rules))->not->toBeEmpty();
});

it('rejects a fixed discount that exceeds daily_price', function () {
    $rules = [
        ['min_days' => 1, 'max_days' => 3, 'discount_type' => 'fixed', 'discount_value' => 150],
    ];

    $errors = validateDayPricingRules($rules, 100.0);

    expect($errors)->not->toBeEmpty();
});

it('rejects a percentage discount above 100', function () {
    $rules = [
        ['min_days' => 1, 'max_days' => 5, 'discount_type' => 'percentage', 'discount_value' => 110],
    ];

    expect(validateDayPricingRules($rules))->not->toBeEmpty();
});

it('accepts a single open-ended rule', function () {
    $rules = [
        ['min_days' => 7, 'max_days' => null, 'discount_type' => 'percentage', 'discount_value' => 25],
    ];

    expect(validateDayPricingRules($rules))->toBeEmpty();
});
