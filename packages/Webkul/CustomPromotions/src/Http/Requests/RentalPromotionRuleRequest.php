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
        ];
    }
}
