<?php

return [
    'common' => [
        'save-btn'                            => '保存',
        'edit-btn'                            => '编辑',
        'topup-requires-verification'         => '请先完成身份验证，方可为钱包充值。',
        'topup-requires-verification-link'    => '请先<a href=":dashboard_url" class="underline">完成身份验证</a>，方可为钱包充值。',
    ],

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
                'notify-customer'       => '通知客户',
                'adjust-add-success'    => '积分添加成功。',
                'adjust-deduct-success' => '积分扣除成功。',
                'insufficient-balance'  => '余额不足。',
                'transactions'          => '交易记录',
                'no-transactions'       => '暂无交易记录。',
                'col-type'              => '类型',
                'col-amount'            => '金额',
                'col-meta'              => '备注',
                'col-created-by'        => '创建人',
                'col-date'              => '日期',
                'creator-system'        => '系统',
                'creator-admin'         => '管理员',
                'creator-customer'      => '客户（本人）',
                'balance-card' => [
                    'title'              => '钱包余额',
                    'balance-label'      => '可用余额',
                    'view-history'       => '查看记录',
                    'update-btn'         => '更新',
                    'drawer-title'       => '调整钱包余额',
                    'type-label'         => '类型',
                    'amount-placeholder' => '例如：10.00',
                    'reason-placeholder' => '输入调整原因',
                    'error-type'         => '请选择类型。',
                    'error-amount'       => '金额至少为0.01。',
                    'error-reason'       => '原因至少需要5个字符。',
                ],
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
                'topup-select-method'         => '选择支付方式',
                'topup-no-methods'            => '当前没有可用的支付方式。',
                'topup-invalid-method'        => '所选支付方式无效。',
                'topup-submit'                => '添加资金',
                'topup-test-mode-notice'      => '测试模式已启用。充值将即时到账，无需通过支付网关。',
                'topup-test-mode-title'       => '测试模式',
                'topup-test-mode-description' => '绕过支付网关（仅限测试模式）。',
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

    'mail' => [
        'topup-success' => [
            'subject'             => '【Auto Leading】网上充值确认',
            'dear'                => '亲爱的 :customer_name',
            'greeting'            => '感谢您支持 Auto Leading 的服务！',
            'body'                => '我们已成功收到您的网上充值申请。相关金额及赠送金额将于 24 小时内存入您的会员账户。金额到账后，您可以登录会员中心查看最新余额。',
            'transaction-details' => '【充值明细】',
            'member-account'      => '会员账户',
            'transaction-time'    => '充值时间',
            'topup-amount'        => '充值金额',
            'footer'              => '如 24 小时后您的账户余额未有更新，或对本次充值有任何查询，欢迎随时回复本邮件，或与我们的客户服务团队联系。',
            'thanks'              => '再次感谢您的支持与关爱！',
            'closing'             => '祝您生活愉快！',
            'team'                => 'Auto Leading 团队 敬上',
        ],

        'wallet-reward' => [
            'subject'             => '【Auto Leading】积分奖励兑换入账通知',
            'dear'                => '亲爱的 :customer_name',
            'greeting'            => '恭喜！您的奖励积分已成功兑换为钱包余额。',
            'body'                => '您兑换的奖励积分已存入您的钱包账户，可于下次消费时直接使用。',
            'transaction-details' => '【兑换明细】',
            'member-account'      => '会员账户',
            'transaction-time'    => '兑换时间',
            'topup-amount'        => '存入金额',
            'footer'              => '如对此次兑换有任何疑问，欢迎随时回复本邮件，或与我们的客户服务团队联系。',
            'thanks'              => '再次感谢您的支持与关爱！',
            'closing'             => '祝您生活愉快！',
            'team'                => 'Auto Leading 团队 敬上',
        ],
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
                        'title'          => '充值设置',
                        'info'           => '配置钱包充值可用的支付网关。',
                        'test-mode'      => '测试模式',
                        'test-mode-info' => '启用后，客户无需通过支付网关即可为钱包充值。仅限在开发或测试环境中使用。',
                    ],

                    'gating' => [
                        'title'                           => '充值访问控制',
                        'info'                            => '控制客户访问钱包充值流程的条件。',
                        'require-topup-verification'      => '充值需要实名认证',
                        'require-topup-verification-info' => '启用后，只有已验证的客户才能访问钱包充值流程。未验证客户将被重定向至验证页面。',
                    ],

                    'notifications' => [
                        'title'                    => '邮件通知',
                        'info'                     => '配置发送给客户的钱包邮件通知。',
                        'topup-email-enabled'      => '发送充值确认邮件',
                        'topup-email-enabled-info' => '启用后，客户通过店面成功充值后将收到一封确认邮件。',
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
