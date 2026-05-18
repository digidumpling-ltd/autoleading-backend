<?php

use Webkul\CartRule\Models\CartRule;
use Webkul\CartRule\Models\CartRuleCoupon;
use Webkul\Customer\Models\Customer;

use function Pest\Laravel\get;

it('redirects unauthenticated visitors away from the promotions page', function () {
    get(route('shop.customers.account.promotions.index'))
        ->assertRedirect();
});

it('shows the promotions page to authenticated customers', function () {
    $this->loginAsCustomer();

    get(route('shop.customers.account.promotions.index'))
        ->assertOk()
        ->assertSeeText(trans('customer_promotions::app.customers.account.promotions.title'));
});

it('shows the empty state when no active promotions exist', function () {
    CartRule::query()->delete();

    $this->loginAsCustomer();

    get(route('shop.customers.account.promotions.index'))
        ->assertOk()
        ->assertSeeText(trans('customer_promotions::app.customers.account.promotions.empty-title'));
});

it('lists active promotions with coupon codes for authenticated customers', function () {
    $customer = Customer::factory()->create(['customer_group_id' => 2]);

    $cartRule = CartRule::factory()->create([
        'status'      => 1,
        'coupon_type' => 1,
        'starts_from' => null,
        'ends_till'   => null,
    ]);

    $cartRule->cart_rule_customer_groups()->sync([2]);
    $cartRule->cart_rule_channels()->sync([1]);

    $coupon = CartRuleCoupon::factory()->create([
        'cart_rule_id' => $cartRule->id,
        'is_primary'   => 1,
    ]);

    $this->loginAsCustomer($customer);

    get(route('shop.customers.account.promotions.index'))
        ->assertOk()
        ->assertSee($coupon->code);
});

it('does not list inactive promotions', function () {
    $customer = Customer::factory()->create(['customer_group_id' => 2]);

    $cartRule = CartRule::factory()->create([
        'status'      => 0,
        'coupon_type' => 1,
        'starts_from' => null,
        'ends_till'   => null,
    ]);

    $cartRule->cart_rule_customer_groups()->sync([2]);
    $cartRule->cart_rule_channels()->sync([1]);

    $coupon = CartRuleCoupon::factory()->create([
        'cart_rule_id' => $cartRule->id,
        'is_primary'   => 1,
    ]);

    $this->loginAsCustomer($customer);

    get(route('shop.customers.account.promotions.index'))
        ->assertOk()
        ->assertDontSee($coupon->code);
});

it('does not list expired promotions', function () {
    $customer = Customer::factory()->create(['customer_group_id' => 2]);

    $cartRule = CartRule::factory()->create([
        'status'      => 1,
        'coupon_type' => 1,
        'starts_from' => null,
        'ends_till'   => now()->subDay()->toDateTimeString(),
    ]);

    $cartRule->cart_rule_customer_groups()->sync([2]);
    $cartRule->cart_rule_channels()->sync([1]);

    $coupon = CartRuleCoupon::factory()->create([
        'cart_rule_id' => $cartRule->id,
        'is_primary'   => 1,
    ]);

    $this->loginAsCustomer($customer);

    get(route('shop.customers.account.promotions.index'))
        ->assertOk()
        ->assertDontSee($coupon->code);
});
