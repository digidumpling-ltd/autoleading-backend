<?php

namespace Webkul\CustomPromotions\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasOne;
use Webkul\CustomPromotions\Models\CustomPromotionCouponProxy;

trait HasPromotionCoupon
{
    public function coupon(): HasOne
    {
        return $this->hasOne(CustomPromotionCouponProxy::modelClass(), 'promotion_id')
            ->where('promotion_type', $this->promotionType)
            ->where('is_primary', 1);
    }

    public function getCouponCodeAttribute(): ?string
    {
        return $this->coupon?->code;
    }
}
