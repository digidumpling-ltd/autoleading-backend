<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Support\Facades\Mail;
use Webkul\CustomerVerification\Mail\DocumentsSubmittedAdminNotification;

class SendDocumentsSubmittedAdminNotification
{
    public function handle(object $customer): void
    {
        try {
            Mail::queue(new DocumentsSubmittedAdminNotification($customer));
        } catch (\Exception $e) {
            \Log::error('CustomerVerification: failed to send admin notification: ' . $e->getMessage());
        }
    }
}
