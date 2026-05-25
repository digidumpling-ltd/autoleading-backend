<?php

return [
    'configuration' => [
        'catalog' => [
            'products' => [
                'whatsapp' => [
                    'title'  => 'WhatsApp',
                    'info'   => 'Configure the WhatsApp enquiry number.',
                    'number' => 'WhatsApp Number',
                ],
                'rental' => [
                    'title'    => 'Rental',
                    'info'     => 'Configure rental booking limits.',
                    'max-days' => 'Max Rental Days (0 = no limit)',
                ],
            ],
        ],
    ],

    'products' => [
        'view' => [
            'type' => [
                'booking' => [
                    'rental' => [
                        'whatsapp-enquire' => 'Enquire on WhatsApp',
                        'whatsapp-message' => 'Hi, I\'m interested in :name - :url',
                        'pricing-tiers'    => 'Pricing Tiers',
                        'days'             => 'days',
                    ],
                ],
            ],
        ],
    ],
];
