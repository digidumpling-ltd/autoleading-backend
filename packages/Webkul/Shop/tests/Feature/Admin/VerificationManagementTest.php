<?php

use Illuminate\Support\Facades\DB;
use Webkul\User\Models\Admin;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Models\VerificationAuditLog;
use Webkul\CustomerVerification\Support\Verification;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    $this->admin = Admin::first();
});

it('displays verification dashboard with pending customers', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_PENDING,
    ]);

    actingAs($this->admin, 'admin')
        ->get(route('admin.verification.index'))
        ->assertOk()
        ->assertSeeText($customer->first_name)
        ->assertSeeText($customer->email);
});

it('filters customers by verification status', function () {
    $approved = Customer::factory()->create(['verification_status' => Verification::STATUS_APPROVED]);
    $pending = Customer::factory()->create(['verification_status' => Verification::STATUS_PENDING]);

    actingAs($this->admin, 'admin')
        ->get(route('admin.verification.index', ['status' => 'approved']))
        ->assertOk()
        ->assertSeeText($approved->first_name)
        ->assertDontSeeText($pending->first_name);
});

it('displays customer details with documents', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_PENDING,
    ]);

    DB::table('customer_verification_documents')->insert([
        'customer_id' => $customer->id,
        'type' => Verification::DOCUMENT_TYPE_ID_DOCUMENT,
        'path' => 'customer-documents/1/id_document/test.jpg',
        'original_name' => 'test.jpg',
        'mime' => 'image/jpeg',
        'size' => 1024,
        'status' => 'pending',
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    actingAs($this->admin, 'admin')
        ->get(route('admin.verification.show', $customer->id))
        ->assertOk()
        ->assertSeeText($customer->first_name)
        ->assertSeeText('ID Document');
});

it('approves a customer and updates status', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_PENDING,
    ]);

    actingAs($this->admin, 'admin')
        ->post(route('admin.verification.approve', $customer->id))
        ->assertRedirectToRoute('admin.verification.index')
        ->assertSessionHas('success');

    expect($customer->fresh()->verification_status)->toBe(Verification::STATUS_APPROVED);
    expect(VerificationAuditLog::where('customer_id', $customer->id)->where('action', 'approved')->exists())->toBeTrue();
});

it('rejects a customer with reason', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_PENDING,
    ]);

    actingAs($this->admin, 'admin')
        ->post(route('admin.verification.reject', $customer->id), [
            'rejection_reason' => 'Document not clear enough',
        ])
        ->assertRedirectToRoute('admin.verification.index')
        ->assertSessionHas('success');

    $customer->refresh();
    expect($customer->verification_status)->toBe(Verification::STATUS_REJECTED);
    expect($customer->rejection_reason)->toBe('Document not clear enough');
    expect(VerificationAuditLog::where('customer_id', $customer->id)->where('action', 'rejected')->first()->reason)
        ->toBe('Document not clear enough');
});

it('requires rejection reason when rejecting', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_PENDING,
    ]);

    actingAs($this->admin, 'admin')
        ->post(route('admin.verification.reject', $customer->id), [
            'rejection_reason' => '',
        ])
        ->assertRedirect()
        ->assertSessionHasErrors('rejection_reason');
});
