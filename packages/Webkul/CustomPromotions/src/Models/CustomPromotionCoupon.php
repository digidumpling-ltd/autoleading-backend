<?php

namespace Webkul\CustomPromotions\Models;

use Illuminate\Database\Eloquent\Model;
use Webkul\CustomPromotions\Contracts\CustomPromotionCoupon as CustomPromotionCouponContract;

class CustomPromotionCoupon extends Model implements CustomPromotionCouponContract
{
    protected $table = 'custom_promotion_coupons';

    protected $fillable = [
        'promotion_id',
        'promotion_type',
        'code',
        'usage_limit',
        'usage_per_customer',
        'times_used',
        'is_primary',
        'expired_at',
    ];

    protected $casts = [
        'is_primary' => 'boolean',
    ];
}
