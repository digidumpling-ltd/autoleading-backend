<?php

return [
    'configuration' => [
        'catalog' => [
            'products' => [
                'whatsapp' => [
                    'title'  => 'WhatsApp',
                    'info'   => '配置WhatsApp咨询号码。',
                    'number' => 'WhatsApp号码',
                ],
                'rental' => [
                    'title'    => '租赁设置',
                    'info'     => '配置租赁预订限制。',
                    'max-days' => '最长租赁天数（0 = 无限制）',
                ],
            ],
        ],
    ],

    'products' => [
        'view' => [
            'type' => [
                'booking' => [
                    'rental' => [
                        'whatsapp-enquire' => '通过WhatsApp咨询',
                        'whatsapp-message' => '您好，我对 :name 感兴趣 - :url',
                    ],
                ],
            ],
        ],
    ],
];
