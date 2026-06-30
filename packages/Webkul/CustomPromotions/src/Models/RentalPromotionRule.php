<?php

namespace Webkul\CustomPromotions\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Webkul\Core\Models\ChannelProxy;
use Webkul\Customer\Models\CustomerGroupProxy;
use Webkul\CustomPromotions\Contracts\RentalPromotionRule as RentalPromotionRuleContract;
use Webkul\CustomPromotions\Models\Concerns\HasPromotionCoupon;

class RentalPromotionRule extends Model implements RentalPromotionRuleContract
{
    use HasPromotionCoupon;

    protected $table = 'custom_rental_promotion_rules';

    protected string $promotionType = 'rental';

    protected $fillable = [
        'name',
        'description',
        'starts_from',
        'ends_till',
        'status',
        'condition_type',
        'conditions',
        'action_type',
        'reward_mode',
        'reward_value',
        'sort_order',
        'coupon_type',
        'uses_per_coupon',
        'usage_per_customer',
        'end_other_rules',
    ];

    protected $casts = [
        'conditions' => 'array',
        'status' => 'boolean',
        'coupon_type' => 'integer',
        'end_other_rules' => 'boolean',
    ];

    public function channels(): BelongsToMany
    {
        return $this->belongsToMany(ChannelProxy::modelClass(), 'custom_rental_promo_rule_channels', 'rental_promotion_rule_id', 'channel_id');
    }

    public function customerGroups(): BelongsToMany
    {
        return $this->belongsToMany(CustomerGroupProxy::modelClass(), 'custom_rental_promo_rule_cgroups', 'rental_promotion_rule_id', 'customer_group_id');
    }
}
