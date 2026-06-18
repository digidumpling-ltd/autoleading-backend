<?php

use Webkul\Customer\Models\Customer;
use Webkul\User\Models\Admin;

it('admin POS lookup returns customer data for valid ID', function () {
    $admin = Admin::factory()->create();
    $customer = Customer::factory()->create([
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ]);

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.api.customers.mobile-pass.lookup', $customer->id))
        ->assertOk()
        ->assertJsonStructure(['data' => ['id', 'name', 'balance']])
        ->assertJsonFragment(['id' => $customer->id, 'name' => 'Jane Doe']);
});

it('admin POS lookup returns 404 for unknown customer ID', function () {
    $admin = Admin::factory()->create();

    $this->actingAs($admin, 'admin')
        ->getJson(route('admin.api.customers.mobile-pass.lookup', 99999))
        ->assertNotFound();
});
