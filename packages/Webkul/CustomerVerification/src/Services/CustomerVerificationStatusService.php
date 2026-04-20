<?php

namespace Webkul\CustomerVerification\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Webkul\CustomerVerification\Support\Verification;

class CustomerVerificationStatusService
{
    public function __construct(
        protected CustomerVerificationDocumentService $documentService
    ) {}

    public function moveToPendingWhenEligible(object $customer): bool
    {
        $currentStatus = (string) ($customer->verification_status ?? Verification::STATUS_INCOMPLETE);

        if ($currentStatus === Verification::STATUS_APPROVED) {
            return false;
        }

        if (! in_array($currentStatus, Verification::TRANSITIONABLE_UPLOAD_STATUSES, true)) {
            return false;
        }

        if (! $this->documentService->hasAllRequiredDocuments((int) $customer->id)) {
            return false;
        }

        // Update directly to avoid Customer model fillable restrictions.
        DB::table('customers')
            ->where('id', (int) $customer->id)
            ->update([
                'verification_status' => Verification::STATUS_PENDING,
            ]);

        Event::dispatch('customer.verification.documents_complete', $customer);

        // Timestamped audit trail entry for compliance and support debugging.
        Log::info('customer.verification.status_changed', [
            'customer_id' => (int) $customer->id,
            'from' => $currentStatus,
            'to' => Verification::STATUS_PENDING,
            'changed_at' => now()->toDateTimeString(),
        ]);

        return true;
    }
}
