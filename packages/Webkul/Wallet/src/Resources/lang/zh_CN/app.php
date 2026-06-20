<?php

return [
    'common' => [
        'save-btn'                            => '保存',
        'edit-btn'                            => '编辑',
        'topup-requires-verification'         => '請先完成身份驗證，方可為錢包充值。',
        'topup-requires-verification-link'    => '請先<a href=":dashboard_url" class="underline">完成身份驗證</a>，方可為錢包充值。',
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

    'mail' => [
        'topup-success' => [
            'subject'             => '【Auto Leading】網上增值確認',
            'dear'                => '親愛的 :customer_name',
            'greeting'            => '感謝您支持 Auto Leading 的服務！',
            'body'                => '我們已成功收到您的網上增值申請。相關金額及贈送金額將於 24 小時內存入您的會員帳戶。金額到帳後，您可以登入會員中心查看最新餘額。',
            'transaction-details' => '【增值明細】',
            'member-account'      => '會員帳戶',
            'transaction-time'    => '增值時間',
            'topup-amount'        => '本金增值金額',
            'footer'              => '如 24 小時後您的帳戶餘額未有更新，或對本次增值有任何查詢，歡迎隨時回覆本電郵，或與我們的客戶服務團隊聯絡。',
            'thanks'              => '再次感謝您的支持與愛護！',
            'closing'             => '祝您生活愉快！',
            'team'                => 'Auto Leading 團隊 敬上',
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
                        'title' => '充值设置',
                        'info'  => '配置钱包充值可用的支付网关。',
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
