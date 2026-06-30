<?php

return [
    'common' => [
        'save-to-google-wallet' => '保存到 Google 钱包',
        'view-on-google-wallet' => '在 Google 钱包中查看',
        'save-to-apple-wallet'  => '添加到 Apple Wallet',
        'view-on-apple-wallet'  => '在 Apple Wallet 中查看',
        'delete'                => '删除',
    ],

    'customers' => [
        'account' => [
            'profile' => [
                'loyalty-pass'       => '忠诚通行证',
                'apple-loyalty-pass' => 'Apple 忠诚通行证',
            ],
        ],
    ],

    'admin' => [
        'customers' => [
            'pass-status-card' => [
                'title'           => '钱包通行证',
                'issued'          => '已签发',
                'not-issued'      => '未签发',
                'last-synced'     => '最后同步',
                'delete-confirm'  => '确定要删除此钱包通行证吗？客户需要重新保存。',
                'delete-success'  => '钱包通行证已成功删除。',
            ],
        ],
    ],

    'configuration' => [
        'index' => [
            'sales' => [
                'mobile-pass' => [
                    'title'    => '移动通行证',
                    'info'     => '为客户钱包账户配置 Google 钱包忠诚度通行证。',
                    'settings' => [
                        'title'   => '设置',
                        'info'    => '为客户启用或禁用移动通行证功能。',
                        'enabled' => '启用移动通行证',
                    ],
                    'google' => [
                        'title'               => 'Google 钱包',
                        'info'                => 'Google 钱包 API 凭证和忠诚度类配置。',
                        'issuer-id'           => '发行方 ID',
                        'class-suffix'        => '忠诚度类后缀',
                        'service-account-key' => '服务账号密钥（Base64 JSON）',
                    ],
                    'apple' => [
                        'title'                => 'Apple Wallet',
                        'info'                 => 'Apple Wallet 通行证凭证。请粘贴以 Base64 编码的 Pass Type ID 证书（.p12）。',
                        'organization-name'    => '机构名称',
                        'type-identifier'      => 'Pass Type 标识符',
                        'team-identifier'      => 'Team 标识符',
                        'certificate'          => '证书（.p12，Base64）',
                        'certificate-password' => '证书密码',
                    ],
                ],
            ],
        ],
    ],
];
