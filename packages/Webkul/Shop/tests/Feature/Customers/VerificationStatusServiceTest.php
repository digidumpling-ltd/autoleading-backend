<?php

use Illuminate\Support\Facades\DB;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Services\CustomerVerificationStatusService;
use Webkul\CustomerVerification\Support\Verification;

it('moves verification status to pending when all required documents exist', function () {
    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    DB::table('customers')
        ->where('id', $customer->id)
        ->update([
            'verification_status' => Verification::STATUS_INCOMPLETE,
        ]);

    DB::table('customer_verification_documents')->insert([
        [
            'customer_id' => $customer->id,
            'type' => Verification::DOCUMENT_TYPE_ID_DOCUMENT,
            'path' => 'customer-documents/'.$customer->id.'/id_document/id.png',
            'mime' => 'image/png',
            'size' => 1024,
            'status' => Verification::STATUS_PENDING,
            'original_name' => 'id.png',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer->id,
            'type' => Verification::DOCUMENT_TYPE_DRIVER_LICENSE,
            'path' => 'customer-documents/'.$customer->id.'/driver_license/license.png',
            'mime' => 'image/png',
            'size' => 1024,
            'status' => Verification::STATUS_PENDING,
            'original_name' => 'license.png',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer->id,
            'type' => Verification::DOCUMENT_TYPE_ADDRESS_PROOF,
            'path' => 'customer-documents/'.$customer->id.'/address_proof/address.pdf',
            'mime' => 'application/pdf',
            'size' => 1024,
            'status' => Verification::STATUS_PENDING,
            'original_name' => 'address.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $service = app(CustomerVerificationStatusService::class);

    expect(
        $service->moveToPendingWhenEligible(Customer::query()->findOrFail($customer->id))
    )->toBeTrue();

    expect(DB::table('customers')->where('id', $customer->id)->value('verification_status'))
        ->toBe(Verification::STATUS_PENDING);
});

it('does not change status when customer is already approved', function () {
    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    DB::table('customers')
        ->where('id', $customer->id)
        ->update([
            'verification_status' => Verification::STATUS_APPROVED,
        ]);

    DB::table('customer_verification_documents')->insert([
        [
            'customer_id' => $customer->id,
            'type' => Verification::DOCUMENT_TYPE_ID_DOCUMENT,
            'path' => 'customer-documents/'.$customer->id.'/id_document/id.png',
            'mime' => 'image/png',
            'size' => 1024,
            'status' => Verification::STATUS_PENDING,
            'original_name' => 'id.png',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer->id,
            'type' => Verification::DOCUMENT_TYPE_DRIVER_LICENSE,
            'path' => 'customer-documents/'.$customer->id.'/driver_license/license.png',
            'mime' => 'image/png',
            'size' => 1024,
            'status' => Verification::STATUS_PENDING,
            'original_name' => 'license.png',
            'created_at' => now(),
            'updated_at' => now(),
        ],
        [
            'customer_id' => $customer->id,
            'type' => Verification::DOCUMENT_TYPE_ADDRESS_PROOF,
            'path' => 'customer-documents/'.$customer->id.'/address_proof/address.pdf',
            'mime' => 'application/pdf',
            'size' => 1024,
            'status' => Verification::STATUS_PENDING,
            'original_name' => 'address.pdf',
            'created_at' => now(),
            'updated_at' => now(),
        ],
    ]);

    $service = app(CustomerVerificationStatusService::class);

    expect(
        $service->moveToPendingWhenEligible(Customer::query()->findOrFail($customer->id))
    )->toBeFalse();

    expect(DB::table('customers')->where('id', $customer->id)->value('verification_status'))
        ->toBe(Verification::STATUS_APPROVED);
});
