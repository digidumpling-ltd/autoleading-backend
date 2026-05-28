<?php

return [
    'admin' => [
        'customers' => [
            'wallet' => [
                'acl-title'             => '钱包',
                'title'                 => '客户钱包',
                'balance'               => '当前余额',
                'adjust-title'          => '积分调整',
                'type-add'              => '增加积分',
                'type-deduct'           => '扣除积分',
                'amount'                => '金额',
                'reason'                => '原因',
                'adjust-submit'         => '应用',
                'adjust-add-success'    => '积分添加成功。',
                'adjust-deduct-success' => '积分扣除成功。',
                'insufficient-balance'  => '余额不足。',
                'transactions'          => '交易记录',
                'no-transactions'       => '暂无交易记录。',
                'col-type'              => '类型',
                'col-amount'            => '金额',
                'col-meta'              => '备注',
                'col-date'              => '日期',
                'view-wallet'           => '查看钱包',
            ],
        ],
    ],

    'customers' => [
        'account' => [
            'wallet' => [
                'title'                    => '我的钱包',
                'balance'                  => '当前余额',
                'topup'                    => '充值',
                'topup-amount'             => '金额',
                'topup-amount-placeholder' => '输入金额',
                'topup-select-method'      => '选择支付方式',
                'topup-no-methods'         => '当前没有可用的支付方式。',
                'topup-invalid-method'     => '所选支付方式无效。',
                'topup-submit'             => '添加资金',
                'topup-success'            => '钱包充值成功。',
                'topup-already-completed'  => '此次充值已处理。',
                'transactions'             => '交易记录',
                'no-transactions'          => '暂无交易记录。',
                'type'                     => '类型',
                'amount'                   => '金额',
                'remarks'                  => '备注',
                'date'                     => '日期',
                'type-deposit'             => '充值',
                'type-withdraw'            => '付款',
                'type-wallet_refund'       => '退款',
                'type-wallet_topup'        => '充值',
            ],
        ],
    ],

    'checkout' => [
        'insufficient-balance-button' => '余额不足 — 钱包充值',
        'wallet-charge-note'          => '将扣除',
        'wallet-balance-label'        => '您的钱包余额：',
        'insufficient-balance-hint'   => '您的钱包余额不足以支付此订单。',
        'insufficient-balance-server' => '钱包余额不足。需要：:required，可用：:available。请充值您的钱包。',
    ],

    'listeners' => [
        'wallet-invoice' => [
            'insufficient-balance' => '钱包余额不足，无法创建发票。',
            'description'          => '订单 #:order 的付款',
        ],

        'wallet-refund' => [
            'description' => '订单 #:order 的退款',
        ],

        'wallet-topup' => [
            'description' => '钱包充值 #:order',
        ],
    ],

    'product' => [
        'wallet-credit-name' => '钱包积分',
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'payment-methods' => [
                    'wallet'      => '钱包',
                    'wallet-info' => '使用钱包余额付款。',
                ],

                'wallet' => [
                    'title'                           => '钱包',
                    'info'                            => '钱包充值和积分设置。',
                    'topup-allowed-methods'           => '允许的充值方式',
                    'topup-allowed-methods-info'      => '选择客户可在钱包充值页面使用的支付网关。留空以允许所有已启用的网关。',

                    'settings' => [
                        'title' => '充值设置',
                        'info'  => '配置钱包充值可用的支付网关。',
                    ],

                    'events' => [
                        'title'                              => '钱包事件',
                        'info'                               => '配置钱包事件发布。',
                        'publish-balance-updated'            => '发布余额更新事件',
                        'publish-balance-updated-info'       => '启用后，每次充值或扣款后都会触发 WalletBalanceUpdated 事件。禁用以阻止下游监听器（如会员等级更新）运行。',
                    ],
                ],
            ],
        ],
    ],
];
