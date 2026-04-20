<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Repositories\CustomerVerificationDocumentRepository;

class HandleCustomerRegistration
{
    /**
     * Create a new listener instance.
     */
    public function __construct(
        protected CustomerVerificationDocumentRepository $documentRepository
    ) {}

    /**
     * Handle the customer registration after event.
     */
    public function handle($customer): void
    {
        // Generate unique reference number for verification tracking
        $this->generateReferenceNumber($customer);

        // Store documents if they were uploaded during registration
        $hasDocuments = $this->storeDocuments($customer->id, request());

        // Update verification status based on document upload
        $customer->verification_status = $hasDocuments ? 'pending' : 'incomplete';
        $customer->save();
    }

    /**
     * Generate and assign a unique reference number to the customer.
     */
    protected function generateReferenceNumber($customer): void
    {
        $referenceNumber = $this->createUniqueReferenceNumber();
        $customer->reference_number = $referenceNumber;
        $customer->save();
    }

    /**
     * Create a unique reference number for customer verification.
     */
    protected function createUniqueReferenceNumber(): string
    {
        do {
            // Format: CV + Date(YYYYMMDD) + Random(6 chars)
            // Example: CV20260418ABC123
            $referenceNumber = 'CV' . now()->format('Ymd') . strtoupper(Str::random(6));
        } while (Customer::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }

    /**
     * Store customer verification documents.
     */
    protected function storeDocuments($customerId, $request)
    {
        $documentTypes = ['id_document', 'driver_license', 'address_proof'];
        $hasDocuments = false;

        foreach ($documentTypes as $type) {
            if ($request->hasFile($type)) {
                $file = $request->file($type);
                $filename = time() . '_' . $type . '_' . $customerId . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('customer-documents', $filename, 'public');

                $this->documentRepository->create([
                    'customer_id' => $customerId,
                    'type' => $type,
                    'path' => $path,
                    'file_name' => $filename,
                    'original_name' => $file->getClientOriginalName(),
                    'mime' => $file->getMimeType(),
                    'size' => $file->getSize(),
                ]);

                $hasDocuments = true;
            }
        }

        return $hasDocuments;
    }
}