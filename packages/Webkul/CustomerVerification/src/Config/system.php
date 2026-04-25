<?php

return [
    [
        'key'  => 'customer_verification',
        'name' => 'customer-verification::app.configuration.title',
        'info' => 'customer-verification::app.configuration.title-info',
        'sort' => 8,
    ], [
        'key'    => 'customer_verification.checkout',
        'name'   => 'customer-verification::app.configuration.checkout.title',
        'info'   => 'customer-verification::app.configuration.checkout.title-info',
        'icon'   => 'settings/settings.svg',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'require_verification_add_to_cart',
                'title'         => 'customer-verification::app.configuration.checkout.require-verification-add-to-cart',
                'info'          => 'customer-verification::app.configuration.checkout.require-verification-add-to-cart-info',
                'type'          => 'boolean',
                'default'       => 1,
                'channel_based' => false,
                'locale_based'  => false,
            ], [
                'name'          => 'require_verification_checkout',
                'title'         => 'customer-verification::app.configuration.checkout.require-verification-checkout',
                'info'          => 'customer-verification::app.configuration.checkout.require-verification-checkout-info',
                'type'          => 'boolean',
                'default'       => 1,
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
