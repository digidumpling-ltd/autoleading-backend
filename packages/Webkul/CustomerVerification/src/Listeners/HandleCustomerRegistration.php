<?php

namespace Webkul\CustomerVerification\Listeners;

class HandleCustomerRegistration
{
    public function handle($customer): void
    {
        $customer->verification_status = 'incomplete';
        $customer->save();
    }
}