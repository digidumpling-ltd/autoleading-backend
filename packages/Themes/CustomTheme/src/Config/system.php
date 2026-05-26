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

    [
        'key'  => 'general.design.navbar',
        'name' => 'custom-theme::app.configuration.general.design.navbar.title',
        'info' => 'custom-theme::app.configuration.general.design.navbar.info',
        'sort' => 10,

        'fields' => [
            [
                'name'          => 'logo_max_height',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.logo-max-height',
                'type'          => 'number',
                'channel_based' => true,
                'locale_based'  => false,
                'default'       => 40,
            ],
            [
                'name'          => 'logo_fit',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.logo-fit',
                'type'          => 'select',
                'channel_based' => true,
                'locale_based'  => false,
                'default'       => 'contain',
                'options'       => [
                    ['title' => 'custom-theme::app.configuration.general.design.navbar.logo-fit-contain', 'value' => 'contain'],
                    ['title' => 'custom-theme::app.configuration.general.design.navbar.logo-fit-cover',   'value' => 'cover'],
                    ['title' => 'custom-theme::app.configuration.general.design.navbar.logo-fit-fill',    'value' => 'fill'],
                    ['title' => 'custom-theme::app.configuration.general.design.navbar.logo-fit-none',    'value' => 'none'],
                ],
            ],
            [
                'name'          => 'show_store_name',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.show-store-name',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'show_manual_menu',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.show-manual-menu',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'menu_items',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.menu-items',
                'type'          => 'blade',
                'path'          => 'custom-theme::configuration.navbar-menu-items',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'show_categories',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.show-categories',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
                'default'       => 1,
            ],
            [
                'name'          => 'categories_order',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.categories-order',
                'type'          => 'number',
                'channel_based' => true,
                'locale_based'  => false,
                'default'       => 0,
            ],
            [
                'name'          => 'show_categories_dropdown',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.show-categories-dropdown',
                'type'          => 'boolean',
                'channel_based' => true,
                'locale_based'  => false,
            ],
            [
                'name'          => 'categories_dropdown_label',
                'title'         => 'custom-theme::app.configuration.general.design.navbar.categories-dropdown-label',
                'type'          => 'text',
                'channel_based' => true,
                'locale_based'  => false,
                'default'       => 'Categories',
            ],
        ],
    ],
];
