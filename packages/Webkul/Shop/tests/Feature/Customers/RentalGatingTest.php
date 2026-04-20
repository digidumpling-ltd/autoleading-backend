<?php

use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Listeners\PreventUnverifiedRentalAddToCartListener;
use Webkul\CustomerVerification\Support\Verification;
use Webkul\Product\Repositories\ProductRepository;

use function Pest\Laravel\actingAs;

function makeListener(object $product): PreventUnverifiedRentalAddToCartListener
{
    $repo = Mockery::mock(ProductRepository::class);
    $repo->shouldIgnoreMissing();

    return new PreventUnverifiedRentalAddToCartListener($repo);
}

it('prevents unverified customer from adding rental to cart', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_INCOMPLETE,
    ]);

    actingAs($customer, 'customer');

    $product = (object) ['type' => 'rental'];
    $listener = makeListener($product);

    expect(fn () => $listener->handle($product))->toThrow(Exception::class);
});

it('allows verified customer to add rental to cart', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_APPROVED,
    ]);

    actingAs($customer, 'customer');

    $product = (object) ['type' => 'rental'];
    $listener = makeListener($product);

    $listener->handle($product);

    expect(true)->toBeTrue();
});

it('allows unverified customer to add non-rental product', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_INCOMPLETE,
    ]);

    actingAs($customer, 'customer');

    $product = (object) ['type' => 'simple'];
    $listener = makeListener($product);

    $listener->handle($product);

    expect(true)->toBeTrue();
});
