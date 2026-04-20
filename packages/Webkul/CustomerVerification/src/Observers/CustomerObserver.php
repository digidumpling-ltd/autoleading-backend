<?php

namespace Webkul\CustomerVerification\Observers;

use Webkul\Customer\Models\Customer;

class CustomerObserver
{
    /**
     * Handle the Customer "created" event.
     */
    public function created(Customer $customer): void
    {
        // Set initial verification status if not set
        if (is_null($customer->verification_status)) {
            $customer->verification_status = 'incomplete';
            $customer->save();
        }
    }

    /**
     * Handle the Customer "updated" event.
     */
    public function updated(Customer $customer): void
    {
        // Handle verification status updates based on document uploads
        if ($customer->wasChanged('verification_status')) {
            // Additional logic can be added here if needed
        }
    }
}