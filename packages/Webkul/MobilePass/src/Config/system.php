<?php

return [
    [
        'key' => 'sales.mobile_pass',
        'name' => 'mobile-pass::app.configuration.index.sales.mobile-pass.title',
        'info' => 'mobile-pass::app.configuration.index.sales.mobile-pass.info',
        'icon' => 'settings/payment-method.svg',
        'sort' => 99,
    ],
    [
        'key' => 'sales.mobile_pass.settings',
        'name' => 'mobile-pass::app.configuration.index.sales.mobile-pass.settings.title',
        'info' => 'mobile-pass::app.configuration.index.sales.mobile-pass.settings.info',
        'sort' => 1,
        'fields' => [
            [
                'name'          => 'enabled',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.settings.enabled',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => false,
                'default'       => false,
            ],
        ],
    ],
    [
        'key' => 'sales.mobile_pass.google',
        'name' => 'mobile-pass::app.configuration.index.sales.mobile-pass.google.title',
        'info' => 'mobile-pass::app.configuration.index.sales.mobile-pass.google.info',
        'sort' => 2,
        'fields' => [
            [
                'name'          => 'issuer_id',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.google.issuer-id',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'class_suffix',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.google.class-suffix',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
                'default'       => 'loyalty_class',
            ],
            [
                'name'          => 'service_account_key',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.google.service-account-key',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
