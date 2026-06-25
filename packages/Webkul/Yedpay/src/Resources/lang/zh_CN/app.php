<?php

return [
    'title'       => 'Yedpay',
    'description' => '通过 Yedpay 安全付款（支付宝、微信支付、信用卡及更多）。',

    'configuration' => [
        'title'                => 'Yedpay',
        'info'                 => '配置 Yedpay 支付网关（支付宝、微信支付、信用卡及更多）。',
        'api-key'              => 'API 密钥',
        'api-key-info'         => '来自 Yedpay 商户后台的生产环境 API 密钥。',
        'signing-key'          => '签名密钥',
        'signing-key-info'     => '用于验证支付回调的 HMAC 签名密钥。',
        'sandbox-api-key'      => '沙盒 API 密钥',
        'sandbox-api-key-info' => '来自 Yedpay 沙盒商户帐户的测试 API 密钥（与生产密钥不同）。',
    ],

    'response' => [
        'cart-not-found'      => '购物车不存在或无效。',
        'cart-processed'      => '此购物车已处理。',
        'topup-not-found'     => '充值请求不存在或已过期。',
        'payment-cancelled'   => '支付已取消。',
        'payment-failed'      => '支付失败。',
        'payment-success'     => '支付成功完成。',
        'provide-credentials' => '请在管理员设置中配置有效的 Yedpay 凭证。',
        'verification-failed' => '支付验证失败。',
    ],
];
