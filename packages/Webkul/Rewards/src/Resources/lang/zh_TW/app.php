<?php

return [
    'common' => [
        'save-btn'        => '儲存',
        'edit-btn'        => '編輯',
        'notify-customer' => '通知客戶',
    ],

    'admin' => [
        'components' => [
            'layouts' => [
                'sidebar' => [
                    'redemptions-settings'                       => '兌換設定',
                    'reward-point-on-attribute'                  => '屬性獎勵點數',
                    'reward-point-on-cart'                       => '購物車獎勵點數',
                    'reward-point-on-category-for-specific-time' => '特定時間的分類獎勵點數',
                    'reward-point-on-category'                   => '分類獎勵點數',
                    'reward-point-on-product-for-specific-time'  => '特定時間的產品獎勵點數',
                    'reward-point-on-product'                    => '產品獎勵點數',
                    'reward-point'                               => '獎勵點數',
                    'reward-system-details'                      => '獎勵系統詳情',
                    'wallet-topup-reward-rules'                  => '錢包獎勵積分',
                ],
            ],
        ],

        'configuration' => [
            'title' => [
                'general' => [
                    'info'    => '設定追蹤客戶活動並以點數獎勵客戶以便未來享受折扣或優惠的方案。',
                    'setting' => '設定',
                    'title'   => '獎勵點數',

                    'reward' => [
                        'setting' => [
                            'delete-success'                         => ':name 刪除成功。',
                            'email-notification'                     => '電子郵件通知',
                            'reward-used-at-one-time'                => '一次可使用的獎勵點數',
                            'reward-when-customer-dob-exp-days'      => '客戶生日獎勵點數在幾天後到期',
                            'reward-when-customer-dob'               => '客戶生日獎勵',
                            'reward-when-customer-register-exp-days' => '客戶註冊獎勵點數在幾天後到期',
                            'reward-when-customer-register'          => '客戶註冊獎勵',
                            'reward-when-product-reviewed-exp-days'  => '產品評價獎勵點數在幾天後到期',
                            'reward-when-product-reviewed'           => '產品評價獎勵',
                            'status'                                 => '模組狀態',
                            'update-success'                         => ':name 更新成功。',
                        ],
                    ],
                ],
            ],
        ],

        'rewards' => [
            'products' => [
                'index' => [
                    'add-btn'        => '建立產品獎勵',
                    'create-success' => '產品獎勵建立成功',
                    'delete-failed'  => '產品獎勵刪除失敗！',
                    'delete-success' => '產品獎勵刪除成功',
                    'error-product'  => '未選擇產品',
                    'title'          => '產品獎勵點數',
                    'update-success' => '產品獎勵更新成功',

                    'datagrid' => [
                        'delete'              => '刪除',
                        'edit'                => '編輯',
                        'end-date'            => '結束日期',
                        'id'                  => 'ID',
                        'mass-delete-success' => '產品獎勵刪除成功',
                        'mass-update-success' => '產品獎勵更新成功',
                        'name'                => '名稱',
                        'reward-points'       => '獎勵積分',
                        'sku'                 => 'SKU',
                        'start-date'          => '開始日期',
                        'status'              => '狀態',
                        'update-status'       => '更新',

                        'options' => [
                            'active'   => '已啟用',
                            'inactive' => '已停用',
                        ],
                    ],

                    'create' => [
                        'active'              => '已啟用',
                        'create-btn'          => '建立特定產品獎勵',
                        'end-date'            => '結束日期',
                        'enter-reward-points' => '輸入獎勵積分',
                        'inactive'            => '已停用',
                        'save-btn'            => '儲存產品獎勵',
                        'select-product'      => '選擇產品',
                        'select-status'       => '選擇狀態',
                        'start-date'          => '開始日期',
                        'status'              => '狀態',
                        'title'               => '產品獎勵點數',
                    ],

                    'edit' => [
                        'active'              => '已啟用',
                        'create-btn'          => '建立特定產品獎勵',
                        'end-date'            => '結束日期',
                        'enter-reward-points' => '輸入獎勵積分',
                        'inactive'            => '已停用',
                        'save-btn'            => '更新產品獎勵',
                        'select-product'      => '選擇產品',
                        'select-status'       => '選擇狀態',
                        'start-date'          => '開始日期',
                        'status'              => '狀態',
                        'title'               => '產品獎勵點數',
                    ],
                ],
            ],

            'products-specific' => [
                'index' => [
                    'add-btn' => '建立產品獎勵',
                    'title'   => '特定時間的產品獎勵點數',

                    'create' => [
                        'create-btn' => '建立時間特定獎勵',
                        'save-btn'   => '儲存時間特定產品獎勵',
                        'title'      => '新增時間特定獎勵',
                    ],

                    'edit' => [
                        'save-btn' => '更新時間特定獎勵',
                        'title'    => '編輯時間特定獎勵',
                    ],
                ],
            ],

            'category' => [
                'index' => [
                    'add-btn'                => '建立分類獎勵',
                    'category-specific-time' => '分類特定時間',
                    'create-success'         => '分類獎勵建立成功',
                    'delete-failed'          => '分類獎勵刪除失敗！',
                    'delete-success'         => '分類獎勵刪除成功',
                    'error-category'         => '未選擇分類',
                    'specific-title'         => '建立分類特定獎勵',
                    'title'                  => '分類獎勵點數',
                    'update-success'         => '分類獎勵更新成功',

                    'datagrid' => [
                        'delete'              => '刪除',
                        'edit'                => '編輯',
                        'end-date'            => '結束日期',
                        'id'                  => 'ID',
                        'mass-delete-success' => '分類獎勵刪除成功',
                        'mass-update-success' => '分類獎勵更新成功',
                        'name'                => '名稱',
                        'reward-points'       => '獎勵積分',
                        'sku'                 => 'SKU',
                        'start-date'          => '開始日期',
                        'status'              => '狀態',
                        'update-status'       => '更新',

                        'options' => [
                            'active'   => '已啟用',
                            'inactive' => '已停用',
                        ],
                    ],
                ],

                'create' => [
                    'active'              => '已啟用',
                    'add-btn'             => '建立分類獎勵',
                    'end-date'            => '結束日期',
                    'enter-reward-points' => '輸入獎勵積分',
                    'inactive'            => '已停用',
                    'save-btn'            => '儲存分類獎勵',
                    'select-category'     => '選擇分類',
                    'select-status'       => '選擇狀態',
                    'start-date'          => '開始日期',
                    'status'              => '狀態',
                    'title'               => '新增分類獎勵點數',
                ],

                'edit' => [
                    'active'              => '已啟用',
                    'add-btn'             => '建立分類獎勵',
                    'end-date'            => '結束日期',
                    'enter-reward-points' => '輸入獎勵積分',
                    'inactive'            => '已停用',
                    'save-btn'            => '更新分類獎勵',
                    'select-category'     => '選擇分類',
                    'select-status'       => '選擇狀態',
                    'start-date'          => '開始日期',
                    'status'              => '狀態',
                    'title'               => '編輯分類獎勵點數',
                ],
            ],

            'category-specific' => [
                'index' => [
                    'add-btn'                => '建立分類時間特定獎勵',
                    'category-specific-time' => '分類特定時間',
                    'title'                  => '特定時間的分類獎勵點數',
                ],

                'create' => [
                    'save-btn' => '儲存時間特定分類獎勵',
                    'title'    => '新增時間特定分類獎勵點數',
                ],

                'edit' => [
                    'save-btn' => '更新時間特定分類獎勵',
                    'title'    => '編輯時間特定分類獎勵點數',
                ],
            ],

            'cart' => [
                'index' => [
                    'add-btn'        => '新增購物車獎勵點數',
                    'create-success' => '購物車獎勵建立成功',
                    'delete-failed'  => '購物車獎勵刪除失敗！',
                    'delete-success' => '購物車獎勵刪除成功',
                    'error-cart'     => '未選擇購物車',
                    'title'          => '購物車獎勵點數',
                    'update-success' => '購物車獎勵更新成功',

                    'datagrid' => [
                        'amount-from'         => '金額從',
                        'amount-to'           => '金額至',
                        'delete'              => '刪除',
                        'edit'                => '編輯',
                        'end-date'            => '結束日期',
                        'id'                  => 'ID',
                        'mass-delete-success' => '購物車獎勵刪除成功',
                        'mass-update-success' => '購物車獎勵更新成功',
                        'name'                => '名稱',
                        'reward-points'       => '獎勵積分',
                        'sku'                 => 'SKU',
                        'start-date'          => '開始日期',
                        'status'              => '狀態',
                        'update-status'       => '更新',

                        'options' => [
                            'active'   => '已啟用',
                            'inactive' => '已停用',
                        ],
                    ],
                ],

                'create' => [
                    'active'              => '已啟用',
                    'add-btn'             => '新增購物車獎勵點數',
                    'amount-from'         => '金額從',
                    'amount-to'           => '金額至',
                    'end-date'            => '結束日期',
                    'enter-reward-points' => '輸入獎勵積分',
                    'inactive'            => '已停用',
                    'save-btn'            => '儲存購物車獎勵',
                    'select-status'       => '選擇狀態',
                    'start-date'          => '開始日期',
                    'status'              => '狀態',
                    'title'               => '新增購物車獎勵點數',
                ],

                'edit' => [
                    'active'              => '已啟用',
                    'add-btn'             => '新增購物車獎勵點數',
                    'amount-from'         => '金額從',
                    'amount-to'           => '金額至',
                    'end-date'            => '結束日期',
                    'enter-reward-points' => '輸入獎勵積分',
                    'inactive'            => '已停用',
                    'save-btn'            => '更新購物車獎勵',
                    'select-status'       => '選擇狀態',
                    'start-date'          => '開始日期',
                    'status'              => '狀態',
                    'title'               => '編輯購物車獎勵點數',
                ],
            ],

            'attributes' => [
                'index' => [
                    'create-btn'     => '建立屬性獎勵點數',
                    'create-success' => '屬性獎勵建立成功',
                    'delete-failed'  => '屬性獎勵刪除失敗！',
                    'delete-success' => '屬性獎勵刪除成功',
                    'error-cart'     => '未選擇任何屬性',
                    'title'          => '屬性獎勵點數',
                    'update-success' => '屬性獎勵更新成功',

                    'datagrid' => [
                        'code'                => '代碼',
                        'delete'              => '刪除',
                        'edit'                => '編輯',
                        'id'                  => 'ID',
                        'mass-delete-success' => '屬性獎勵刪除成功',
                        'mass-update-success' => '屬性獎勵更新成功',
                        'name'                => '名稱',
                        'reward-points'       => '獎勵積分',
                        'sku'                 => 'SKU 代碼',
                        'status'              => '狀態',
                        'update-status'       => '更新',

                        'options' => [
                            'active'   => '已啟用',
                            'inactive' => '已停用',
                        ],
                    ],
                ],

                'create' => [
                    'active'              => '已啟用',
                    'enter-reward-points' => '輸入獎勵積分',
                    'inactive'            => '已停用',
                    'reward_points'       => '屬性積分',
                    'save-btn'            => '儲存屬性獎勵點數',
                    'select-attributes'   => '選擇屬性',
                    'select-status'       => '選擇狀態',
                    'status'              => '狀態',
                    'title'               => '新增屬性獎勵點數',
                ],

                'edit' => [
                    'active'              => '已啟用',
                    'edit-btn'            => '更新屬性獎勵點數',
                    'enter-reward-points' => '輸入獎勵積分',
                    'inactive'            => '已停用',
                    'reward_points'       => '屬性積分',
                    'select-attributes'   => '選擇屬性',
                    'select-status'       => '選擇狀態',
                    'status'              => '狀態',
                    'title'               => '編輯屬性獎勵點數',
                ],
            ],

            'system' => [
                'index' => [
                    'title' => '獎勵系統詳情',

                    'datagrid' => [
                        'active'              => '已啟用',
                        'id'                  => 'ID',
                        'inactive'            => '已停用',
                        'name'                => '名稱',
                        'reward-points'       => '獎勵積分',
                        'status'              => '狀態',
                        'used-rewards-points' => '已使用獎勵積分',
                        'view'                => '查看',
                    ],
                ],

                'balance-card' => [
                    'title'        => '獎勵點數',
                    'view-history' => '查看記錄',
                    'pts'          => '點',
                ],

                'view' => [
                    'title'            => '獎勵系統詳情',
                    'allocate-btn'     => '分配積分',
                    'allocate-success' => '獎勵積分分配成功。',

                    'adjust-success-add'    => '獎勵積分新增成功。',
                    'adjust-success-deduct' => '獎勵積分扣除成功。',
                    'insufficient-balance'  => '獎勵積分餘額不足。',
                    'type-add'              => '新增',
                    'type-deduct'           => '扣除',

                    'adjust-modal' => [
                        'title' => '調整獎勵積分',
                    ],

                    'allocate-modal' => [
                        'title'              => '分配獎勵積分',
                        'points'             => '積分',
                        'points-placeholder' => '輸入積分數量',
                        'reason'             => '原因',
                        'reason-placeholder' => '輸入分配原因',
                        'save-btn'           => '儲存',
                        'error-points'       => '積分至少為 1。',
                        'error-reason'       => '原因為必填項。',
                    ],

                    'datagrid' => [
                        'approved'            => '已批准',
                        'attribute-id'        => '屬性 ID',
                        'canceled'            => '已取消',
                        'category-id'         => '分類 ID',
                        'closed'              => '已關閉',
                        'created-at'          => '交易日期',
                        'exp-date'            => '到期日期',
                        'expire'              => '已到期',
                        'fraud'               => '欺詐',
                        'id'                  => 'ID',
                        'name'                => '名稱',
                        'note'                => '備註',
                        'order-id'            => '訂單 ID',
                        'pending'             => '待處理',
                        'processing'          => '處理中',
                        'product-id'          => '產品 ID',
                        'reward-points'       => '獎勵積分',
                        'status'              => '狀態',
                        'total-reward-points' => '總獎勵積分',
                        'used'                => '已使用',
                        'created-by'          => '建立人',
                        'creator-system'      => '系統',
                        'creator-admin'       => '管理員',
                        'creator-customer'    => '客戶（本人）',
                    ],
                ],
            ],

            'wallet-topup' => [
                'index' => [
                    'add-btn'        => '新增錢包獎勵規則',
                    'create-success' => '錢包獎勵規則建立成功',
                    'delete-failed'  => '錢包獎勵規則刪除失敗',
                    'delete-success' => '錢包獎勵規則刪除成功',
                    'title'          => '錢包獎勵規則',
                    'update-success' => '錢包獎勵規則更新成功',

                    'datagrid' => [
                        'customer-group'       => '客戶群組',
                        'delete'               => '刪除',
                        'edit'                 => '編輯',
                        'id'                   => 'ID',
                        'mass-delete-success'  => '所選規則刪除成功',
                        'mass-update-success'  => '所選規則更新成功',
                        'max-amount'           => '最高金額',
                        'min-amount'           => '最低金額',
                        'mode'                 => '模式',
                        'priority'             => '優先順序',
                        'status'               => '狀態',
                        'trigger'              => '觸發條件',
                        'trigger-wallet-spend' => '錢包消費',
                        'trigger-wallet-topup' => '錢包儲值',
                        'update-status'        => '更新狀態',
                        'value'                => '積分值',

                        'options' => [
                            'active'   => '已啟用',
                            'inactive' => '已停用',
                        ],
                    ],
                ],

                'create' => [
                    'active'               => '已啟用',
                    'all-groups'           => '所有客戶群組（全域）',
                    'customer-group'       => '客戶群組',
                    'inactive'             => '已停用',
                    'max-amount'           => '最高金額',
                    'min-amount'           => '最低金額',
                    'mode'                 => '積分模式',
                    'mode-fixed'           => '固定積分',
                    'mode-percent'         => '金額百分比',
                    'priority'             => '優先順序',
                    'save-btn'             => '儲存規則',
                    'select-mode'          => '選擇模式',
                    'select-status'        => '選擇狀態',
                    'select-trigger'       => '選擇觸發條件',
                    'status'               => '狀態',
                    'title'                => '新增錢包獎勵規則',
                    'trigger'              => '觸發條件',
                    'trigger-wallet-spend' => '錢包消費',
                    'trigger-wallet-topup' => '錢包儲值',
                    'value'                => '積分值',
                    'value-fixed'          => '獎勵積分',
                    'value-percent'        => '金額百分比 (%)',
                ],

                'edit' => [
                    'save-btn' => '更新規則',
                    'title'    => '編輯錢包獎勵規則',
                ],
            ],

            'redemption' => [
                'index' => [
                    'conversion-rate'           => '購物車總金額的兌換率',
                    'conversion'                => '兌換率',
                    'enable-apply-points-label' => '允許客戶在結帳時使用積分',
                    'enable-apply-points'       => '啟用結帳時積分使用',
                    'points'                    => '積分',
                    'redemption-cart-label'     => '允許客戶在購買時使用積分',
                    'redemption-cart'           => '購物車總金額兌換',
                    'save-btn'                  => '儲存',
                    'title'                     => '兌換設定',
                    'update-success'            => '兌換設定更新成功',
                ],
            ],
        ],
    ],

    'shop' => [
        'customer' => [
            'account' => [
                'layouts' => [
                    'reward-points' => '獎勵點數',
                ],

                'rewards' => [
                    'index' => [
                        'your-reward-points' => '您的獎勵點數',

                        'datagrid' => [
                            'approved'            => '已批准',
                            'attribute-id'        => '屬性 ID',
                            'canceled'            => '已取消',
                            'category-id'         => '分類 ID',
                            'closed'              => '已關閉',
                            'created-at'          => '交易日期',
                            'exp-date'            => '到期日期',
                            'expire'              => '已到期',
                            'fraud'               => '欺詐',
                            'id'                  => 'ID',
                            'name'                => '名稱',
                            'note'                => '備註',
                            'order-id'            => '訂單 ID',
                            'pending'             => '待處理',
                            'processing'          => '處理中',
                            'product-id'          => '產品 ID',
                            'reward-points'       => '獎勵積分',
                            'status'              => '狀態',
                            'total-reward-points' => '總獎勵積分',
                            'used'                => '已使用',
                        ],
                    ],
                ],
            ],
        ],

        'product' => [
            'product-reward-end-date' => '購買此產品可獲得 :reward 積分，優惠有效期至 :end_date',
            'product-reward'          => '購買此產品可獲得 :reward 積分',

            'review' => [
                'review-points' => '撰寫評價可獲得 :points 積分',
            ],
        ],

        'register' => [
            'register-reward' => '立即註冊成為會員，可獲得 :points 積分',
        ],

        'checkout' => [
            'cart' => [
                'mini-cart' => [
                    'reward-points'        => '獎勵點數',
                    'reward-points-earned' => '獲得的獎勵點數',

                    'summary' => [
                        'reward_points' => '總獎勵積分',
                    ],
                ],

                'points' => [
                    'apply-points'  => '使用積分',
                    'button-title'  => '套用',
                    'enter-points'  => '輸入積分',
                    'reward-points' => '套用獎勵積分',
                ],
            ],
        ],
    ],

    'checkout' => [
        'onepage' => [
            'apply-points'       => '使用積分',
            'enter-points'       => '輸入積分',
            'points-used'        => '已使用積分',
            'redemption-setting' => '說明：:points 積分等於 :conversion_rate',
            'total-point'        => '您的總積分：:total_reward_points',
        ],

        'total' => [
            'cannot-apply-points'         => '無法套用積分',
            'grandtotal'                  => '總計',
            'invalid-points'              => '積分欄位為必填',
            'only-number'                 => '積分欄位必須為 1 或以上',
            'points-applied'              => '已套用積分',
            'points-apply-issue'          => '積分代碼無法套用。',
            'points'                      => '積分',
            'redem-points'                => '兌換積分 (-)',
            'remove-points'               => '移除積分',
            'success-points'              => '積分代碼套用成功。',
            'total-reward-points-awarded' => '總獎勵積分',
            'unauthorized-use-points'     => '您目前無法使用獎勵積分，請聯絡管理員。',
            'use-can-use-only'            => '您只能使用 ',
            'warning-required-less-point' => '兌換積分必須低於商品價格。',
            'you-have-only'               => '您僅有 ',
        ],
    ],

    'mail' => [
        'registration' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '歡迎並感謝您的註冊！',
            'points-rewarded'  => '您因註冊獲得了 :points 點數',
            'subject'          => '新客戶註冊',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'review' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '感謝您對 :product_name 的評價！',
            'points-rewarded'  => '您因評價獲得了 :points 點數',
            'subject'          => '客戶評價',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'dob' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '祝您生日快樂！',
            'points-rewarded'  => '您因生日獲得了 :points 點數',
            'subject'          => '客戶生日',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'pending' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '感謝您在我們網站上訂購產品！',
            'points-rewarded'  => '在訂單號 #:order_id 批准後，您可使用 :points 點數',
            'status'           => '待處理',
            'subject'          => '客戶訂單',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'used' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '感謝您使用點數購買我們網站上的產品！',
            'points-rewarded'  => '您在訂單號 #:order_id 中使用了 :points 點數',
            'status'           => '已使用',
            'subject'          => '客戶使用積分',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'approved' => [
            'dear'                     => '親愛的 :customer_name',
            'greeting'                 => '您的積分已獲批准！',
            'points-rewarded'          => '您因訂單號 #:order_id 獲得了 :points 點數',
            'points-rewarded-no-order' => '您已獲得 :points 點數。',
            'status'                   => '已批准',
            'subject'                  => '積分批准',
            'thanks'                   => '謝謝！',
            'total-point-left'         => '您還有 :total_reward_points 點數',
            'used-points'              => '您已使用了 :used_reward_points 點數',
        ],

        'processing' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '狀態已於 :date 更改為處理中！',
            'points-rewarded'  => '訂單號 #:order_id 的狀態已更改',
            'status'           => '已批准',
            'subject'          => '積分處理中',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'expire' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '狀態已於 :date 更改為已到期！',
            'points-rewarded'  => '訂單號 #:order_id 的狀態已更改',
            'status'           => '已到期',
            'subject'          => '積分到期',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'closed' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '狀態已於 :date 更改為已關閉！',
            'points-rewarded'  => '訂單號 #:order_id 的狀態已更改為已關閉',
            'status'           => '已關閉',
            'subject'          => '積分已關閉',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'cancel' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '您的積分已於 :date 被取消！',
            'points-rewarded'  => '訂單號 #:order_id 的 :points 點數已被取消',
            'status'           => '已取消',
            'subject'          => '積分取消',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],

        'fraud' => [
            'dear'             => '親愛的 :customer_name',
            'greeting'         => '狀態已更改為欺詐！',
            'points-rewarded'  => '訂單號 #:order_id 的 :points 點數已於 :date 被取消',
            'status'           => '欺詐',
            'subject'          => '積分欺詐',
            'thanks'           => '謝謝！',
            'total-point-left' => '您還有 :total_reward_points 點數',
            'used-points'      => '您已使用了 :used_reward_points 點數',
        ],
    ],
];