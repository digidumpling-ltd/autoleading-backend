<?php

use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Support\Verification;

use function Pest\Laravel\actingAs;

it('prevents unverified customer from adding rental to cart', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_INCOMPLETE,
    ]);

    actingAs($customer, 'customer');

    // Mock product data
    $product = (object) [
        'type' => 'rental',
    ];

    $event = (object) [
        'product' => $product,
    ];

    $listener = new \Webkul\CustomerVerification\Listeners\PreventUnverifiedRentalAddToCartListener();

    expect(function () use ($listener, $event) {
        $listener->handle($event);
    })->toThrow(Exception::class);
});

it('allows verified customer to add rental to cart', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_APPROVED,
    ]);

    actingAs($customer, 'customer');

    $product = (object) [
        'type' => 'rental',
    ];

    $event = (object) [
        'product' => $product,
    ];

    $listener = new \Webkul\CustomerVerification\Listeners\PreventUnverifiedRentalAddToCartListener();

    // Should not throw exception
    $listener->handle($event);

    expect(true)->toBeTrue();
});

it('allows unverified customer to add non-rental product', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_INCOMPLETE,
    ]);

    actingAs($customer, 'customer');

    $product = (object) [
        'type' => 'simple',
    ];

    $event = (object) [
        'product' => $product,
    ];

    $listener = new \Webkul\CustomerVerification\Listeners\PreventUnverifiedRentalAddToCartListener();

    // Should not throw exception
    $listener->handle($event);

    expect(true)->toBeTrue();
});
