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
        return [
            'name' => 'required',
            'channels' => 'required|array|min:1',
            'customer_groups' => 'required|array|min:1',
            'action_type' => 'required',
            'reward_mode' => 'required',
            'reward_value' => 'required|numeric|min:0',
            'starts_from' => 'nullable|date',
            'ends_till' => 'nullable|date|after_or_equal:starts_from',
        ];
    }
}
