<?php

namespace Webkul\CustomForm\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Customer\Facades\Captcha;

class TvdFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return Captcha::getValidations([
            'chinese_name' => 'required|string|max:400',
            'english_name' => 'required|string|max:400',
            'rental_model' => 'required|string|max:400',
            'return_date' => 'required|date',
            'contact_number' => 'required|string|max:400',
            'email' => 'required|email|max:400',
            'refund_type' => 'required|in:local,overseas',
            'local_bank_info' => 'nullable|string|max:1000',
            'overseas_bank_info' => 'nullable|string|max:1000',
        ]);
    }

    public function messages(): array
    {
        return Captcha::getValidationMessages();
    }
}
