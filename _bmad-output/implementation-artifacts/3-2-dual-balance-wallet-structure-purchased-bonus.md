# Story 3.2: Attach Wallet to Customer Model

Status: review

## Story

As a developer,
I want to attach `bavix/laravel-wallet`'s wallet capability to the Customer model via Bagisto's Proxy/Concord pattern,
so that any Customer instance can deposit, withdraw, and query a wallet balance without modifying core packages.

> **Scope note (revised from original):** Original story title referred to a dual-balance (purchased/bonus) structure. Per product decision, this is simplified to a **single-balance wallet** — straightforward top-up and deduction. Reward/bonus points will be handled separately by the Rewards package when it is re-enabled.

## Acceptance Criteria

1. **Given** the Wallet package boots, **when** I resolve a `Customer` model instance, **then** it implements `Bavix\Wallet\Interfaces\Wallet` and `Bavix\Wallet\Interfaces\WalletFloat` via the `HasWalletFloat` trait.

2. **Given** a verified customer exists, **when** I call `$customer->depositFloat(100.00)`, **then** `$customer->balanceFloatNum` equals `100.00` and a `deposit` transaction record exists in the `transactions` table.

3. **Given** a customer with balance `100.00`, **when** I call `$customer->withdrawFloat(40.00)`, **then** `$customer->balanceFloatNum` equals `60.00`.

4. **Given** a customer with balance `50.00`, **when** I call `$customer->canWithdrawFloat(200.00)`, **then** it returns `false` and balance remains `50.00`. (`safeWithdrawFloat` does not exist in bavix/laravel-wallet v11 — use `canWithdrawFloat()` as the guard before calling `withdrawFloat()`.)

5. **Given** the Concord binding is registered, **when** I call `CustomerProxy::modelClass()`, **then** it returns `Webkul\Wallet\Models\Customer`.

6. **Given** Pest tests exist, **when** I run `vendor/bin/pest packages/Webkul/Wallet/tests/`, **then** all wallet operation tests pass.

## Tasks / Subtasks

- [x] Task 1: Create Customer proxy model with HasWalletFloat trait (AC: 1, 5)
  - [x] Create `packages/Webkul/Wallet/src/Models/Customer.php` extending `Webkul\Customer\Models\Customer`
  - [x] Implement `Bavix\Wallet\Interfaces\Wallet` and `Bavix\Wallet\Interfaces\WalletFloat`
  - [x] Add `use HasWalletFloat` trait (which includes `HasWallet` internally)

- [x] Task 2: Register Customer model binding via Concord (AC: 5)
  - [x] Override `boot()` in `Webkul\Wallet\Providers\ModuleServiceProvider`
  - [x] Call `$this->app->concord->registerModel(\Webkul\Customer\Contracts\Customer::class, \Webkul\Wallet\Models\Customer::class)`

- [x] Task 3: Write Pest feature tests (AC: 2, 3, 4, 6)
  - [x] Test: `depositFloat(100)` → `balanceFloatNum === 100.0` and transaction exists
  - [x] Test: `withdrawFloat(40)` from 100 balance → `balanceFloatNum === 60.0`
  - [x] Test: `canWithdrawFloat(200)` from 50 balance → returns `false`, balance unchanged (note: `safeWithdrawFloat` does not exist in v11; use `canWithdrawFloat` as the guard)

## Dev Notes

### Customer Model Extension Pattern

Bagisto uses Konekt/Concord for model proxying. The last registered binding for a contract wins. Since the Rewards package is **currently disabled**, the current binding for `\Webkul\Customer\Contracts\Customer::class` is the base `Webkul\Customer\Models\Customer`.

Create the proxy model:
```php
namespace Webkul\Wallet\Models;

use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Interfaces\WalletFloat;
use Bavix\Wallet\Traits\HasWalletFloat;
use Webkul\Customer\Models\Customer as BaseCustomer;

class Customer extends BaseCustomer implements Wallet, WalletFloat
{
    use HasWalletFloat;
}
```

### Registering via Concord in ModuleServiceProvider

`ModuleServiceProvider` extends `Konekt\Concord\BaseModuleServiceProvider`. Override `boot()`:

```php
public function boot(): void
{
    parent::boot();

    $this->app->concord->registerModel(
        \Webkul\Customer\Contracts\Customer::class,
        \Webkul\Wallet\Models\Customer::class
    );
}
```

> **Warning for future:** When the Rewards package is re-enabled, `Webkul\Wallet\Models\Customer` must extend `Webkul\Rewards\Models\Customer` (not the base) to preserve the trait chain. The Wallet package's `ModuleServiceProvider` must also be loaded **after** Rewards in `config/concord.php`.

### HasWalletFloat vs HasWallet

Use `HasWalletFloat` (not `HasWallet`) — it includes `HasWallet` internally and adds float-based convenience methods:
- `depositFloat(float $amount)` → accepts `10.50` instead of integer cents
- `withdrawFloat(float $amount)`
- `safeWithdrawFloat(float $amount)` → returns `null` on insufficient funds (no exception)
- `balanceFloat` → string representation of balance
- `balanceFloatNum` → float representation

### Tests

Use `WalletTestCase` (already exists from Story 3.1). Tests need a real database — use `RefreshDatabase` or Pest's `pest()->extend()` transaction handling. Create a Customer via factory, call wallet operations directly.

```php
it('customer can deposit to wallet', function () {
    $customer = Customer::factory()->create(['verification_status' => 'approved']);
    $customer->depositFloat(100.00);
    expect($customer->fresh()->balanceFloatNum)->toBe(100.0);
});
```

Note: Call `$customer->fresh()` after mutations to reload the balance from DB (bavix wallet caches balance on the model instance).

### Files to Create/Modify

- `packages/Webkul/Wallet/src/Models/Customer.php` (new)
- `packages/Webkul/Wallet/src/Providers/ModuleServiceProvider.php` (modify — add boot())
- `packages/Webkul/Wallet/tests/Feature/WalletCustomerModelTest.php` (new)

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Debug Log

### Completion Notes

- Created `Webkul\Wallet\Models\Customer` extending base Customer with `HasWalletFloat` trait (includes `HasWallet`); implements `Wallet` and `WalletFloat` interfaces
- Registered model binding via `ModuleServiceProvider::boot()` → `concord->registerModel(\Webkul\Customer\Contracts\Customer::class, \Webkul\Wallet\Models\Customer::class)`
- `safeWithdrawFloat` does not exist in bavix/laravel-wallet v11 — AC 4 updated to use `canWithdrawFloat()` instead
- WalletPaymentAvailabilityTest updated from `factory()->create()` to `updateOrCreate()` for CoreConfig to handle pre-existing DB records robustly
- All 10 Wallet tests pass (5 new + 5 from Story 3.1)
- bavix wallet tables (`autlwallets`, `autltransactions`, etc.) confirmed present after running pending migrations

### File List

- `packages/Webkul/Wallet/src/Models/Customer.php` (new)
- `packages/Webkul/Wallet/src/Providers/ModuleServiceProvider.php` (modified — added boot() with registerModel)
- `packages/Webkul/Wallet/tests/Feature/WalletCustomerModelTest.php` (new)
- `packages/Webkul/Wallet/tests/Feature/WalletPaymentAvailabilityTest.php` (modified — factory→updateOrCreate for robustness)

## Change Log

- Attached bavix/laravel-wallet to Customer model via Concord proxy; single-balance wallet with depositFloat/withdrawFloat/canWithdrawFloat; 5 Pest tests added (Date: 2026-04-23)
