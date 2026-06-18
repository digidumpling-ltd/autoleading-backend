<?php

use Spatie\LaravelMobilePass\Enums\Platform;
use Spatie\LaravelMobilePass\Models\MobilePass;
use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;
use Webkul\MobilePass\Services\MobilePassService;

function makeGooglePass(array $attrs = []): MobilePass
{
    return MobilePass::create(array_merge([
        'pass_serial' => 'test-serial-'.uniqid(),
        'builder_name' => 'loyalty',
        'platform' => Platform::Google,
        'images' => [],
        'content' => [],
        'model_type' => null,
        'model_id' => null,
    ], $attrs));
}

beforeEach(function () {
    config(['mobile-pass.google.issuer_id' => 'test-issuer-123']);
});

it('createOrGetGooglePass returns existing pass without creating a new one', function () {
    $customer = Customer::factory()->create();

    $existing = makeGooglePass([
        'model_type' => Customer::class,
        'model_id' => $customer->id,
    ]);

    $service = new MobilePassService;
    $result = $service->createOrGetGooglePass($customer);

    expect($result->id)->toBe($existing->id);
    expect(MobilePass::where('model_id', $customer->id)->where('platform', Platform::Google)->count())->toBe(1);
});

it('getCustomerGooglePass returns null when no pass exists', function () {
    $customer = Customer::factory()->create();

    $service = new MobilePassService;

    expect($service->getCustomerGooglePass($customer->id))->toBeNull();
});

it('getCustomerGooglePass returns existing Google pass', function () {
    $customer = Customer::factory()->create();

    $pass = makeGooglePass([
        'model_type' => Customer::class,
        'model_id' => $customer->id,
    ]);

    $service = new MobilePassService;

    expect($service->getCustomerGooglePass($customer->id)?->id)->toBe($pass->id);
});

it('isEnabled returns false when explicitly disabled', function () {
    CoreConfig::updateOrCreate(
        ['code' => 'sales.mobile_pass.settings.enabled', 'channel_code' => null, 'locale_code' => null],
        ['value' => '0']
    );

    $service = new MobilePassService;

    expect($service->isEnabled())->toBeFalse();
});

it('hasGoogleCredentials returns false when issuer_id is empty', function () {
    config(['mobile-pass.google.issuer_id' => '']);

    $service = new MobilePassService;

    expect($service->hasGoogleCredentials())->toBeFalse();
});

it('hasGoogleCredentials returns true when both issuer_id and class_suffix are set', function () {
    $service = new MobilePassService;

    expect($service->hasGoogleCredentials())->toBeTrue();
});
