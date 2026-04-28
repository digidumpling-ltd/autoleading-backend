<?php

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
];
