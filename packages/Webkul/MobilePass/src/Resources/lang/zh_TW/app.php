<?php

return [
    'common' => [
        'save-to-google-wallet' => '儲存至 Google 錢包',
        'view-on-google-wallet' => '在 Google 錢包中查看',
        'delete'                => '刪除',
    ],

    'customers' => [
        'account' => [
            'profile' => [
                'loyalty-pass' => '忠誠通行證',
            ],
        ],
    ],

    'admin' => [
        'customers' => [
            'pass-status-card' => [
                'title'           => '錢包通行證',
                'issued'          => '已簽發',
                'not-issued'      => '未簽發',
                'last-synced'     => '最後同步',
                'delete-confirm'  => '確定要刪除此錢包通行證嗎？客戶需要重新儲存。',
                'delete-success'  => '錢包通行證已成功刪除。',
            ],
        ],
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'mobile-pass' => [
                    'title'    => '行動通行證',
                    'info'     => '為客戶錢包帳戶設定 Google 錢包忠誠度通行證。',
                    'settings' => [
                        'title'   => '設定',
                        'info'    => '為客戶啟用或停用行動通行證功能。',
                        'enabled' => '啟用行動通行證',
                    ],
                    'google' => [
                        'title'               => 'Google 錢包',
                        'info'                => 'Google 錢包 API 憑證及忠誠度類別設定。',
                        'issuer-id'           => '發行方 ID',
                        'class-suffix'        => '忠誠度類別後綴',
                        'service-account-key' => '服務帳號金鑰（Base64 JSON）',
                    ],
                ],
            ],
        ],
    ],
];
