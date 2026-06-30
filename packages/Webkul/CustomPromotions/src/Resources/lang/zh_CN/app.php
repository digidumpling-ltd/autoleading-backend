<?php

return [
    'common' => [
        'create' => '创建',
        'edit' => '编辑',
        'delete' => '删除',
        'save' => '保存',
        'back' => '返回',
        'active' => '活跃',
        'inactive' => '未激活',
    ],

    'admin' => [
        'conditions' => [
            'topup-amount' => '充值金额',
            'spend-amount' => '消费金额',
            'is-first-topup' => '是否首次充值',
            'rental-total' => '租赁总金额',
            'rental-total-days' => '租赁总天数',
            'is-first-booking' => '是否首次预订',
        ],

        'wallet-rules' => [
            'menu-title' => '钱包规则',
            'acl-title' => '钱包规则',

            'index' => [
                'title' => '钱包规则',
                'create-btn' => '创建钱包规则',

                'datagrid' => [
                    'id' => '编号',
                    'name' => '名称',
                    'status' => '状态',
                    'starts-from' => '开始日期',
                    'ends-till' => '结束日期',
                    'sort-order' => '排序',
                ],
            ],

            'create' => [
                'title' => '创建钱包规则',
                'create-success' => '钱包规则创建成功。',

                'general' => '基本信息',
                'name' => '名称',
                'description' => '描述',

                'conditions' => '条件',
                'condition-type' => '条件匹配',
                'all' => '所有条件必须满足',
                'any' => '任意条件满足即可',
                'attribute' => '属性',
                'operator' => '运算符',
                'value' => '值',
                'choose-condition' => '-- 选择条件 --',
                'add-condition' => '添加条件',

                'actions' => '操作',
                'action-type' => '操作类型',
                'reward-mode' => '奖励模式',
                'reward-value' => '奖励值',
                'reward-value-fixed' => '固定金额',
                'reward-value-percent' => '百分比 (%)',

                'action-types' => [
                    'reward_points' => '奖励积分',
                    'wallet_credit' => '钱包充值',
                ],

                'reward-modes' => [
                    'fixed' => '固定',
                    'percentage' => '百分比',
                ],

                'settings' => '设置',
                'sort-order' => '排序',
                'channels' => '渠道',
                'customer-groups' => '客户群组',
                'status' => '状态',

                'marketing-time' => '营销时间',
                'starts-from' => '开始日期',
                'ends-till' => '结束日期',

                'coupon' => '优惠券',
                'coupon-type' => '优惠券类型',
                'coupon-type-none' => '无优惠券',
                'coupon-type-specific' => '指定优惠券码',
                'coupon-code' => '优惠券码',
                'uses-per-coupon' => '每券使用次数',
                'usage-per-customer' => '每客户使用次数',
                'end-other-rules' => '结束其他规则',
                'end-other-rules-yes' => '是',
                'end-other-rules-no' => '否',

                'operators' => [
                    '==' => '等于',
                    '!=' => '不等于',
                    '>=' => '大于或等于',
                    '<=' => '小于或等于',
                    '>' => '大于',
                    '<' => '小于',
                ],

                'yes' => '是',
                'no' => '否',
            ],

            'edit' => [
                'title' => '编辑钱包规则',
                'update-success' => '钱包规则更新成功。',
            ],

            'delete-success' => '钱包规则删除成功。',
        ],

        'rental-rules' => [
            'menu-title' => '租赁规则',
            'acl-title' => '租赁规则',

            'index' => [
                'title' => '租赁规则',
                'create-btn' => '创建租赁规则',

                'datagrid' => [
                    'id' => '编号',
                    'name' => '名称',
                    'status' => '状态',
                    'starts-from' => '开始日期',
                    'ends-till' => '结束日期',
                    'sort-order' => '排序',
                ],
            ],

            'create' => [
                'title' => '创建租赁规则',
                'create-success' => '租赁规则创建成功。',

                'general' => '基本信息',
                'name' => '名称',
                'description' => '描述',

                'conditions' => '条件',
                'condition-type' => '条件匹配',
                'all' => '所有条件必须满足',
                'any' => '任意条件满足即可',
                'attribute' => '属性',
                'operator' => '运算符',
                'value' => '值',
                'choose-condition' => '-- 选择条件 --',
                'add-condition' => '添加条件',

                'actions' => '操作',
                'action-type' => '操作类型',
                'reward-mode' => '奖励模式',
                'reward-value' => '奖励值',
                'reward-value-fixed' => '固定金额',
                'reward-value-percent' => '百分比 (%)',
                'product-id' => '奖励产品ID',
                'extension-days' => '延期天数',
                'note-text' => '备注 / 说明',

                'action-types' => [
                    'reward_points' => '奖励积分',
                    'wallet_credit' => '钱包充值',
                    'reward_product' => '奖励产品 / 备注',
                    'free_extension' => '免费延期（额外天数）',
                ],

                'reward-modes' => [
                    'fixed' => '固定',
                    'percentage' => '百分比',
                ],

                'product-reward-modes' => [
                    'product' => '实体产品（加入订单）',
                    'note' => '订单备注（文字说明）',
                ],

                'settings' => '设置',
                'sort-order' => '排序',
                'channels' => '渠道',
                'customer-groups' => '客户群组',
                'status' => '状态',

                'marketing-time' => '营销时间',
                'starts-from' => '开始日期',
                'ends-till' => '结束日期',

                'coupon' => '优惠券',
                'coupon-type' => '优惠券类型',
                'coupon-type-none' => '无优惠券',
                'coupon-type-specific' => '指定优惠券码',
                'coupon-code' => '优惠券码',
                'uses-per-coupon' => '每券使用次数',
                'usage-per-customer' => '每客户使用次数',
                'end-other-rules' => '结束其他规则',
                'end-other-rules-yes' => '是',
                'end-other-rules-no' => '否',

                'operators' => [
                    '==' => '等于',
                    '!=' => '不等于',
                    '>=' => '大于或等于',
                    '<=' => '小于或等于',
                    '>' => '大于',
                    '<' => '小于',
                ],

                'yes' => '是',
                'no' => '否',
            ],

            'edit' => [
                'title' => '编辑租赁规则',
                'update-success' => '租赁规则更新成功。',
            ],

            'delete-success' => '租赁规则删除成功。',
        ],
    ],

    'shop' => [
        'coupon' => [
            'coupon-not-found' => '优惠券码无效。',
            'coupon-usage-exceeded' => '此优惠券已达到使用限额。',
            'coupon-per-customer-exceeded' => '您已达到此优惠券的最大使用次数。',
        ],

        'promo-card' => [
            'heading' => '您的预订专属优惠',
            'free-extension-label' => '免费延期 :days 天',
            'reward-product-label' => '赠送 :name',
            'reward-points-fixed' => ':points 额外积分',
            'reward-points-percent' => '订单金额的 :percent% 额外积分',
            'wallet-credit-fixed' => ':amount 钱包余额',
            'wallet-credit-percent' => '订单金额的 :percent% 钱包余额',
        ],
    ],

    'reward' => [
        'points-note' => '促销奖励：:rule',
        'wallet-credit-note' => '促销奖励：:rule',
    ],
];
