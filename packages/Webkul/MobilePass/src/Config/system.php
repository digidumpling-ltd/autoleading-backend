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
    [
        'key' => 'sales.mobile_pass.apple',
        'name' => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.title',
        'info' => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.info',
        'sort' => 3,
        'fields' => [
            [
                'name'          => 'organization_name',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.organization-name',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'type_identifier',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.type-identifier',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'team_identifier',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.team-identifier',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'certificate',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.certificate',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => false,
            ],
            [
                'name'          => 'certificate_password',
                'title'         => 'mobile-pass::app.configuration.index.sales.mobile-pass.apple.certificate-password',
                'type'          => 'password',
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
