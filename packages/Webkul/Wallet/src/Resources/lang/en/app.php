<?php

return [
    'common' => [
        'save-btn' => 'Save',
        'edit-btn' => 'Edit',
    ],

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
                'balance-card' => [
                    'title'              => 'Wallet Balance',
                    'balance-label'      => 'Available balance',
                    'view-history'       => 'View history',
                    'update-btn'         => 'Update',
                    'drawer-title'       => 'Adjust Wallet Balance',
                    'type-label'         => 'Type',
                    'amount-placeholder' => 'e.g. 10.00',
                    'reason-placeholder' => 'Enter reason for adjustment',
                    'error-type'         => 'Please select a type.',
                    'error-amount'       => 'Amount must be at least 0.01.',
                    'error-reason'       => 'Reason must be at least 5 characters.',
                ],
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

                    'events' => [
                        'title'                              => 'Wallet Events',
                        'info'                               => 'Configure wallet event publishing.',
                        'publish-balance-updated'            => 'Publish Balance Updated Event',
                        'publish-balance-updated-info'       => 'When enabled, a WalletBalanceUpdated event is dispatched after every top-up or deduction. Disable to prevent downstream listeners (e.g. membership tier updates) from running.',
                    ],
                ],
            ],
        ],
    ],
];
