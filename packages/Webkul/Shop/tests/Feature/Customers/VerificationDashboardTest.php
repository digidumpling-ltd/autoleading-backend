<?php

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Support\Verification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

it('redirects guests when opening verification dashboard', function () {
    get(route('shop.customer.verification.index'))
        ->assertRedirect(route('shop.customer.session.index'));
});

it('shows verification dashboard with uploaded and missing document states', function () {
    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    DB::table('customer_verification_documents')->insert([
        'customer_id' => $customer->id,
        'type' => Verification::DOCUMENT_TYPE_ID_DOCUMENT,
        'path' => 'customer-documents/'.$customer->id.'/id_document/id.png',
        'mime' => 'image/png',
        'size' => 1024,
        'status' => Verification::STATUS_PENDING,
        'original_name' => 'id.png',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    get(route('shop.customer.verification.index'))
        ->assertOk()
        ->assertSeeText(trans('customer-verification::app.common.verification_dashboard_title'))
        ->assertSeeText(trans('customer-verification::app.common.document_type_id_document'))
        ->assertSeeText(trans('customer-verification::app.common.document_type_driver_license'))
        ->assertSeeText(trans('customer-verification::app.common.document_type_address_proof'))
        ->assertSeeText(trans('customer-verification::app.common.verification_document_uploaded'))
        ->assertSeeText(trans('customer-verification::app.common.verification_document_missing'));
});

it('uploads missing document and moves status to pending when all required docs exist', function () {
    Storage::fake('public');

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    DB::table('customers')
        ->where('id', $customer->id)
        ->update(['verification_status' => Verification::STATUS_INCOMPLETE]);

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
    ]);

    post(route('shop.customer.verification.upload'), [
        'customer_id' => $customer->id,
        'address_proof' => UploadedFile::fake()->create('address.pdf', 120, 'application/pdf'),
    ])
        ->assertRedirect(route('shop.customer.verification.index'))
        ->assertSessionHas('success', trans('customer-verification::app.common.verification_all_docs_uploaded'));

    expect(
        DB::table('customer_verification_documents')
            ->where('customer_id', $customer->id)
            ->count()
    )->toBe(3);

    expect(DB::table('customers')->where('id', $customer->id)->value('verification_status'))
        ->toBe(Verification::STATUS_PENDING);
});

it('returns forbidden when customer id does not match authenticated customer', function () {
    Storage::fake('public');

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    post(route('shop.customer.verification.upload'), [
        'customer_id' => $customer->id + 1,
        'id_document' => UploadedFile::fake()->image('id.png'),
    ])->assertForbidden();
});

it('validates invalid file types for dashboard uploads', function () {
    Storage::fake('public');

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    post(route('shop.customer.verification.upload'), [
        'customer_id' => $customer->id,
        'id_document' => UploadedFile::fake()->create('id.pdf', 100, 'application/pdf'),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors('id_document');
});

it('validates file size limits for dashboard uploads', function () {
    Storage::fake('public');

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    post(route('shop.customer.verification.upload'), [
        'customer_id' => $customer->id,
        'id_document' => UploadedFile::fake()->image('id.png')->size(6000),
    ])
        ->assertRedirect()
        ->assertSessionHasErrors('id_document');
});

it('moves rejected customers back to pending when they complete required uploads', function () {
    Storage::fake('public');

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    DB::table('customers')
        ->where('id', $customer->id)
        ->update(['verification_status' => Verification::STATUS_REJECTED]);

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
    ]);

    post(route('shop.customer.verification.upload'), [
        'customer_id' => $customer->id,
        'address_proof' => UploadedFile::fake()->create('address.pdf', 120, 'application/pdf'),
    ])->assertRedirect(route('shop.customer.verification.index'));

    expect(DB::table('customers')->where('id', $customer->id)->value('verification_status'))
        ->toBe(Verification::STATUS_PENDING);
});

it('replaces existing document when same type is uploaded again', function () {
    Storage::fake('public');

    /** @var Customer $customer */
    $customer = Customer::factory()->create();

    actingAs($customer, 'customer');

    $existingId = DB::table('customer_verification_documents')->insertGetId([
        'customer_id' => $customer->id,
        'type' => Verification::DOCUMENT_TYPE_ID_DOCUMENT,
        'path' => 'customer-documents/'.$customer->id.'/id_document/original-id.png',
        'mime' => 'image/png',
        'size' => 1024,
        'status' => Verification::STATUS_PENDING,
        'original_name' => 'original-id.png',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    post(route('shop.customer.verification.upload'), [
        'customer_id' => $customer->id,
        'id_document' => UploadedFile::fake()->image('replacement-id.png'),
    ])
        ->assertRedirect(route('shop.customer.verification.index'))
        ->assertSessionHas('success', trans('customer-verification::app.common.verification_docs_complete'));

    expect(
        DB::table('customer_verification_documents')
            ->where('customer_id', $customer->id)
            ->where('type', Verification::DOCUMENT_TYPE_ID_DOCUMENT)
            ->count()
    )->toBe(1);

    $storedDocument = DB::table('customer_verification_documents')
        ->where('id', $existingId)
        ->first();

    expect($storedDocument->original_name)->toBe('replacement-id.png');
});
