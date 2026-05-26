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
        'general' => [
            'design' => [
                'navbar' => [
                    'title'                     => '导航栏菜单',
                    'info'                      => '配置导航栏Logo、菜单项和分类。',
                    'logo-max-height'           => 'Logo最大高度（像素）',
                    'logo-fit'                  => 'Logo适配方式',
                    'logo-fit-contain'          => '适应（完整显示）',
                    'logo-fit-cover'            => '覆盖（填满裁切）',
                    'logo-fit-fill'             => '拉伸填充',
                    'logo-fit-none'             => '原始尺寸',
                    'show-store-name'           => '显示店铺名称',
                    'show-manual-menu'          => '显示菜单项',
                    'menu-items'                => '菜单项',
                    'menu-items-add'            => '添加项目',
                    'menu-items-item'           => '名称',
                    'menu-items-url'            => '链接（必须以 / 开头）',
                    'menu-items-order'          => '排序',
                    'show-categories'           => '显示分类',
                    'categories-order'          => '分类排序',
                    'show-categories-dropdown'  => '显示为下拉菜单',
                    'categories-dropdown-label' => '下拉菜单标签',
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
                        'pricing-tiers'    => '价格梯度',
                        'days'             => '天',
                    ],
                ],
            ],
        ],
    ],
];
