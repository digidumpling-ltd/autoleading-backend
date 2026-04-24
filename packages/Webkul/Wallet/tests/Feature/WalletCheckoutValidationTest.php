<?php

use Webkul\Checkout\Models\Cart;
use Webkul\Checkout\Models\CartAddress;
use Webkul\Checkout\Models\CartItem;
use Webkul\Checkout\Models\CartPayment;
use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\Faker\Helpers\Product as ProductFaker;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Services\WalletService;

use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

// ---------------------------------------------------------------------------
// WalletService unit tests
// ---------------------------------------------------------------------------

it('WalletService canAfford returns true when balance equals amount', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(200.00);

    $service = new WalletService;

    expect($service->canAfford($customer->fresh(), 200.00))->toBeTrue();
});

it('WalletService canAfford returns true when balance exceeds amount', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(300.00);

    $service = new WalletService;

    expect($service->canAfford($customer->fresh(), 200.00))->toBeTrue();
});

it('WalletService canAfford returns false when balance is less than amount', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    $service = new WalletService;

    expect($service->canAfford($customer->fresh(), 200.00))->toBeFalse();
});

it('WalletService canAfford returns false when balance is zero', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);

    $service = new WalletService;

    expect($service->canAfford($customer, 100.00))->toBeFalse();
});

it('WalletService shortfall returns zero when balance is sufficient', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(300.00);

    $service = new WalletService;

    expect($service->shortfall($customer->fresh(), 200.00))->toBe(0.0);
});

it('WalletService shortfall returns correct difference when balance is insufficient', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    $service = new WalletService;

    expect($service->shortfall($customer->fresh(), 200.00))->toBe(150.0);
});

// ---------------------------------------------------------------------------
// WalletCheckoutController status endpoint
// ---------------------------------------------------------------------------

it('checkout status endpoint returns 401 for unauthenticated requests', function () {
    $response = getJson(route('shop.wallet.checkout.status'));

    $response->assertStatus(401);
});

it('checkout status endpoint returns can_afford true when balance is sufficient', function () {
    $base     = BaseCustomer::factory()->create(['verification_status' => 'approved']);
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    $this->actingAs($base, 'customer');

    $cart = Cart::factory()->create([
        'customer_id'      => $base->id,
        'customer_email'   => $base->email,
        'is_guest'         => 0,
        'grand_total'      => 200.00,
        'base_grand_total' => 200.00,
    ]);

    CartPayment::factory()->create([
        'cart_id' => $cart->id,
        'method'  => 'wallet',
    ]);

    cart()->setCart($cart);

    $response = getJson(route('shop.wallet.checkout.status'));

    $response->assertOk()
        ->assertJsonPath('can_afford', true)
        ->assertJsonPath('shortfall', 0);
});

it('checkout status endpoint returns can_afford false when balance is insufficient', function () {
    $base     = BaseCustomer::factory()->create(['verification_status' => 'approved']);
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(50.00);

    $this->actingAs($base, 'customer');

    $cart = Cart::factory()->create([
        'customer_id'      => $base->id,
        'customer_email'   => $base->email,
        'is_guest'         => 0,
        'grand_total'      => 200.00,
        'base_grand_total' => 200.00,
    ]);

    CartPayment::factory()->create([
        'cart_id' => $cart->id,
        'method'  => 'wallet',
    ]);

    cart()->setCart($cart);

    $response = getJson(route('shop.wallet.checkout.status'));

    $response->assertOk()
        ->assertJsonPath('can_afford', false);

    expect($response->json('shortfall'))->toBeGreaterThan(0.0);
});

// ---------------------------------------------------------------------------
// WalletCheckoutMiddleware — server-side order placement validation
// ---------------------------------------------------------------------------

it('storeOrder rejects wallet order when customer has insufficient balance', function () {
    $product = (new ProductFaker([
        'attributes' => [
            5 => 'new',
            6 => 'featured',
        ],

        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],

            'featured' => [
                'boolean_value' => true,
            ],
        ],
    ]))
        ->getVirtualProductFactory()
        ->create();

    $base     = BaseCustomer::factory()->create([
        'verification_status' => 'approved',
        'status'              => 1,
        'is_suspended'        => 0,
    ]);
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(10.00);

    $this->actingAs($base, 'customer');

    $price = $product->price > 50 ? $product->price : 100;

    $cart = Cart::factory()->create([
        'customer_id'         => $base->id,
        'customer_first_name' => $base->first_name,
        'customer_last_name'  => $base->last_name,
        'customer_email'      => $base->email,
        'is_guest'            => 0,
    ]);

    $additional = [
        'product_id' => $product->id,
        'rating'     => '0',
        'is_buy_now' => '0',
        'quantity'   => '1',
    ];

    CartItem::factory()->create([
        'cart_id'              => $cart->id,
        'product_id'           => $product->id,
        'sku'                  => $product->sku,
        'quantity'             => 1,
        'name'                 => $product->name,
        'price'                => $convertedPrice = core()->convertPrice($price),
        'price_incl_tax'       => $convertedPrice,
        'base_price'           => $price,
        'base_price_incl_tax'  => $price,
        'total'                => $convertedPrice,
        'total_incl_tax'       => $convertedPrice,
        'base_total'           => $price,
        'weight'               => 0,
        'total_weight'         => 0,
        'base_total_weight'    => 0,
        'type'                 => $product->type,
        'additional'           => $additional,
    ]);

    CartAddress::factory()->create([
        'cart_id'      => $cart->id,
        'address_type' => CartAddress::ADDRESS_TYPE_BILLING,
        'customer_id'  => $base->id,
    ]);

    CartPayment::factory()->create([
        'cart_id' => $cart->id,
        'method'  => 'wallet',
    ]);

    cart()->setCart($cart);
    cart()->collectTotals();

    $response = postJson(route('shop.checkout.onepage.orders.store'));

    $response->assertStatus(500);

    expect($response->json('message'))->toContain('Insufficient wallet balance');
});

it('storeOrder passes through for non-wallet payment methods', function () {
    $product = (new ProductFaker([
        'attributes' => [
            5 => 'new',
            6 => 'featured',
        ],

        'attribute_value' => [
            'new' => [
                'boolean_value' => true,
            ],

            'featured' => [
                'boolean_value' => true,
            ],
        ],
    ]))
        ->getVirtualProductFactory()
        ->create();

    $base = BaseCustomer::factory()->create([
        'verification_status' => 'approved',
        'status'              => 1,
        'is_suspended'        => 0,
    ]);

    $this->actingAs($base, 'customer');

    $price = $product->price;

    $cart = Cart::factory()->create([
        'customer_id'         => $base->id,
        'customer_first_name' => $base->first_name,
        'customer_last_name'  => $base->last_name,
        'customer_email'      => $base->email,
        'is_guest'            => 0,
    ]);

    $additional = [
        'product_id' => $product->id,
        'rating'     => '0',
        'is_buy_now' => '0',
        'quantity'   => '1',
    ];

    CartItem::factory()->create([
        'cart_id'              => $cart->id,
        'product_id'           => $product->id,
        'sku'                  => $product->sku,
        'quantity'             => 1,
        'name'                 => $product->name,
        'price'                => $convertedPrice = core()->convertPrice($price),
        'price_incl_tax'       => $convertedPrice,
        'base_price'           => $price,
        'base_price_incl_tax'  => $price,
        'total'                => $convertedPrice,
        'total_incl_tax'       => $convertedPrice,
        'base_total'           => $price,
        'weight'               => 0,
        'total_weight'         => 0,
        'base_total_weight'    => 0,
        'type'                 => $product->type,
        'additional'           => $additional,
    ]);

    CartAddress::factory()->create([
        'cart_id'      => $cart->id,
        'address_type' => CartAddress::ADDRESS_TYPE_BILLING,
        'customer_id'  => $base->id,
    ]);

    CartPayment::factory()->create([
        'cart_id' => $cart->id,
        'method'  => 'cashondelivery',
    ]);

    cart()->setCart($cart);
    cart()->collectTotals();

    $response = postJson(route('shop.checkout.onepage.orders.store'));

    // Middleware must not block non-wallet orders with a wallet balance 500
    $walletBlocked = $response->status() === 500
        && str_contains((string) $response->json('message'), 'Insufficient wallet balance');

    expect($walletBlocked)->toBeFalse();
});
