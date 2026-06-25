<?php

return [
    'title'       => 'Yedpay',
    'description' => '透過 Yedpay 安全付款（支付寶、微信支付、信用卡及更多）。',

    'configuration' => [
        'title'                => 'Yedpay',
        'info'                 => '設定 Yedpay 付款閘道（支付寶、微信支付、信用卡及更多）。',
        'api-key'              => 'API 金鑰',
        'api-key-info'         => '來自 Yedpay 商戶後台的生產環境 API 金鑰。',
        'signing-key'          => '簽名金鑰',
        'signing-key-info'     => '用於驗證付款回調的 HMAC 簽名金鑰。',
        'sandbox-api-key'      => '沙盒 API 金鑰',
        'sandbox-api-key-info' => '來自 Yedpay 沙盒商戶帳戶的測試 API 金鑰（與生產金鑰不同）。',
    ],

    'response' => [
        'cart-not-found'      => '購物車不存在或無效。',
        'cart-processed'      => '此購物車已處理。',
        'topup-not-found'     => '儲值請求不存在或已過期。',
        'payment-cancelled'   => '付款已取消。',
        'payment-failed'      => '付款失敗。',
        'payment-success'     => '付款成功完成。',
        'provide-credentials' => '請在管理員設定中設定有效的 Yedpay 憑證。',
        'verification-failed' => '付款驗證失敗。',
    ],
];
