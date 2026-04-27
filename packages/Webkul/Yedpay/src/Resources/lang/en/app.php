<?php

return [
    'title'       => 'Yedpay',
    'description' => 'Pay securely via Yedpay (Alipay, WeChat Pay, Credit Card and more).',

    'configuration' => [
        'title'            => 'Yedpay',
        'info'             => 'Configure Yedpay payment gateway (Alipay, WeChat Pay, Credit Card and more).',
        'api-key'          => 'API Key',
        'api-key-info'     => 'Production API key from your Yedpay merchant dashboard.',
        'signing-key'          => 'Signing Key',
        'signing-key-info'     => 'HMAC signing key used to verify payment callbacks.',
        'sandbox-api-key'      => 'Sandbox API Key',
        'sandbox-api-key-info' => 'Staging API key from your Yedpay sandbox merchant account (different from the production key).',
    ],

    'response' => [
        'cart-not-found'       => 'Cart not found or invalid.',
        'cart-processed'       => 'This cart has already been processed.',
        'topup-not-found'      => 'Top-up request not found or expired.',
        'payment-cancelled'    => 'Payment was cancelled.',
        'payment-failed'       => 'Payment failed.',
        'payment-success'      => 'Payment completed successfully.',
        'provide-credentials'  => 'Please configure valid Yedpay credentials in admin settings.',
        'verification-failed'  => 'Payment verification failed.',
    ],
];
