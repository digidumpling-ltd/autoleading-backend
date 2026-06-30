<?php

return [
    'common' => [
        'create' => 'Create',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'save' => 'Save',
        'back' => 'Back',
        'active' => 'Active',
        'inactive' => 'Inactive',
    ],

    'admin' => [
        'conditions' => [
            'topup-amount' => 'Top-Up Amount',
            'spend-amount' => 'Spend Amount',
            'is-first-topup' => 'Is First Top-Up',
            'rental-total' => 'Rental Total',
            'rental-total-days' => 'Rental Total Days',
            'is-first-booking' => 'Is First Booking',
        ],

        'wallet-rules' => [
            'menu-title' => 'Wallet Rules',
            'acl-title' => 'Wallet Rules',

            'index' => [
                'title' => 'Wallet Rules',
                'create-btn' => 'Create Wallet Rule',

                'datagrid' => [
                    'id' => 'ID',
                    'name' => 'Name',
                    'status' => 'Status',
                    'starts-from' => 'Starts From',
                    'ends-till' => 'Ends Till',
                    'sort-order' => 'Sort Order',
                ],
            ],

            'create' => [
                'title' => 'Create Wallet Rule',
                'create-success' => 'Wallet rule created successfully.',

                'general' => 'General',
                'name' => 'Name',
                'description' => 'Description',

                'conditions' => 'Conditions',
                'condition-type' => 'Conditions Match',
                'all' => 'ALL conditions must match',
                'any' => 'ANY condition must match',
                'attribute' => 'Attribute',
                'operator' => 'Operator',
                'value' => 'Value',
                'choose-condition' => '-- Choose Condition --',
                'add-condition' => 'Add Condition',

                'actions' => 'Actions',
                'action-type' => 'Action Type',
                'reward-mode' => 'Reward Mode',
                'reward-value' => 'Reward Value',
                'reward-value-fixed' => 'Fixed Amount',
                'reward-value-percent' => 'Percentage (%)',

                'action-types' => [
                    'reward_points' => 'Reward Points',
                    'wallet_credit' => 'Wallet Credit',
                ],

                'reward-modes' => [
                    'fixed' => 'Fixed',
                    'percentage' => 'Percentage',
                ],

                'settings' => 'Settings',
                'sort-order' => 'Sort Order',
                'channels' => 'Channels',
                'customer-groups' => 'Customer Groups',
                'status' => 'Status',

                'marketing-time' => 'Marketing Time',
                'starts-from' => 'Starts From',
                'ends-till' => 'Ends Till',

                'coupon' => 'Coupon',
                'coupon-type' => 'Coupon Type',
                'coupon-type-none' => 'No Coupon',
                'coupon-type-specific' => 'Specific Coupon Code',
                'coupon-code' => 'Coupon Code',
                'uses-per-coupon' => 'Uses Per Coupon',
                'usage-per-customer' => 'Usage Per Customer',
                'end-other-rules' => 'End Of Other Rules',
                'end-other-rules-yes' => 'Yes',
                'end-other-rules-no' => 'No',

                'operators' => [
                    '==' => 'Equals',
                    '!=' => 'Not Equals',
                    '>=' => 'Greater Than or Equal',
                    '<=' => 'Less Than or Equal',
                    '>' => 'Greater Than',
                    '<' => 'Less Than',
                ],

                'yes' => 'Yes',
                'no' => 'No',
            ],

            'edit' => [
                'title' => 'Edit Wallet Rule',
                'update-success' => 'Wallet rule updated successfully.',
            ],

            'delete-success' => 'Wallet rule deleted successfully.',
        ],

        'rental-rules' => [
            'menu-title' => 'Rental Rules',
            'acl-title' => 'Rental Rules',

            'index' => [
                'title' => 'Rental Rules',
                'create-btn' => 'Create Rental Rule',

                'datagrid' => [
                    'id' => 'ID',
                    'name' => 'Name',
                    'status' => 'Status',
                    'starts-from' => 'Starts From',
                    'ends-till' => 'Ends Till',
                    'sort-order' => 'Sort Order',
                ],
            ],

            'create' => [
                'title' => 'Create Rental Rule',
                'create-success' => 'Rental rule created successfully.',

                'general' => 'General',
                'name' => 'Name',
                'description' => 'Description',

                'conditions' => 'Conditions',
                'condition-type' => 'Conditions Match',
                'all' => 'ALL conditions must match',
                'any' => 'ANY condition must match',
                'attribute' => 'Attribute',
                'operator' => 'Operator',
                'value' => 'Value',
                'choose-condition' => '-- Choose Condition --',
                'add-condition' => 'Add Condition',

                'actions' => 'Actions',
                'action-type' => 'Action Type',
                'reward-mode' => 'Reward Mode',
                'reward-value' => 'Reward Value',
                'reward-value-fixed' => 'Fixed Amount',
                'reward-value-percent' => 'Percentage (%)',
                'product-id' => 'Reward Product ID',
                'extension-days' => 'Extension Days',
                'note-text' => 'Note / Description',

                'action-types' => [
                    'reward_points' => 'Reward Points',
                    'wallet_credit' => 'Wallet Credit',
                    'reward_product' => 'Reward Product / Note',
                    'free_extension' => 'Free Extension (Extra Days)',
                ],

                'reward-modes' => [
                    'fixed' => 'Fixed',
                    'percentage' => 'Percentage',
                ],

                'product-reward-modes' => [
                    'product' => 'Physical Product (add to order)',
                    'note' => 'Order Note (text description)',
                ],

                'settings' => 'Settings',
                'sort-order' => 'Sort Order',
                'channels' => 'Channels',
                'customer-groups' => 'Customer Groups',
                'status' => 'Status',

                'marketing-time' => 'Marketing Time',
                'starts-from' => 'Starts From',
                'ends-till' => 'Ends Till',

                'coupon' => 'Coupon',
                'coupon-type' => 'Coupon Type',
                'coupon-type-none' => 'No Coupon',
                'coupon-type-specific' => 'Specific Coupon Code',
                'coupon-code' => 'Coupon Code',
                'uses-per-coupon' => 'Uses Per Coupon',
                'usage-per-customer' => 'Usage Per Customer',
                'end-other-rules' => 'End Of Other Rules',
                'end-other-rules-yes' => 'Yes',
                'end-other-rules-no' => 'No',

                'operators' => [
                    '==' => 'Equals',
                    '!=' => 'Not Equals',
                    '>=' => 'Greater Than or Equal',
                    '<=' => 'Less Than or Equal',
                    '>' => 'Greater Than',
                    '<' => 'Less Than',
                ],

                'yes' => 'Yes',
                'no' => 'No',
            ],

            'edit' => [
                'title' => 'Edit Rental Rule',
                'update-success' => 'Rental rule updated successfully.',
            ],

            'delete-success' => 'Rental rule deleted successfully.',
        ],
    ],

    'shop' => [
        'coupon' => [
            'coupon-not-found' => 'The coupon code is invalid.',
            'coupon-usage-exceeded' => 'This coupon has reached its usage limit.',
            'coupon-per-customer-exceeded' => 'You have already used this coupon the maximum number of times.',
        ],

        'promo-card' => [
            'heading' => 'Special Offers for Your Booking',
            'free-extension-label' => ':days extra days at no cost',
            'reward-product-label' => ':name included',
            'reward-points-fixed' => ':points bonus points',
            'reward-points-percent' => ':percent% bonus points on your booking value',
            'wallet-credit-fixed' => ':amount wallet credit',
            'wallet-credit-percent' => ':percent% wallet credit on your booking value',
        ],
    ],

    'reward' => [
        'points-note' => 'Promotion reward: :rule',
        'wallet-credit-note' => 'Promotion reward: :rule',
    ],
];
