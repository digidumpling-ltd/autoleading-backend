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
        'general' => [
            'design' => [
                'navbar' => [
                    'title'                     => 'Navbar Menu',
                    'info'                      => 'Configure the navigation bar logo, menu items, and categories.',
                    'logo-max-height'           => 'Logo Max Height (px)',
                    'logo-fit'                  => 'Logo Fit',
                    'logo-fit-contain'          => 'Contain',
                    'logo-fit-cover'            => 'Cover',
                    'logo-fit-fill'             => 'Fill',
                    'logo-fit-none'             => 'None (original size)',
                    'show-store-name'           => 'Show Store Name',
                    'show-manual-menu'          => 'Show Menu Items',
                    'menu-items'                => 'Menu Items',
                    'menu-items-add'            => 'Add Item',
                    'menu-items-item'           => 'Item',
                    'menu-items-url'            => 'URL (must start with /)',
                    'menu-items-order'          => 'Order',
                    'show-categories'           => 'Show Categories',
                    'categories-order'          => 'Categories Order',
                    'show-categories-dropdown'  => 'Show as Dropdown',
                    'categories-dropdown-label' => 'Dropdown Label',
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
