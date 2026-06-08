<?php

namespace Webkul\Rewards\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Customer\Models\CustomerGroup;
use Webkul\Rewards\Contracts\WalletTopupRewardRule as WalletTopupRewardRuleContract;

class WalletTopupRewardRule extends Model implements WalletTopupRewardRuleContract
{
    protected $table = 'wallet_topup_reward_rules';

    protected $fillable = [
        'customer_group_id',
        'trigger',
        'mode',
        'value',
        'min_amount',
        'max_amount',
        'priority',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    public function customerGroup(): BelongsTo
    {
        return $this->belongsTo(CustomerGroup::class);
    }
}
