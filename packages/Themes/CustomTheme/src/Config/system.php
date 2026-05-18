<?php

return [
    [
        'key'  => 'catalog.products.whatsapp',
        'name' => 'custom-theme::app.configuration.catalog.products.whatsapp.title',
        'info' => 'custom-theme::app.configuration.catalog.products.whatsapp.info',
        'sort' => 99,

        'fields' => [
            [
                'name'          => 'number',
                'title'         => 'custom-theme::app.configuration.catalog.products.whatsapp.number',
                'type'          => 'text',
                'channel_based' => true,
                'locale_based'  => false,
            ],
        ],
    ],

    [
        'key'  => 'catalog.products.rental',
        'name' => 'custom-theme::app.configuration.catalog.products.rental.title',
        'info' => 'custom-theme::app.configuration.catalog.products.rental.info',
        'sort' => 100,

        'fields' => [
            [
                'name'          => 'max_days',
                'title'         => 'custom-theme::app.configuration.catalog.products.rental.max-days',
                'type'          => 'text',
                'channel_based' => false,
                'locale_based'  => false,
            ],
        ],
    ],
];
