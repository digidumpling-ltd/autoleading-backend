<?php

namespace Webkul\CustomerVerification\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Models\VerificationAuditLog;
use Webkul\CustomerVerification\Support\Verification;

class AdminVerificationActionService
{
    public function approve(int $customerId, int $adminId): void
    {
        DB::transaction(function () use ($customerId, $adminId) {
            Customer::where('id', $customerId)->update([
                'verification_status' => Verification::STATUS_APPROVED,
                'rejection_reason'    => null,
            ]);

            // Log the action
            VerificationAuditLog::create([
                'admin_id' => $adminId,
                'customer_id' => $customerId,
                'action' => 'approved',
            ]);

            // Dispatch event
            $customer = Customer::find($customerId);
            if ($customer) {
                Event::dispatch('verification.admin.approved', $customer);
            }
        });
    }

    public function reject(int $customerId, int $adminId, string $reason): void
    {
        DB::transaction(function () use ($customerId, $adminId, $reason) {
            Customer::where('id', $customerId)->update([
                'verification_status' => Verification::STATUS_REJECTED,
                'rejection_reason'    => $reason,
            ]);

            // Log the action
            VerificationAuditLog::create([
                'admin_id' => $adminId,
                'customer_id' => $customerId,
                'action' => 'rejected',
                'reason' => $reason,
            ]);

            // Dispatch event
            $customer = Customer::find($customerId);
            if ($customer) {
                Event::dispatch('verification.admin.rejected', $customer);
            }
        });
    }
}
