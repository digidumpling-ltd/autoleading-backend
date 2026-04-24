<?php

return [
    'admin' => [
        'customers' => [
            'wallet' => [
                'acl-title'             => 'Wallet',
                'title'                 => 'Customer Wallet',
                'balance'               => 'Current Balance',
                'adjust-title'          => 'Credit Adjustment',
                'type-add'              => 'Add Credit',
                'type-deduct'           => 'Deduct Credit',
                'amount'                => 'Amount',
                'reason'                => 'Reason',
                'adjust-submit'         => 'Apply',
                'adjust-add-success'    => 'Credit added successfully.',
                'adjust-deduct-success' => 'Credit deducted successfully.',
                'insufficient-balance'  => 'Insufficient balance.',
                'transactions'          => 'Transaction History',
                'no-transactions'       => 'No transactions yet.',
                'col-type'              => 'Type',
                'col-amount'            => 'Amount',
                'col-meta'              => 'Note',
                'col-date'              => 'Date',
                'view-wallet'           => 'View Wallet',
            ],
        ],
    ],

    'customers' => [
        'account' => [
            'wallet' => [
                'title'                    => 'My Wallet',
                'balance'                  => 'Current Balance',
                'topup'                    => 'Top Up',
                'topup-amount'             => 'Amount',
                'topup-amount-placeholder' => 'Enter amount',
                'topup-submit'             => 'Add Funds',
                'topup-success'            => 'Wallet topped up successfully.',
                'transactions'             => 'Transaction History',
                'no-transactions'          => 'No transactions yet.',
                'type'                     => 'Type',
                'amount'                   => 'Amount',
                'date'                     => 'Date',
                'type-deposit'             => 'Top Up',
                'type-withdraw'            => 'Payment',
            ],
        ],
    ],

    'listeners' => [
        'wallet-invoice' => [
            'insufficient-balance' => 'Insufficient wallet balance. Cannot create invoice.',
        ],
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'payment-methods' => [
                    'wallet'      => 'Wallet',
                    'wallet-info' => 'Pay using your wallet balance.',
                ],
            ],
        ],
    ],
];
