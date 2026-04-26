<?php

use Webkul\Yedpay\Payment\Yedpay;

return [
    'yedpay' => [
        'class'       => Yedpay::class,
        'code'        => 'yedpay',
        'title'       => 'Yedpay',
        'description' => 'Pay via Yedpay (Alipay, WeChat Pay, Credit Card and more).',
        'active'      => true,
        'sandbox'     => true,
        'api_key'     => '',
        'signing_key' => '',
        'sort'        => 2,
    ],
];
