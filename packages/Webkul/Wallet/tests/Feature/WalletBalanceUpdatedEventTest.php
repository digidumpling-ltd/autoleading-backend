<?php

use Illuminate\Support\Facades\Event;
use Webkul\Core\Models\CoreConfig;
use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\User\Models\Admin;
use Webkul\User\Models\Role;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;

use function Pest\Laravel\actingAs;

function makeAdminAllPerms(): Admin
{
    $role = Role::create([
        'name'            => 'All ' . uniqid(),
        'permission_type' => 'all',
        'permissions'     => [],
    ]);

    return Admin::factory()->create(['role_id' => $role->id]);
}

function makeWalletCustomer(): WalletCustomer
{
    return WalletCustomer::find(BaseCustomer::factory()->create()->id);
}

function enableBalanceUpdatedEvent(): void
{
    CoreConfig::create([
        'code'         => 'sales.wallet.events.publish_balance_updated',
        'value'        => '1',
        'channel_code' => null,
        'locale_code'  => null,
    ]);
}

it('dispatches WalletBalanceUpdated after admin add when event publishing is enabled', function () {
    Event::fake([WalletBalanceUpdated::class]);

    enableBalanceUpdatedEvent();

    $admin    = makeAdminAllPerms();
    $customer = makeWalletCustomer();

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'add',
            'amount' => '100.00',
            'reason' => 'Test top-up event',
        ]);

    Event::assertDispatched(WalletBalanceUpdated::class, function ($event) use ($customer) {
        return $event->customerId === $customer->id
            && $event->oldBalance === 0.0
            && $event->newBalance === 100.0
            && $event->reason === 'admin_grant';
    });
});

it('does not dispatch WalletBalanceUpdated when event publishing is disabled', function () {
    Event::fake([WalletBalanceUpdated::class]);

    // Override the seeded '1' entry and flush the L5-Repository array cache
    CoreConfig::where('code', 'sales.wallet.events.publish_balance_updated')->update(['value' => '0']);
    \Illuminate\Support\Facades\Cache::flush();

    // CoreConfig value '0' → falsy → no dispatch

    $admin    = makeAdminAllPerms();
    $customer = makeWalletCustomer();

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'add',
            'amount' => '50.00',
            'reason' => 'Event disabled test',
        ]);

    Event::assertNotDispatched(WalletBalanceUpdated::class);
});

it('dispatches WalletBalanceUpdated after admin deduct when event publishing is enabled', function () {
    Event::fake([WalletBalanceUpdated::class]);

    enableBalanceUpdatedEvent();

    $admin    = makeAdminAllPerms();
    $customer = makeWalletCustomer();
    $customer->depositFloat(200.0);

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'deduct',
            'amount' => '80.00',
            'reason' => 'Deduction event test',
        ]);

    Event::assertDispatched(WalletBalanceUpdated::class, function ($event) use ($customer) {
        return $event->customerId === $customer->id
            && $event->oldBalance === 200.0
            && $event->newBalance === 120.0
            && $event->reason === 'admin_deduct';
    });
});

it('event payload contains correct fields', function () {
    $event = new WalletBalanceUpdated(
        customerId: 42,
        oldBalance: 150.0,
        newBalance: 250.0,
        reason: 'wallet_topup',
    );

    expect($event->customerId)->toBe(42)
        ->and($event->oldBalance)->toBe(150.0)
        ->and($event->newBalance)->toBe(250.0)
        ->and($event->reason)->toBe('wallet_topup');
});
