<?php

namespace Webkul\CustomerVerification\Listeners;

class HandleCustomerLogin
{
    /**
     * Handle the customer after login event.
     * 
     * NOTE: Verification status checks are NOT enforced at login yet.
     * Story 2.1-2.2: Customers can login regardless of verification_status
     * Story 2.3: Admin verification workflow will enforce login blocking
     */
    public function handle($customer): void
    {
        // PLACEHOLDER: Verification status checks will be implemented in Story 2.3
        // when the admin verification and approval workflow is complete.
        // For now, allow all customers to login.
    }
}

