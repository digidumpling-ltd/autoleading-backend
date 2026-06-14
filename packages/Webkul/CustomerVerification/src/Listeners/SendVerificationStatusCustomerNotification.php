<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\CustomerVerification\Mail\VerificationApprovedNotification;
use Webkul\CustomerVerification\Mail\VerificationRejectedNotification;

class SendVerificationStatusCustomerNotification
{
    public function approved(object $customer): void
    {
        try {
            Mail::queue(new VerificationApprovedNotification($customer));
        } catch (\Exception $e) {
            \Log::error('CustomerVerification: failed to send approval email: ' . $e->getMessage());
        }
    }

    public function rejected(object $customer): void
    {
        try {
            Mail::queue(new VerificationRejectedNotification($customer));
        } catch (\Exception $e) {
            \Log::error('CustomerVerification: failed to send rejection email: ' . $e->getMessage());
        }
    }
}
