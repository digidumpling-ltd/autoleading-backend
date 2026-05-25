<?php

return [
    'admin' => [
        'day-pricing' => [
            'title'            => 'Daily Pricing Rules',
            'description'      => 'Set discount rules based on the number of rental days. Discounts are applied off the daily price.',
            'add-rule'         => 'Add Rule',
            'add-rules'        => 'Add Rules',
            'no-rules'         => 'No day-based discount rules configured.',
            'min-days'         => 'Min Days',
            'max-days'         => 'Max Days',
            'max-days-placeholder' => 'Leave blank for open-ended (e.g. 7+)',
            'discount-type'    => 'Discount Type',
            'discount-value'   => 'Discount Value',
            'fixed'            => 'Fixed (amount off)',
            'percentage'       => 'Percentage (% off)',
            'remove'           => 'Remove',
            'save'             => 'Save Rules',
            'saving'           => 'Saving…',
            'saved'            => 'Day pricing rules saved successfully.',
        ],
    ],

    'validation' => [
        'booking-product-not-found'  => 'Booking product not found.',
        'min-days-positive'          => 'Rule #:index: Min days must be at least 1.',
        'max-days-gte-min'           => 'Rule #:index: Max days must be greater than or equal to min days.',
        'invalid-discount-type'      => 'Rule #:index: Discount type must be "fixed" or "percentage".',
        'fixed-exceeds-daily-price'  => 'Rule #:index: Fixed discount must not exceed the daily price.',
        'percentage-out-of-range'    => 'Rule #:index: Percentage discount must be between 0 and 100.',
        'multiple-open-ended'        => 'At most one open-ended rule (no max days) is allowed per product.',
        'overlapping-ranges'         => 'Rules #:a and #:b have overlapping day ranges.',
    ],
];
