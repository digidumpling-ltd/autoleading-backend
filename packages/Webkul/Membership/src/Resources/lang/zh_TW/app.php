<?php

return [
    'shop' => [
        'customers' => [
            'profile' => [
                'membership-group' => '會員等級',
            ],
        ],
    ],

    'admin' => [
        'tiers' => [
            'acl-title'    => '會員',
            'menu-title'   => '會員',
            'title'        => '會員',
            'subtitle'     => '等級規則',
            'description'  => '根據客戶錢包餘額範圍自動分配客戶群組。',
            'drawer-title' => '管理等級規則',
            'add-tier'     => '新增等級',
            'save'         => '儲存',
            'saved'        => '會員等級規則儲存成功。',
            'no-rules'     => '尚未設定等級規則。',
            'select-group' => '— 選擇客戶群組 —',

            'col-sort-order'     => '優先順序',
            'col-min-balance'    => '最低餘額',
            'col-max-balance'    => '最高餘額',
            'col-customer-group' => '客戶群組',
            'col-background-color' => '背景顏色',
            'col-text-color'    => '文字顏色',

            'validation' => [
                'min-balance-required' => '第 :n 條規則：最低餘額為必填項且須 ≥ 0。',
                'max-balance-required' => '第 :n 條規則：最高餘額為必填項且須 ≥ 0。',
                'min-exceeds-max'      => '第 :n 條規則：最低餘額不能大於最高餘額。',
                'group-required'       => '第 :n 條規則：客戶群組為必填項。',
                'overlap'              => '第 :a 條與第 :b 條規則的餘額範圍存在重疊。',
            ],
        ],
    ],

];
