<?php

use Illuminate\Support\Facades\Queue;
use Spatie\LaravelMobilePass\Enums\Platform;
use Spatie\LaravelMobilePass\Jobs\PushPassUpdateJob;
use Spatie\LaravelMobilePass\Models\MobilePass;
use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer;
use Webkul\MobilePass\Listeners\SyncGooglePassBalance;
use Webkul\MobilePass\Services\MobilePassService;
use Webkul\Wallet\Events\WalletBalanceUpdated;

function setMobilePassEnabled(bool $enabled): void
{
    CoreConfig::updateOrCreate(
        ['code' => 'sales.mobile_pass.settings.enabled', 'channel_code' => null, 'locale_code' => null],
        ['value' => $enabled ? '1' : '0']
    );
}

function makeGooglePassForSync(int $customerId, array $content = []): MobilePass
{
    return MobilePass::create([
        'pass_serial'  => 'sync-serial-'.uniqid(),
        'builder_name' => 'loyalty',
        'platform'     => Platform::Google,
        'images'       => [],
        'content'      => $content,
        'model_type'   => Customer::class,
        'model_id'     => $customerId,
    ]);
}

it('SyncGooglePassBalance updates loyaltyPoints and queues a push update', function () {
    Queue::fake();

    $customer = Customer::factory()->create();
    $pass = makeGooglePassForSync($customer->id, []);

    setMobilePassEnabled(true);

    $service = new MobilePassService;
    $listener = new SyncGooglePassBalance($service);

    $listener->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 100.0,
        newBalance: 150.0,
        reason: 'wallet_topup',
    ));

    $pass->refresh();
    expect($pass->content['googleObjectPayload']['loyaltyPoints'])->toHaveKey('balance');

    Queue::assertPushed(PushPassUpdateJob::class);
});

it('SyncGooglePassBalance skips update when feature is disabled', function () {
    Queue::fake();

    $customer = Customer::factory()->create();

    $originalContent = ['googleObjectPayload' => ['loyaltyPoints' => ['balance' => ['string' => '$100.00']]]];
    $pass = makeGooglePassForSync($customer->id, $originalContent);

    setMobilePassEnabled(false);

    $service = new MobilePassService;
    $listener = new SyncGooglePassBalance($service);

    $listener->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 100.0,
        newBalance: 200.0,
        reason: 'wallet_topup',
    ));

    $pass->refresh();
    expect($pass->content['googleObjectPayload']['loyaltyPoints']['balance']['string'])->toBe('$100.00');

    Queue::assertNothingPushed();
});

it('SyncGooglePassBalance skips when customer has no pass', function () {
    Queue::fake();

    $customer = Customer::factory()->create();

    setMobilePassEnabled(true);

    $service = new MobilePassService;
    $listener = new SyncGooglePassBalance($service);

    $listener->handle(new WalletBalanceUpdated(
        customerId: $customer->id,
        oldBalance: 0.0,
        newBalance: 50.0,
        reason: 'wallet_topup',
    ));

    expect(MobilePass::where('model_id', $customer->id)->count())->toBe(0);

    Queue::assertNothingPushed();
});
