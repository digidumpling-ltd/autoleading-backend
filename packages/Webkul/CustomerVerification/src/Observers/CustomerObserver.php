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

}