<?php

return [
    'common' => [
        'save-to-google-wallet' => 'Save to Google Wallet',
        'view-on-google-wallet' => 'View on Google Wallet',
        'delete'                => 'Delete',
    ],

    'customers' => [
        'account' => [
            'profile' => [
                'loyalty-pass' => 'Loyalty Pass',
            ],
        ],
    ],

    'admin' => [
        'customers' => [
            'pass-status-card' => [
                'title'           => 'Wallet Pass',
                'issued'          => 'Issued',
                'not-issued'      => 'Not Issued',
                'last-synced'     => 'Last synced',
                'delete-confirm'  => 'Are you sure you want to delete this wallet pass? The customer will need to save a new one.',
                'delete-success'  => 'Wallet pass deleted successfully.',
            ],
        ],
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'mobile-pass' => [
                    'title'    => 'Mobile Pass',
                    'info'     => 'Configure Google Wallet loyalty passes for customer wallet accounts.',
                    'settings' => [
                        'title'   => 'Settings',
                        'info'    => 'Enable or disable the mobile pass feature for customers.',
                        'enabled' => 'Enable Mobile Pass',
                    ],
                    'google' => [
                        'title'               => 'Google Wallet',
                        'info'                => 'Google Wallet API credentials and loyalty class configuration.',
                        'issuer-id'           => 'Issuer ID',
                        'class-suffix'        => 'Loyalty Class Suffix',
                        'service-account-key' => 'Service Account Key (Base64 JSON)',
                    ],
                ],
            ],
        ],
    ],
];
