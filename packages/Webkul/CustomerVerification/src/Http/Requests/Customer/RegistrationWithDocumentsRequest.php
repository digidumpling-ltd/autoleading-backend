<?php

namespace Webkul\CustomerVerification\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Webkul\Customer\Facades\Captcha;

class RegistrationWithDocumentsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'first_name' => 'string|required',
            'last_name' => 'string|required',
            'email' => 'email|required|unique:customers,email,NULL,id,channel_id,' . core()->getCurrentChannel()->id,
            'password' => 'confirmed|min:6|required',
            'id_document' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',
            'driver_license' => 'nullable|file|mimes:png,jpg,jpeg,webp|max:5120',
            'address_proof' => 'nullable|file|mimes:png,jpg,jpeg,pdf|max:5120',
        ];

        return Captcha::getValidations($rules);
    }

    public function messages(): array
    {
        return Captcha::getValidationMessages();
    }
}
