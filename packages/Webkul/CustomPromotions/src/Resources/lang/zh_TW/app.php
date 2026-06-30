<?php

return [
    'common' => [
        'create'   => '建立',
        'edit'     => '編輯',
        'delete'   => '刪除',
        'save'     => '儲存',
        'back'     => '返回',
        'active'   => '已啟用',
        'inactive' => '已停用',
    ],

    'admin' => [
        'conditions' => [
            'topup-amount'      => '儲值金額',
            'spend-amount'      => '消費金額',
            'is-first-topup'    => '是否首次儲值',
            'rental-total'      => '租賃總金額',
            'rental-total-days' => '租賃總天數',
            'is-first-booking'  => '是否首次預訂',
        ],

        'wallet-rules' => [
            'menu-title' => '錢包規則',
            'acl-title'  => '錢包規則',

            'index' => [
                'title'      => '錢包規則',
                'create-btn' => '建立錢包規則',

                'datagrid' => [
                    'id'          => 'ID',
                    'name'        => '名稱',
                    'status'      => '狀態',
                    'starts-from' => '開始日期',
                    'ends-till'   => '結束日期',
                    'sort-order'  => '排序',
                ],
            ],

            'create' => [
                'title'          => '建立錢包規則',
                'create-success' => '錢包規則建立成功。',

                'general'     => '基本資訊',
                'name'        => '名稱',
                'description' => '描述',

                'conditions'       => '條件',
                'condition-type'   => '條件匹配',
                'all'              => '所有條件必須滿足',
                'any'              => '任意條件滿足即可',
                'attribute'        => '屬性',
                'operator'         => '運算符',
                'value'            => '值',
                'choose-condition' => '-- 選擇條件 --',
                'add-condition'    => '新增條件',

                'actions'              => '操作',
                'action-type'          => '操作類型',
                'reward-mode'          => '獎勵模式',
                'reward-value'         => '獎勵值',
                'reward-value-fixed'   => '固定金額',
                'reward-value-percent' => '百分比 (%)',

                'action-types' => [
                    'reward_points' => '獎勵積分',
                    'wallet_credit' => '錢包點數',
                ],

                'reward-modes' => [
                    'fixed'      => '固定',
                    'percentage' => '百分比',
                ],

                'settings'        => '設定',
                'sort-order'      => '排序',
                'channels'        => '頻道',
                'customer-groups' => '客戶群組',
                'status'          => '狀態',

                'marketing-time' => '行銷時間',
                'starts-from'    => '開始日期',
                'ends-till'      => '結束日期',

                'coupon'              => '優惠券',
                'coupon-type'         => '優惠券類型',
                'coupon-type-none'    => '無優惠券',
                'coupon-type-specific' => '指定優惠券碼',
                'coupon-code'         => '優惠券碼',
                'uses-per-coupon'     => '每券使用次數',
                'usage-per-customer'  => '每客戶使用次數',
                'end-other-rules'     => '結束其他規則',
                'end-other-rules-yes' => '是',
                'end-other-rules-no'  => '否',

                'operators' => [
                    '==' => '等於',
                    '!=' => '不等於',
                    '>=' => '大於或等於',
                    '<=' => '小於或等於',
                    '>'  => '大於',
                    '<'  => '小於',
                ],

                'yes' => '是',
                'no'  => '否',
            ],

            'edit' => [
                'title'          => '編輯錢包規則',
                'update-success' => '錢包規則更新成功。',
            ],

            'delete-success' => '錢包規則刪除成功。',
        ],

        'rental-rules' => [
            'menu-title' => '租賃規則',
            'acl-title'  => '租賃規則',

            'index' => [
                'title'      => '租賃規則',
                'create-btn' => '建立租賃規則',

                'datagrid' => [
                    'id'          => 'ID',
                    'name'        => '名稱',
                    'status'      => '狀態',
                    'starts-from' => '開始日期',
                    'ends-till'   => '結束日期',
                    'sort-order'  => '排序',
                ],
            ],

            'create' => [
                'title'          => '建立租賃規則',
                'create-success' => '租賃規則建立成功。',

                'general'     => '基本資訊',
                'name'        => '名稱',
                'description' => '描述',

                'conditions'       => '條件',
                'condition-type'   => '條件匹配',
                'all'              => '所有條件必須滿足',
                'any'              => '任意條件滿足即可',
                'attribute'        => '屬性',
                'operator'         => '運算符',
                'value'            => '值',
                'choose-condition' => '-- 選擇條件 --',
                'add-condition'    => '新增條件',

                'actions'              => '操作',
                'action-type'          => '操作類型',
                'reward-mode'          => '獎勵模式',
                'reward-value'         => '獎勵值',
                'reward-value-fixed'   => '固定金額',
                'reward-value-percent' => '百分比 (%)',
                'product-id'           => '獎勵產品 ID',
                'extension-days'       => '延期天數',
                'note-text'            => '備註 / 說明',

                'action-types' => [
                    'reward_points'  => '獎勵積分',
                    'wallet_credit'  => '錢包點數',
                    'reward_product' => '獎勵產品 / 備註',
                    'free_extension' => '免費延期（額外天數）',
                ],

                'reward-modes' => [
                    'fixed'      => '固定',
                    'percentage' => '百分比',
                ],

                'product-reward-modes' => [
                    'product' => '實體產品（加入訂單）',
                    'note'    => '訂單備註（文字說明）',
                ],

                'settings'        => '設定',
                'sort-order'      => '排序',
                'channels'        => '頻道',
                'customer-groups' => '客戶群組',
                'status'          => '狀態',

                'marketing-time' => '行銷時間',
                'starts-from'    => '開始日期',
                'ends-till'      => '結束日期',

                'coupon'              => '優惠券',
                'coupon-type'         => '優惠券類型',
                'coupon-type-none'    => '無優惠券',
                'coupon-type-specific' => '指定優惠券碼',
                'coupon-code'         => '優惠券碼',
                'uses-per-coupon'     => '每券使用次數',
                'usage-per-customer'  => '每客戶使用次數',
                'end-other-rules'     => '結束其他規則',
                'end-other-rules-yes' => '是',
                'end-other-rules-no'  => '否',

                'operators' => [
                    '==' => '等於',
                    '!=' => '不等於',
                    '>=' => '大於或等於',
                    '<=' => '小於或等於',
                    '>'  => '大於',
                    '<'  => '小於',
                ],

                'yes' => '是',
                'no'  => '否',
            ],

            'edit' => [
                'title'          => '編輯租賃規則',
                'update-success' => '租賃規則更新成功。',
            ],

            'delete-success' => '租賃規則刪除成功。',
        ],
    ],

    'shop' => [
        'coupon' => [
            'coupon-not-found'            => '優惠券碼無效。',
            'coupon-usage-exceeded'       => '此優惠券已達到使用上限。',
            'coupon-per-customer-exceeded' => '您已達到此優惠券的最大使用次數。',
        ],

        'promo-card' => [
            'heading'               => '您的預訂專屬優惠',
            'free-extension-label'  => '免費延期 :days 天',
            'reward-product-label'  => '贈送 :name',
            'reward-points-fixed'   => ':points 額外積分',
            'reward-points-percent' => '訂單金額的 :percent% 額外積分',
            'wallet-credit-fixed'   => ':amount 錢包點數',
            'wallet-credit-percent' => '訂單金額的 :percent% 錢包點數',
        ],
    ],

    'reward' => [
        'points-note'        => '促銷獎勵：:rule',
        'wallet-credit-note' => '促銷獎勵：:rule',
    ],
];
