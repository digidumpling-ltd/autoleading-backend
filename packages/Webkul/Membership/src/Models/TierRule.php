<?php

namespace Webkul\Membership\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Customer\Models\CustomerGroupProxy;

class TierRule extends Model
{
    protected $table = 'membership_tier_rules';

    protected $fillable = [
        'min_balance',
        'max_balance',
        'customer_group_id',
        'background_color',
        'text_color',
        'sort_order',
    ];

    protected $casts = [
        'min_balance' => 'float',
        'max_balance' => 'float',
        'sort_order'  => 'integer',
    ];

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroupProxy::modelClass(), 'customer_group_id');
    }
}
