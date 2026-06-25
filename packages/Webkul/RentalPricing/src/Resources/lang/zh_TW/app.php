<?php

return [
    'admin' => [
        'day-pricing' => [
            'title'               => '每日定價規則',
            'description'         => '根據租賃天數設定折扣規則。折扣從每日價格中扣除。',
            'add-rule'            => '新增規則',
            'add-rules'           => '新增規則',
            'no-rules'            => '尚未設定按天折扣規則。',
            'min-days'            => '最少天數',
            'max-days'            => '最多天數',
            'max-days-placeholder' => '留空表示無上限（例如 7+）',
            'discount-type'       => '折扣類型',
            'discount-value'      => '折扣金額',
            'fixed'               => '固定金額（減去金額）',
            'percentage'          => '百分比（折扣%）',
            'remove'              => '刪除',
            'save'                => '儲存規則',
            'saving'              => '儲存中…',
            'saved'               => '按天定價規則儲存成功。',
        ],
    ],

    'validation' => [
        'booking-product-not-found' => '找不到預訂產品。',
        'min-days-positive'         => '規則 #:index：最少天數至少為 1。',
        'max-days-gte-min'          => '規則 #:index：最多天數必須大於或等於最少天數。',
        'invalid-discount-type'     => '規則 #:index：折扣類型必須為「fixed」或「percentage」。',
        'fixed-exceeds-daily-price' => '規則 #:index：固定折扣不得超過每日價格。',
        'percentage-out-of-range'   => '規則 #:index：百分比折扣必須在 0 到 100 之間。',
        'multiple-open-ended'       => '每個產品最多只能有一條無上限規則（無最多天數）。',
        'overlapping-ranges'        => '規則 #:a 和 #:b 的天數範圍重疊。',
    ],
];
