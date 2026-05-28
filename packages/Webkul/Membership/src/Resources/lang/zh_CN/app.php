<?php

return [
    'shop' => [
        'customers' => [
            'profile' => [
                'membership-group' => '会员等级',
            ],
        ],
    ],

    'admin' => [
        'tiers' => [
            'acl-title'    => '会员',
            'menu-title'   => '会员',
            'title'        => '会员',
            'subtitle'     => '等级规则',
            'description'  => '根据客户钱包余额范围自动分配客户分组。',
            'drawer-title' => '管理等级规则',
            'add-tier'     => '添加等级',
            'save'         => '保存',
            'saved'        => '会员等级规则保存成功。',
            'no-rules'     => '尚未配置等级规则。',
            'select-group' => '— 选择客户分组 —',

            'col-sort-order'     => '优先级',
            'col-min-balance'    => '最低余额',
            'col-max-balance'    => '最高余额',
            'col-customer-group' => '客户分组',

            'validation' => [
                'min-balance-required' => '第 :n 条规则：最低余额为必填项且须 ≥ 0。',
                'max-balance-required' => '第 :n 条规则：最高余额为必填项且须 ≥ 0。',
                'min-exceeds-max'      => '第 :n 条规则：最低余额不能大于最高余额。',
                'group-required'       => '第 :n 条规则：客户分组为必填项。',
                'overlap'              => '第 :a 条与第 :b 条规则的余额范围存在重叠。',
            ],
        ],
    ],

];
