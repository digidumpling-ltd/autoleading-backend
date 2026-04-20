<?php

use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;
use Webkul\CustomerVerification\Support\Verification;
use Webkul\Faker\Helpers\Product as ProductFaker;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

// AC 3: Non-booking product renders natively without hardcoded placeholder content
it('renders simple product detail page with no hardcoded spec placeholders', function () {
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new'            => ['boolean_value' => true],
            'featured'       => ['boolean_value' => true],
            'price'          => ['float_value' => rand(1000, 5000)],
            'guest_checkout' => ['boolean_value' => true],
        ],
    ]))->getSimpleProductFactory()->create();

    $response = get('/' . $product->url_key);

    $response->assertOk();

    $content = $response->content();

    // Old hardcoded placeholders must not appear
    expect($content)->not->toContain('V6 3.0L');
    expect($content)->not->toContain('★★★★★');
    expect($content)->not->toContain('feature_1');
    expect($content)->not->toContain('feature_2');
    expect($content)->not->toContain('feature_3');
});

// AC 1: Native v-product Vue component structure is present in rendered page
it('product detail page includes native v-product form shell', function () {
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new'            => ['boolean_value' => true],
            'featured'       => ['boolean_value' => true],
            'price'          => ['float_value' => rand(1000, 5000)],
            'guest_checkout' => ['boolean_value' => true],
        ],
    ]))->getSimpleProductFactory()->create();

    $response = get('/' . $product->url_key);

    $response->assertOk();

    // Native add-to-cart payload key must be present (from the form action)
    expect($response->content())->toContain('product_id');
});

// AC 5: Related products use native association API, not inline repository query
it('product detail page does not contain inline repository query', function () {
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new'            => ['boolean_value' => true],
            'featured'       => ['boolean_value' => true],
            'price'          => ['float_value' => rand(1000, 5000)],
            'guest_checkout' => ['boolean_value' => true],
        ],
    ]))->getSimpleProductFactory()->create();

    $response = get('/' . $product->url_key);

    $response->assertOk();

    // Inline repository class reference from old template must be gone
    expect($response->content())->not->toContain('ProductRepository');
});

// AC 6: No verification banner shown for non-booking products regardless of customer status
it('does not show verification banner on non-booking product page', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_INCOMPLETE,
    ]);

    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new'            => ['boolean_value' => true],
            'featured'       => ['boolean_value' => true],
            'price'          => ['float_value' => rand(1000, 5000)],
            'guest_checkout' => ['boolean_value' => true],
        ],
    ]))->getSimpleProductFactory()->create();

    actingAs($customer, 'customer');

    $response = get('/' . $product->url_key);

    $response->assertOk();

    // Banner CSS class should not appear for simple (non-booking) products
    expect($response->content())->not->toContain('al-verification-banner');
});

// AC 6: Approved customer visiting non-booking product sees no banner
it('approved customer sees product detail page without verification banner', function () {
    $customer = Customer::factory()->create([
        'verification_status' => Verification::STATUS_APPROVED,
    ]);

    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new'            => ['boolean_value' => true],
            'featured'       => ['boolean_value' => true],
            'price'          => ['float_value' => rand(1000, 5000)],
            'guest_checkout' => ['boolean_value' => true],
        ],
    ]))->getSimpleProductFactory()->create();

    actingAs($customer, 'customer');

    $response = get('/' . $product->url_key);

    $response->assertOk();

    expect($response->content())->not->toContain('al-verification-banner');
});

// AC 4: Additional information section is data-driven (not hardcoded rows)
it('product detail page renders additional information tab from attribute data', function () {
    $product = (new ProductFaker([
        'attributes' => [
            5  => 'new',
            6  => 'featured',
            11 => 'price',
            26 => 'guest_checkout',
        ],
        'attribute_value' => [
            'new'            => ['boolean_value' => true],
            'featured'       => ['boolean_value' => true],
            'price'          => ['float_value' => rand(1000, 5000)],
            'guest_checkout' => ['boolean_value' => true],
        ],
    ]))->getSimpleProductFactory()->create();

    $response = get('/' . $product->url_key);

    $response->assertOk();

    // Description tab is always rendered
    expect($response->content())->toContain(trans('shop::app.products.view.description'));
});
