<?php

return [
    'common' => [
        'save-btn'                            => '儲存',
        'edit-btn'                            => '編輯',
        'topup-requires-verification'         => '請先完成身份驗證，方可為錢包儲值。',
        'topup-requires-verification-link'    => '請先<a href=":dashboard_url" class="underline">完成身份驗證</a>，方可為錢包儲值。',
    ],

    'admin' => [
        'customers' => [
            'wallet' => [
                'acl-title'             => '錢包',
                'title'                 => '客戶錢包',
                'balance'               => '目前餘額',
                'adjust-title'          => '餘額調整',
                'type-add'              => '增加餘額',
                'type-deduct'           => '扣除餘額',
                'amount'                => '金額',
                'reason'                => '原因',
                'adjust-submit'         => '套用',
                'notify-customer'       => '通知客戶',
                'adjust-add-success'    => '餘額增加成功。',
                'adjust-deduct-success' => '餘額扣除成功。',
                'insufficient-balance'  => '餘額不足。',
                'transactions'          => '交易記錄',
                'no-transactions'       => '暫無交易記錄。',
                'col-type'              => '類型',
                'col-amount'            => '金額',
                'col-meta'              => '備註',
                'col-created-by'        => '建立人',
                'col-date'              => '日期',
                'creator-system'        => '系統',
                'creator-admin'         => '管理員',
                'creator-customer'      => '客戶（本人）',
                'balance-card' => [
                    'title'              => '錢包餘額',
                    'balance-label'      => '可用餘額',
                    'view-history'       => '查看記錄',
                    'update-btn'         => '更新',
                    'drawer-title'       => '調整錢包餘額',
                    'type-label'         => '類型',
                    'amount-placeholder' => '例如：10.00',
                    'reason-placeholder' => '輸入調整原因',
                    'error-type'         => '請選擇類型。',
                    'error-amount'       => '金額至少為 0.01。',
                    'error-reason'       => '原因至少需要 5 個字元。',
                ],
            ],
        ],
    ],

    'customers' => [
        'account' => [
            'wallet' => [
                'title'                    => '我的錢包',
                'balance'                  => '目前餘額',
                'topup'                    => '儲值',
                'topup-amount'             => '金額',
                'topup-amount-placeholder' => '輸入金額',
                'topup-select-method'         => '選擇付款方式',
                'topup-no-methods'            => '目前沒有可用的付款方式。',
                'topup-invalid-method'        => '所選付款方式無效。',
                'topup-submit'                => '新增資金',
                'topup-test-mode-notice'      => '測試模式已啟用。儲值將即時入帳，無需通過付款閘道。',
                'topup-test-mode-title'       => '測試模式',
                'topup-test-mode-description' => '略過付款閘道（僅限測試模式）。',
                'topup-success'            => '錢包儲值成功。',
                'topup-already-completed'  => '此次儲值已處理。',
                'transactions'             => '交易記錄',
                'no-transactions'          => '暫無交易記錄。',
                'type'                     => '類型',
                'amount'                   => '金額',
                'remarks'                  => '備註',
                'date'                     => '日期',
                'type-deposit'             => '儲值',
                'type-withdraw'            => '付款',
                'type-wallet_refund'       => '退款',
                'type-wallet_topup'        => '儲值',
            ],
        ],
    ],

    'checkout' => [
        'insufficient-balance-button' => '餘額不足 — 錢包儲值',
        'wallet-charge-note'          => '將扣除',
        'wallet-balance-label'        => '您的錢包餘額：',
        'insufficient-balance-hint'   => '您的錢包餘額不足以支付此訂單。',
        'insufficient-balance-server' => '錢包餘額不足。需要：:required，可用：:available。請為您的錢包儲值。',
    ],

    'listeners' => [
        'wallet-invoice' => [
            'insufficient-balance' => '錢包餘額不足，無法建立發票。',
            'description'          => '訂單 #:order 的付款',
        ],

        'wallet-refund' => [
            'description' => '訂單 #:order 的退款',
        ],

        'wallet-topup' => [
            'description' => '錢包儲值 #:order',
        ],
    ],

    'product' => [
        'wallet-credit-name' => '錢包積分',
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

        'wallet-reward' => [
            'subject'             => '【Auto Leading】積分獎勵兌換入帳通知',
            'dear'                => '親愛的 :customer_name',
            'greeting'            => '恭喜！您的獎勵積分已成功兌換為錢包餘額。',
            'body'                => '您兌換的獎勵積分已存入您的錢包帳戶，可於下次消費時直接使用。',
            'transaction-details' => '【兌換明細】',
            'member-account'      => '會員帳戶',
            'transaction-time'    => '兌換時間',
            'topup-amount'        => '存入金額',
            'footer'              => '如對此次兌換有任何疑問，歡迎隨時回覆本電郵，或與我們的客戶服務團隊聯絡。',
            'thanks'              => '再次感謝您的支持與愛護！',
            'closing'             => '祝您生活愉快！',
            'team'                => 'Auto Leading 團隊 敬上',
        ],
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'payment-methods' => [
                    'wallet'      => '錢包',
                    'wallet-info' => '使用錢包餘額付款。',
                ],

                'wallet' => [
                    'title'                           => '錢包',
                    'info'                            => '錢包儲值及積分設定。',
                    'topup-allowed-methods'           => '允許的儲值方式',
                    'topup-allowed-methods-info'      => '選擇客戶可在錢包儲值頁面使用的付款閘道。留空以允許所有已啟用的閘道。',

                    'settings' => [
                        'title'          => '儲值設定',
                        'info'           => '設定錢包儲值可用的付款閘道。',
                        'test-mode'      => '測試模式',
                        'test-mode-info' => '啟用後，客戶無需通過付款閘道即可為錢包儲值。僅限在開發或測試環境中使用。',
                    ],

                    'gating' => [
                        'title'                           => '儲值存取控制',
                        'info'                            => '控制客戶存取錢包儲值流程的條件。',
                        'require-topup-verification'      => '儲值需要實名認證',
                        'require-topup-verification-info' => '啟用後，只有已驗證的客戶才能存取錢包儲值流程。未驗證客戶將被導向至驗證頁面。',
                    ],

                    'notifications' => [
                        'title'                    => '電子郵件通知',
                        'info'                     => '設定發送給客戶的錢包電子郵件通知。',
                        'topup-email-enabled'      => '發送儲值確認郵件',
                        'topup-email-enabled-info' => '啟用後，客戶透過店面成功儲值後將收到一封確認郵件。',
                    ],

                    'events' => [
                        'title'                              => '錢包事件',
                        'info'                               => '設定錢包事件發佈。',
                        'publish-balance-updated'            => '發佈餘額更新事件',
                        'publish-balance-updated-info'       => '啟用後，每次儲值或扣款後都會觸發 WalletBalanceUpdated 事件。停用以阻止下游監聽器（如會員等級更新）執行。',
                    ],
                ],
            ],
        ],
    ],
];
