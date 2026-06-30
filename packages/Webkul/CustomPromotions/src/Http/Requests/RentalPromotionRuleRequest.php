<?php

namespace Webkul\CustomPromotions\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RentalPromotionRuleRequest extends FormRequest
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
                ->where('promotion_type', 'rental')
                ->where('is_primary', 1)
                ->value('id');
        }

        return [
            'name' => 'required',
            'channels' => 'required|array|min:1',
            'customer_groups' => 'required|array|min:1',
            'action_type' => 'required',
            'reward_mode' => 'required_unless:action_type,free_extension',
            'reward_value' => [
                'required',
                function ($attribute, $value, $fail) {
                    $actionType = $this->input('action_type');
                    $rewardMode = $this->input('reward_mode');

                    if ($actionType === 'reward_product' && $rewardMode === 'note') {
                        if (empty(trim((string) $value))) {
                            $fail('The note text is required.');
                        }
                    } elseif (! is_numeric($value) || (float) $value < 0) {
                        $fail('The reward value must be a number >= 0.');
                    }
                },
            ],
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
