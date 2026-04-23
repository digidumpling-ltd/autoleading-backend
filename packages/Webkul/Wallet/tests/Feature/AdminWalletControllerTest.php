<?php

use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\User\Models\Admin;
use Webkul\User\Models\Role;
use Webkul\Wallet\Models\Customer as WalletCustomer;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

function createAdminWithRole(string $permissionType, array $permissions = []): Admin
{
    $role = Role::create([
        'name'            => 'Test Role ' . uniqid(),
        'permission_type' => $permissionType,
        'permissions'     => $permissions,
    ]);

    return Admin::factory()->create(['role_id' => $role->id]);
}

function createWalletCustomer(): WalletCustomer
{
    $base = BaseCustomer::factory()->create();

    return WalletCustomer::find($base->id);
}

it('admin with permission can view customer wallet page', function () {
    $admin = createAdminWithRole('all');
    $customer = createWalletCustomer();
    $customer->depositFloat(150.00);

    actingAs($admin, 'admin')
        ->get(route('admin.customers.wallet.index', $customer->id))
        ->assertOk()
        ->assertSeeText($customer->first_name);
});

it('admin can add credit to customer wallet', function () {
    $admin = createAdminWithRole('all');
    $customer = createWalletCustomer();

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'add',
            'amount' => '200.00',
            'reason' => 'Test manual grant',
        ])
        ->assertRedirect(route('admin.customers.wallet.index', $customer->id));

    expect($customer->fresh()->balanceFloatNum)->toBe(200.0);

    $transaction = $customer->transactions()->latest()->first();
    expect($transaction->type)->toBe('deposit')
        ->and($transaction->meta['type'] ?? null)->toBe('admin_grant')
        ->and($transaction->meta['admin_id'] ?? null)->toBe($admin->id)
        ->and($transaction->meta['reason'] ?? null)->toBe('Test manual grant');
});

it('admin can deduct credit when customer has sufficient balance', function () {
    $admin = createAdminWithRole('all');
    $customer = createWalletCustomer();
    $customer->depositFloat(300.00);

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'deduct',
            'amount' => '100.00',
            'reason' => 'Dispute resolution',
        ])
        ->assertRedirect(route('admin.customers.wallet.index', $customer->id));

    expect($customer->fresh()->balanceFloatNum)->toBe(200.0);

    $transaction = $customer->transactions()->where('type', 'withdraw')->latest()->first();
    expect($transaction->meta['type'] ?? null)->toBe('admin_deduct')
        ->and($transaction->meta['admin_id'] ?? null)->toBe($admin->id);
});

it('deduction fails with error when balance is insufficient', function () {
    $admin = createAdminWithRole('all');
    $customer = createWalletCustomer();
    $customer->depositFloat(50.00);

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'deduct',
            'amount' => '500.00',
            'reason' => 'Attempting over-deduct',
        ])
        ->assertSessionHasErrors(['amount']);

    expect($customer->fresh()->balanceFloatNum)->toBe(50.0);
});

it('admin without customers.wallet permission gets 401', function () {
    $admin = createAdminWithRole('custom', ['dashboard']);
    $customer = createWalletCustomer();

    actingAs($admin, 'admin')
        ->get(route('admin.customers.wallet.index', $customer->id))
        ->assertStatus(401);
});

it('adjust endpoint returns 401 for admin without permission', function () {
    $admin = createAdminWithRole('custom', ['dashboard']);
    $customer = createWalletCustomer();

    actingAs($admin, 'admin')
        ->post(route('admin.customers.wallet.adjust', $customer->id), [
            'type'   => 'add',
            'amount' => '100.00',
            'reason' => 'Unauthorized attempt',
        ])
        ->assertStatus(401);
});
