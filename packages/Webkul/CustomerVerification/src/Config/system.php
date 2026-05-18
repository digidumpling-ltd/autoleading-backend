<?php

return [
    [
        'key'  => 'customer_verification',
        'name' => 'customer-verification::app.configuration.title',
        'info' => 'customer-verification::app.configuration.title-info',
        'sort' => 8,
    ],
    [
        'key'  => 'customer_verification.checkout',
        'name' => 'customer-verification::app.configuration.checkout.title',
        'info' => 'customer-verification::app.configuration.checkout.title-info',
        'icon' => 'settings/settings.svg',
        'sort' => 1,
    ],
    [
        'key'    => 'customer_verification.checkout.gating',
        'name'   => 'customer-verification::app.configuration.checkout.title',
        'info'   => 'customer-verification::app.configuration.checkout.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'require_verification',
                'title'         => 'customer-verification::app.configuration.checkout.require-verification',
                'info'          => 'customer-verification::app.configuration.checkout.require-verification-info',
                'type'          => 'boolean',
                'default'       => 1,
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],

    /* Wallet top-up gating */
    [
        'key'  => 'customer_verification.wallet',
        'name' => 'customer-verification::app.configuration.wallet.title',
        'info' => 'customer-verification::app.configuration.wallet.title-info',
        'icon' => 'settings/settings.svg',
        'sort' => 2,
    ],
    [
        'key'    => 'customer_verification.wallet.settings',
        'name'   => 'customer-verification::app.configuration.wallet.title',
        'info'   => 'customer-verification::app.configuration.wallet.title-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'require_verification',
                'title'         => 'customer-verification::app.configuration.wallet.require-verification',
                'info'          => 'customer-verification::app.configuration.wallet.require-verification-info',
                'type'          => 'boolean',
                'default'       => 0,
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
