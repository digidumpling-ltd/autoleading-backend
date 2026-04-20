<?php

namespace Webkul\CustomerVerification\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;
use Webkul\CustomerVerification\Services\CustomerVerificationDocumentService;
use Webkul\CustomerVerification\Support\Verification;

class CustomerVerificationUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->guard('customer')->check();
    }

    public function rules(CustomerVerificationDocumentService $documentService): array
    {
        return $documentService->getUploadValidationRules();
    }

    public function messages(): array
    {
        return [
            'id_document.max' => trans('customer-verification::app.common.verification_file_too_large'),
            'driver_license.max' => trans('customer-verification::app.common.verification_file_too_large'),
            'address_proof.max' => trans('customer-verification::app.common.verification_file_too_large'),
            'id_document.mimes' => trans('customer-verification::app.common.verification_invalid_file_type'),
            'driver_license.mimes' => trans('customer-verification::app.common.verification_invalid_file_type'),
            'address_proof.mimes' => trans('customer-verification::app.common.verification_invalid_file_type'),
        ];
    }

    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $validator): void {
            $hasAnyFile = false;

            foreach (Verification::REQUIRED_DOCUMENT_TYPES as $documentType) {
                if ($this->hasFile($documentType)) {
                    $hasAnyFile = true;

                    break;
                }
            }

            if (! $hasAnyFile) {
                $validator->errors()->add(
                    'documents',
                    trans('customer-verification::app.common.verification_upload_required')
                );
            }
        });
    }
}
