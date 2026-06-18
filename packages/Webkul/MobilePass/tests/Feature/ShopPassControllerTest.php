<?php

use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;

function enableMobilePass(): void
{
    CoreConfig::updateOrCreate(
        ['code' => 'sales.mobile_pass.settings.enabled', 'channel_code' => null, 'locale_code' => null],
        ['value' => '1']
    );
}

function disableMobilePass(): void
{
    CoreConfig::updateOrCreate(
        ['code' => 'sales.mobile_pass.settings.enabled', 'channel_code' => null, 'locale_code' => null],
        ['value' => '0']
    );
}

it('saveGoogle route returns 404 when feature is disabled', function () {
    $customer = Customer::factory()->create();

    disableMobilePass();
    config(['mobile-pass.google.issuer_id' => 'test-issuer']);

    $this->actingAs($customer, 'customer')
        ->get(route('shop.customers.account.mobile-pass.google.save'))
        ->assertNotFound();
});

it('saveGoogle route returns 404 when credentials are not set', function () {
    $customer = Customer::factory()->create();

    enableMobilePass();
    config(['mobile-pass.google.issuer_id' => '']);

    $this->actingAs($customer, 'customer')
        ->get(route('shop.customers.account.mobile-pass.google.save'))
        ->assertNotFound();
});
