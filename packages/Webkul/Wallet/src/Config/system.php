<?php

$topupOptions = collect(config('payment_methods', []))
    ->filter(function ($method) {
        if (($method['code'] ?? '') === 'wallet') {
            return false;
        }

        return is_a($method['class'] ?? '', \Webkul\Wallet\Contracts\SupportsWalletTopUp::class, true);
    })
    ->map(fn ($method) => ['title' => $method['title'], 'value' => $method['code']])
    ->values()
    ->toArray();

return [
    [
        'key'    => 'sales.payment_methods.wallet',
        'name'   => 'bagisto-wallet::app.configuration.index.sales.payment-methods.wallet',
        'info'   => 'bagisto-wallet::app.configuration.index.sales.payment-methods.wallet-info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'title',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.title',
                'type'          => 'text',
                'validation'    => 'required',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'description',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.description',
                'type'          => 'textarea',
                'channel_based' => false,
                'locale_based'  => true,
            ], [
                'name'          => 'active',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.status',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'          => 'image',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.logo',
                'type'          => 'image',
                'info'          => 'admin::app.configuration.index.sales.payment-methods.logo-information',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => false,
                'validation'    => 'mimes:bmp,jpeg,jpg,png,webp',
            ], [
                'name'          => 'generate_invoice',
                'title'         => 'admin::app.configuration.index.sales.payment-methods.generate-invoice',
                'type'          => 'boolean',
                'depends'       => 'active:1',
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'    => 'invoice_status',
                'title'   => 'admin::app.configuration.index.sales.payment-methods.set-invoice-status',
                'type'    => 'select',
                'depends' => 'active:1',
                'options' => [
                    [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.pending',
                        'value' => 'pending',
                    ], [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.paid',
                        'value' => 'paid',
                    ],
                ],
                'channel_based' => true,
                'locale_based'  => false,
            ], [
                'name'    => 'order_status',
                'title'   => 'admin::app.configuration.index.sales.payment-methods.set-order-status',
                'type'    => 'select',
                'depends' => 'active:1',
                'options' => [
                    [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.pending',
                        'value' => 'pending',
                    ], [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.pending-payment',
                        'value' => 'pending_payment',
                    ], [
                        'title' => 'admin::app.configuration.index.sales.payment-methods.processing',
                        'value' => 'processing',
                    ],
                ],
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ],
    [
        'key'  => 'sales.wallet',
        'name' => 'bagisto-wallet::app.configuration.index.sales.wallet.title',
        'info' => 'bagisto-wallet::app.configuration.index.sales.wallet.info',
        'sort' => 99,
    ],
    [
        'key'    => 'sales.wallet.settings',
        'name'   => 'bagisto-wallet::app.configuration.index.sales.wallet.settings.title',
        'info'   => 'bagisto-wallet::app.configuration.index.sales.wallet.settings.info',
        'sort'   => 1,
        'fields' => [
            [
                'name'          => 'topup_allowed_methods',
                'title'         => 'bagisto-wallet::app.configuration.index.sales.wallet.topup-allowed-methods',
                'info'          => 'bagisto-wallet::app.configuration.index.sales.wallet.topup-allowed-methods-info',
                'type'          => 'multiselect',
                'options'       => $topupOptions,
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ],
    [
        'key'    => 'sales.wallet.events',
        'name'   => 'bagisto-wallet::app.configuration.index.sales.wallet.events.title',
        'info'   => 'bagisto-wallet::app.configuration.index.sales.wallet.events.info',
        'sort'   => 2,
        'fields' => [
            [
                'name'          => 'publish_balance_updated',
                'title'         => 'bagisto-wallet::app.configuration.index.sales.wallet.events.publish-balance-updated',
                'info'          => 'bagisto-wallet::app.configuration.index.sales.wallet.events.publish-balance-updated-info',
                'type'          => 'boolean',
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
