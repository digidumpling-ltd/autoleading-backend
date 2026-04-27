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
                'topup-select-method'      => 'Select Payment Method',
                'topup-no-methods'         => 'No payment methods are currently available.',
                'topup-invalid-method'     => 'The selected payment method is invalid.',
                'topup-submit'             => 'Add Funds',
                'topup-success'            => 'Wallet topped up successfully.',
                'topup-already-completed'  => 'This top-up has already been processed.',
                'transactions'             => 'Transaction History',
                'no-transactions'          => 'No transactions yet.',
                'type'                     => 'Type',
                'amount'                   => 'Amount',
                'remarks'                  => 'Remarks',
                'date'                     => 'Date',
                'type-deposit'             => 'Top Up',
                'type-withdraw'            => 'Payment',
                'type-wallet_refund'       => 'Refund',
                'type-wallet_topup'        => 'Top-Up',
            ],
        ],
    ],

    'checkout' => [
        'insufficient-balance-button' => 'Insufficient Balance — Top Up Wallet',
        'wallet-charge-note'          => 'You will be charged',
        'wallet-balance-label'        => 'Your wallet balance:',
        'insufficient-balance-hint'   => 'Your wallet balance is insufficient for this order.',
        'insufficient-balance-server' => 'Insufficient wallet balance. Required: :required, Available: :available. Please top up your wallet.',
    ],

    'listeners' => [
        'wallet-invoice' => [
            'insufficient-balance' => 'Insufficient wallet balance. Cannot create invoice.',
            'description'          => 'Payment for order #:order',
        ],

        'wallet-refund' => [
            'description' => 'Refund for order #:order',
        ],

        'wallet-topup' => [
            'description' => 'Wallet top-up #:order',
        ],
    ],

    'product' => [
        'wallet-credit-name' => 'Wallet Credit',
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'payment-methods' => [
                    'wallet'      => 'Wallet',
                    'wallet-info' => 'Pay using your wallet balance.',
                ],

                'wallet' => [
                    'title'                           => 'Wallet',
                    'info'                            => 'Wallet top-up and credit settings.',
                    'topup-allowed-methods'           => 'Allowed Top-Up Payment Methods',
                    'topup-allowed-methods-info'      => 'Select which payment gateways customers may use on the wallet top-up page. Leave empty to allow all enabled gateways.',

                    'settings' => [
                        'title' => 'Top-Up Settings',
                        'info'  => 'Configure which payment gateways are available for wallet top-ups.',
                    ],
                ],
            ],
        ],
    ],
];
