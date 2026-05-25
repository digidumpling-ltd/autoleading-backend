<?php

return [
    'admin' => [
        'day-pricing' => [
            'title'               => '每日定价规则',
            'description'         => '根据租赁天数设置折扣规则。折扣从每日价格中扣除。',
            'add-rule'            => '添加规则',
            'add-rules'           => '添加规则',
            'no-rules'            => '尚未配置按天折扣规则。',
            'min-days'            => '最少天数',
            'max-days'            => '最多天数',
            'max-days-placeholder' => '留空表示无上限（例如 7+）',
            'discount-type'       => '折扣类型',
            'discount-value'      => '折扣金额',
            'fixed'               => '固定金额（减去金额）',
            'percentage'          => '百分比（折扣%）',
            'remove'              => '删除',
            'save'                => '保存规则',
            'saving'              => '保存中…',
            'saved'               => '按天定价规则保存成功。',
        ],
    ],

    'validation' => [
        'booking-product-not-found' => '未找到预订产品。',
        'min-days-positive'         => '规则 #:index：最少天数必须至少为 1。',
        'max-days-gte-min'          => '规则 #:index：最多天数必须大于或等于最少天数。',
        'invalid-discount-type'     => '规则 #:index：折扣类型必须为"fixed"或"percentage"。',
        'fixed-exceeds-daily-price' => '规则 #:index：固定折扣不得超过每日价格。',
        'percentage-out-of-range'   => '规则 #:index：百分比折扣必须在 0 到 100 之间。',
        'multiple-open-ended'       => '每个产品最多只能有一条无上限规则（无最多天数）。',
        'overlapping-ranges'        => '规则 #:a 和 #:b 的天数范围重叠。',
    ],
];
