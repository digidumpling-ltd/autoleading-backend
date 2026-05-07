<?php

namespace Webkul\CustomerVerification\Listeners;

use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;

class HandleCustomerRegistration
{
    public function handle($customer): void
    {
        $this->generateReferenceNumber($customer);

        $customer->verification_status = 'incomplete';
        $customer->save();
    }

    protected function generateReferenceNumber($customer): void
    {
        $customer->reference_number = $this->createUniqueReferenceNumber();
        $customer->save();
    }

    protected function createUniqueReferenceNumber(): string
    {
        do {
            $referenceNumber = 'CV' . now()->format('Ymd') . strtoupper(Str::random(6));
        } while (Customer::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }
}