<?php

use Spatie\LaravelMobilePass\Enums\Platform;
use Spatie\LaravelMobilePass\Models\MobilePass;
use Spatie\LaravelMobilePass\Support\Google\GoogleJwtSigner;
use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;
use Webkul\User\Models\Admin;

function setMobilePassFeature(bool $enabled): void
{
    CoreConfig::updateOrCreate(
        ['code' => 'sales.mobile_pass.settings.enabled', 'channel_code' => null, 'locale_code' => null],
        ['value' => $enabled ? '1' : '0']
    );
}

function makePassForCustomer(int $customerId): MobilePass
{
    return MobilePass::create([
        'pass_serial' => 'admin-card-serial-'.uniqid(),
        'builder_name' => 'loyalty',
        'platform' => Platform::Google,
        'images' => [],
        'content' => [
            'googleClassType' => 'loyaltyClass',
            'googleObjectId' => 'test-issuer.WALLET-'.$customerId,
            'googleClassId' => 'test-issuer.loyalty_class',
            'googleObjectPayload' => [],
        ],
        'model_type' => Customer::class,
        'model_id' => $customerId,
    ]);
}

it('admin pass status card is hidden when feature is disabled', function () {
    $admin = Admin::factory()->create();
    $customer = Customer::factory()->create();

    setMobilePassFeature(false);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.customers.customers.view', $customer->id))
        ->assertOk()
        ->assertDontSee(trans('mobile-pass::app.admin.customers.pass-status-card.title'));
});

it('admin pass status card shows not issued when no pass exists', function () {
    $admin = Admin::factory()->create();
    $customer = Customer::factory()->create();

    setMobilePassFeature(true);

    $this->actingAs($admin, 'admin')
        ->get(route('admin.customers.customers.view', $customer->id))
        ->assertOk()
        ->assertSee(trans('mobile-pass::app.admin.customers.pass-status-card.not-issued'));
});

it('admin pass status card shows issued when pass exists', function () {
    $admin = Admin::factory()->create();
    $customer = Customer::factory()->create();

    setMobilePassFeature(true);
    makePassForCustomer($customer->id);

    $this->mock(GoogleJwtSigner::class, function ($mock) {
        $mock->shouldReceive('signSaveUrlJwt')->andReturn('fake-jwt-token');
    });

    $this->actingAs($admin, 'admin')
        ->get(route('admin.customers.customers.view', $customer->id))
        ->assertOk()
        ->assertSee(trans('mobile-pass::app.admin.customers.pass-status-card.issued'));
});

it('admin can delete a customer wallet pass', function () {
    $admin = Admin::factory()->create();
    $customer = Customer::factory()->create();
    $pass = makePassForCustomer($customer->id);

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.customers.mobile-pass.destroy', $customer->id))
        ->assertRedirect()
        ->assertSessionHas('success', trans('mobile-pass::app.admin.customers.pass-status-card.delete-success'));

    expect(MobilePass::find($pass->id))->toBeNull();
});

it('admin delete is a no-op when customer has no pass', function () {
    $admin = Admin::factory()->create();
    $customer = Customer::factory()->create();

    $this->actingAs($admin, 'admin')
        ->delete(route('admin.customers.mobile-pass.destroy', $customer->id))
        ->assertRedirect()
        ->assertSessionHas('success', trans('mobile-pass::app.admin.customers.pass-status-card.delete-success'));
});
