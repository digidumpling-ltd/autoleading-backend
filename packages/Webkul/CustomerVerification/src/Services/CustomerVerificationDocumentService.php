<?php

namespace Webkul\CustomerVerification\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Repositories\CustomerVerificationDocumentRepository;
use Webkul\CustomerVerification\Support\Verification;

class CustomerVerificationDocumentService
{
    public function __construct(
        protected CustomerVerificationDocumentRepository $documentRepository
    ) {}

    public function getDocumentMeta(): array
    {
        return [
            Verification::DOCUMENT_TYPE_ID_DOCUMENT => [
                'label' => 'customer-verification::app.common.document_type_id_document',
                'hint' => 'customer-verification::app.common.upload_id_document_hint',
                'accept' => 'image/png,image/jpeg,image/webp,application/pdf',
            ],
            Verification::DOCUMENT_TYPE_DRIVER_LICENSE => [
                'label' => 'customer-verification::app.common.document_type_driver_license',
                'hint' => 'customer-verification::app.common.upload_driver_license_hint',
                'accept' => 'image/png,image/jpeg,image/webp,application/pdf',
            ],
            Verification::DOCUMENT_TYPE_ADDRESS_PROOF => [
                'label' => 'customer-verification::app.common.document_type_address_proof',
                'hint' => 'customer-verification::app.common.upload_address_proof_hint',
                'accept' => 'image/png,image/jpeg,image/webp,application/pdf',
            ],
        ];
    }

    public function getUploadValidationRules(): array
    {
        return [
            Verification::DOCUMENT_TYPE_ID_DOCUMENT => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
            Verification::DOCUMENT_TYPE_DRIVER_LICENSE => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
            Verification::DOCUMENT_TYPE_ADDRESS_PROOF => ['nullable', 'file', 'mimes:png,jpg,jpeg,webp,pdf', 'max:5120'],
        ];
    }

    public function getCustomerDocumentsByType(int $customerId): Collection
    {
        $documents = $this->documentRepository->findWhere([
            'customer_id' => $customerId,
        ]);

        return collect($documents)->keyBy('type');
    }

    public function hasAllRequiredDocuments(int $customerId): bool
    {
        return $this->getCustomerDocumentsByType($customerId)
            ->only(Verification::REQUIRED_DOCUMENT_TYPES)
            ->count() === count(Verification::REQUIRED_DOCUMENT_TYPES);
    }

    public function storeCustomerDocument(int $customerId, string $documentType, UploadedFile $uploadedFile): void
    {
        $this->assignReferenceNumberIfAbsent($customerId);

        $storedPath = $this->storeOnPublicDisk($customerId, $documentType, $uploadedFile);

        $payload = [
            'customer_id' => $customerId,
            'type' => $documentType,
            'path' => $storedPath,
            'mime' => $uploadedFile->getClientMimeType(),
            'size' => $uploadedFile->getSize(),
            'status' => Verification::STATUS_PENDING,
            'original_name' => $uploadedFile->getClientOriginalName(),
        ];

        $existingDocument = $this->documentRepository->findOneWhere([
            'customer_id' => $customerId,
            'type' => $documentType,
        ]);

        if ($existingDocument) {
            $this->documentRepository->update($payload, $existingDocument->id);

            return;
        }

        $this->documentRepository->create($payload);
    }

    public function deleteDocument(int $documentId, int $customerId): void
    {
        $doc = $this->documentRepository->findOneWhere([
            'id'          => $documentId,
            'customer_id' => $customerId,
        ]);

        if (! $doc) {
            abort(404);
        }

        $disk = config('filesystems.default');

        Storage::disk($disk)->delete($doc->path);

        $this->documentRepository->delete($documentId);
    }

    protected function assignReferenceNumberIfAbsent(int $customerId): void
    {
        $hasReference = Customer::where('id', $customerId)
            ->whereNotNull('reference_number')
            ->exists();

        if ($hasReference) {
            return;
        }

        do {
            $reference = 'CV' . now()->format('Ymd') . strtoupper(Str::random(6));
        } while (Customer::where('reference_number', $reference)->exists());

        DB::table('customers')
            ->where('id', $customerId)
            ->update(['reference_number' => $reference]);
    }

    protected function storeOnPublicDisk(int $customerId, string $documentType, UploadedFile $uploadedFile): string
    {
        $extension = strtolower($uploadedFile->getClientOriginalExtension() ?: $uploadedFile->extension() ?: 'bin');

        $fileName = now()->timestamp.'_'.$customerId.'_'.$documentType.'.'.$extension;

        $disk = config('filesystems.default');

        return Storage::disk($disk)->putFileAs(
            'customer-documents/'.$customerId.'/'.$documentType,
            $uploadedFile,
            $fileName,
            ['visibility' => 'public']
        );
    }
}
