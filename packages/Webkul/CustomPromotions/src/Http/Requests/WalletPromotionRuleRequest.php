<?php

namespace Webkul\CustomPromotions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WalletPromotionRuleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $couponId = null;

        if ($this->route('id') && $this->input('coupon_type')) {
            $couponId = \Illuminate\Support\Facades\DB::table('custom_promotion_coupons')
                ->where('promotion_id', $this->route('id'))
                ->where('promotion_type', 'wallet')
                ->where('is_primary', 1)
                ->value('id');
        }

        return [
            'name' => 'required',
            'channels' => 'required|array|min:1',
            'customer_groups' => 'required|array|min:1',
            'action_type' => 'required',
            'reward_mode' => 'required',
            'reward_value' => 'required|numeric|min:0',
            'starts_from' => 'nullable|date',
            'ends_till' => 'nullable|date|after_or_equal:starts_from',
            'coupon_type' => 'nullable|integer|in:0,1',
            'coupon_code' => [
                'required_if:coupon_type,1',
                'nullable',
                'string',
                \Illuminate\Validation\Rule::unique('custom_promotion_coupons', 'code')->ignore($couponId),
            ],
            'uses_per_coupon' => 'nullable|integer|min:0',
            'usage_per_customer' => 'nullable|integer|min:0',
            'end_other_rules' => 'nullable|boolean',
        ];
    }
}
