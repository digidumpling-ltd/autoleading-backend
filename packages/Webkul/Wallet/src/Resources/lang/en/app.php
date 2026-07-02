<?php

return [
    'common' => [
        'save-btn'                            => 'Save',
        'edit-btn'                            => 'Edit',
        'topup-requires-verification'         => 'Please complete identity verification before topping up your wallet.',
        'topup-requires-verification-link'    => 'Please <a href=":dashboard_url" class="underline">complete identity verification</a> before topping up your wallet.',
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
                'notify-customer'       => 'Notify Customer',
                'adjust-add-success'    => 'Credit added successfully.',
                'adjust-deduct-success' => 'Credit deducted successfully.',
                'insufficient-balance'  => 'Insufficient balance.',
                'transactions'          => 'Transaction History',
                'no-transactions'       => 'No transactions yet.',
                'col-type'              => 'Type',
                'col-amount'            => 'Amount',
                'col-meta'              => 'Note',
                'col-created-by'        => 'Created By',
                'col-date'              => 'Date',
                'creator-system'        => 'System',
                'creator-admin'         => 'Admin',
                'creator-customer'      => 'Customer (Self)',
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
                'topup-select-method'       => 'Select Payment Method',
                'topup-no-methods'          => 'No payment methods are currently available.',
                'topup-invalid-method'      => 'The selected payment method is invalid.',
                'topup-submit'              => 'Add Funds',
                'topup-test-mode-notice'    => 'Test mode is active. Top-ups are credited instantly without going through a payment gateway.',
                'topup-test-mode-title'     => 'Test Mode',
                'topup-test-mode-description' => 'Bypass payment gateway (test mode only).',
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

    'mail' => [
        'topup-success' => [
            'subject'             => '[Auto Leading] Online Top-Up Confirmation',
            'dear'                => 'Dear :customer_name',
            'greeting'            => 'Thank you for choosing and supporting Auto Leading!',
            'body'                => 'We have successfully received your online top-up request. The reloaded amount and any eligible bonus credit will be credited to your membership account within 1 to 24 hours. Once the balance has been updated, you can log in to the Member Center to check your latest account statement.',
            'transaction-details' => 'Transaction Details',
            'member-account'      => 'Member Account',
            'transaction-time'    => 'Transaction Time',
            'topup-amount'        => 'Top-Up Amount',
            'footer'              => 'If your account balance is not updated after 24 hours, or if you have any questions regarding this transaction, please feel free to reply directly to this email or contact our customer service team.',
            'thanks'              => 'Thank you once again for your continued support!',
            'closing'             => 'Best regards,',
            'team'                => 'The Auto Leading Team',
        ],

        'wallet-reward' => [
            'subject'             => '[Auto Leading] Wallet Promotion Reward Credited',
            'dear'                => 'Dear :customer_name',
            'greeting'            => 'Great news! You have received a promotion reward.',
            'body'                => 'A wallet credit has been added to your account as part of a promotion reward. The amount is now available in your wallet balance.',
            'transaction-details' => 'Reward Details',
            'member-account'      => 'Member Account',
            'transaction-time'    => 'Credited At',
            'topup-amount'        => 'Reward Amount',
            'footer'              => 'If you have any questions regarding this reward, please contact our customer service team.',
            'thanks'              => 'Thank you for being a valued member!',
            'closing'             => 'Best regards,',
            'team'                => 'The Auto Leading Team',
        ],
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
                        'title'          => 'Top-Up Settings',
                        'info'           => 'Configure which payment gateways are available for wallet top-ups.',
                        'test-mode'      => 'Test Mode',
                        'test-mode-info' => 'When enabled, customers can top up their wallet without going through a payment gateway. Use only in development or staging environments.',
                    ],

                    'gating' => [
                        'title'                           => 'Top-Up Gating',
                        'info'                            => 'Control access to the wallet top-up flow.',
                        'require-topup-verification'      => 'Require Verification for Top-Up',
                        'require-topup-verification-info' => 'When enabled, only verified customers can access the wallet top-up flow. Unverified customers will be redirected to the verification page.',
                    ],

                    'notifications' => [
                        'title'                    => 'Email Notifications',
                        'info'                     => 'Configure wallet email notifications sent to customers.',
                        'topup-email-enabled'      => 'Send Top-Up Confirmation Email',
                        'topup-email-enabled-info' => 'When enabled, customers receive an email confirmation after their wallet is successfully topped up via the storefront.',
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
