<?php

return [
    [
        'key'  => 'customer.verification',
        'name' => 'customer-verification::app.configuration.title',
        'info' => 'customer-verification::app.configuration.title-info',
        'icon' => 'settings/settings.svg',
        'sort' => 10,
    ],
    [
        'key'    => 'customer.verification.checkout',
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
    [
        'key'    => 'customer.verification.signup',
        'name'   => 'customer-verification::app.configuration.signup.title',
        'info'   => 'customer-verification::app.configuration.signup.title-info',
        'sort'   => 3,
        'fields' => [
            [
                'name'          => 'pdcs_url',
                'title'         => 'customer-verification::app.configuration.signup.pdcs-url',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => true,
            ],
            [
                'name'          => 'membership_tnc_url',
                'title'         => 'customer-verification::app.configuration.signup.membership-tnc-url',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => true,
            ],
        ],
    ],
];
