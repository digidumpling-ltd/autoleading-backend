# Story 3.1: Install and Configure bavix/laravel-wallet

Status: review

## Story

As a developer,
I want to install and configure `bavix/laravel-wallet` within the Bagisto package system,
so that the wallet engine is available as a foundation for all credit operations.

## Acceptance Criteria

1. **Given** I run the package installation, **when** `composer require bavix/laravel-wallet` completes, **then** the package is listed as a dependency, migrations have been published, and `php artisan migrate` creates `wallets`, `transactions`, and `transfers` tables without errors.

2. **Given** the Wallet package is scaffolded, **when** I inspect `packages/Webkul/Wallet/src/`, **then** the following exist:
   - `Providers/WalletServiceProvider.php` (registered in `bootstrap/providers.php`)
   - `Providers/ModuleServiceProvider.php` (registered in `config/concord.php`)
   - `Config/payment-methods.php` (registers `WalletPayment`)
   - `Payment/WalletPayment.php` (extends Bagisto's `Payment` abstract class)
   - PSR-4 namespace `Webkul\Wallet` added to root `composer.json`

3. **Given** the Wallet package boots, **when** the application initializes, **then** `WalletPayment` is the **only** active payment method: all other methods (Stripe, Razorpay, CashOnDelivery, MoneyTransfer) are forced to `active = false` via `Config::set()` in `WalletServiceProvider::boot()`.

4. **Given** `WalletPayment::isAvailable()` is called, **when** the customer is unauthenticated or their `verification_status !== 'approved'`, **then** `isAvailable()` returns `false`; **when** authenticated AND approved, it returns `true`.

5. **Given** `bavix/laravel-wallet` config is published, **when** I inspect the published config, **then** `decimal_places` is aligned with Bagisto's currency (default: 2).

6. **Given** I run `composer dump-autoload && php artisan optimize:clear`, **when** no errors are thrown, **then** the Wallet package is recognized by Laravel and the `wallets`, `transactions`, `transfers` tables are present.

7. **Given** Pest tests exist, **when** I run `vendor/bin/pest packages/Webkul/Wallet/tests/`, **then**:
   - `config('payment_methods')` contains only `wallet` key with `active = true` after boot
   - `WalletPayment::isAvailable()` returns `false` for unauthenticated requests
   - `WalletPayment::isAvailable()` returns `false` for unverified customers
   - `WalletPayment::isAvailable()` returns `true` for verified + authenticated customers

## Tasks / Subtasks

- [x] Task 1: Install bavix/laravel-wallet and publish migrations (AC: 1, 5, 6)
  - [x] Run `composer require bavix/laravel-wallet:^11.0` from project root
  - [x] Run `php artisan vendor:publish --tag=laravel-wallet-migrations` to publish migrations
  - [x] Run `php artisan migrate` and confirm `wallets`, `transactions`, `transfers` tables exist
  - [x] Confirm `decimal_places` defaults to 2 (check published migration for wallets table)

- [x] Task 2: Scaffold the Webkul/Wallet package structure (AC: 2)
  - [x] Create directory: `packages/Webkul/Wallet/src/`
  - [x] Create `Providers/WalletServiceProvider.php` (see Dev Notes for exact structure)
  - [x] Create `Providers/ModuleServiceProvider.php`
  - [x] Create `Config/payment-methods.php` registering `WalletPayment`
  - [x] Create `Payment/WalletPayment.php` extending `Webkul\Payment\Payment\Payment`
  - [x] Add PSR-4 namespace to root `composer.json` under `autoload.psr-4`

- [x] Task 3: Register the Wallet package with Laravel/Concord (AC: 2, 6)
  - [x] Add `WalletServiceProvider::class` to `bootstrap/providers.php` (AFTER all payment providers — see Dev Notes)
  - [x] Add `Webkul\Wallet\Providers\ModuleServiceProvider::class` to `config/concord.php`
  - [x] Run `composer dump-autoload && php artisan optimize:clear` — confirm no errors

- [x] Task 4: Implement payment method override in WalletServiceProvider::boot() (AC: 3)
  - [x] Use `Config::set()` to force-disable all non-wallet payment methods in `boot()` (see exact key list in Dev Notes)
  - [x] Verify `config('payment_methods')` at runtime contains only `wallet` key as active

- [x] Task 5: Implement WalletPayment class (AC: 3, 4)
  - [x] Set `protected $code = 'wallet'`
  - [x] Implement `getRedirectUrl()` returning `null`
  - [x] Override `isAvailable()`: returns `true` only if authenticated customer AND `verification_status === 'approved'`
  - [x] Implement `getTitle()`, `getDescription()`, `getSortOrder()` returning sensible defaults

- [x] Task 6: Write Pest feature tests (AC: 7)
  - [x] Create `packages/Webkul/Wallet/tests/Feature/WalletPaymentAvailabilityTest.php`
  - [x] Test: only `wallet` key active in `config('payment_methods')` after boot
  - [x] Test: `WalletPayment::isAvailable()` = `false` when unauthenticated
  - [x] Test: `WalletPayment::isAvailable()` = `false` for unverified customer
  - [x] Test: `WalletPayment::isAvailable()` = `true` for verified + authenticated customer

## Dev Notes

### Critical: Provider Registration Order

`WalletServiceProvider::boot()` calls `Config::set()` to override payment method active flags. This works because:
1. All payment providers call `mergeConfigFrom()` in their `register()` methods
2. `WalletServiceProvider::boot()` fires AFTER all `register()` calls have completed
3. Therefore `Config::set()` in `boot()` wins over `mergeConfigFrom()` in `register()`

**`WalletServiceProvider` MUST be added as the LAST entry in `bootstrap/providers.php`** to guarantee it boots after all other payment providers. Current last entries are `StripeServiceProvider::class` and others — add `WalletServiceProvider::class` after `UserServiceProvider::class` which is currently last visible.

### Config Keys to Force-Disable

In `WalletServiceProvider::boot()`, disable all non-wallet payment methods:

```php
use Illuminate\Support\Facades\Config;

// Force-disable all non-wallet payment methods at runtime
$methodsToDisable = ['cashondelivery', 'moneytransfer', 'stripe', 'razorpay', 'paypal', 'payubiz', 'payuindia'];
foreach ($methodsToDisable as $key) {
    if (Config::has("payment_methods.{$key}")) {
        Config::set("payment_methods.{$key}.active", false);
    }
}
```

Do NOT remove or delete other payment method configs — only set `active = false`. Other payment providers remain installed for gateway top-up in Story 3.3.

### WalletPayment::isAvailable() Implementation

The `CustomerVerification` package provides the verification status constant. Use:

```php
use Webkul\CustomerVerification\Models\CustomerVerification;

public function isAvailable(): bool
{
    if (! auth('customer')->check()) {
        return false;
    }

    $customer = auth('customer')->user();

    // Check verification_status on the customer record
    // CustomerVerification uses 'approved' as the approved status string
    return $customer->verification_status === 'approved';
}
```

**Verify the exact attribute name** by reading `packages/Webkul/CustomerVerification/src/Models/` before implementing — the attribute may be `verification_status` on the Customer model itself (via observer/fillable) or via a relationship.

### Package File Structure

Follow `packages/Webkul/CustomerVerification/` exactly as the template:

```
packages/Webkul/Wallet/
├── src/
│   ├── Config/
│   │   └── payment-methods.php
│   ├── Payment/
│   │   └── WalletPayment.php
│   └── Providers/
│       ├── WalletServiceProvider.php
│       └── ModuleServiceProvider.php
└── tests/
    └── Feature/
        └── WalletPaymentAvailabilityTest.php
```

Story 3.2 will add `Models/`, `Services/`, `Contracts/` when implementing the Customer model binding.

### WalletServiceProvider Structure (Template)

Model this on `CustomerVerificationServiceProvider.php` (line-for-line pattern reference):

```php
namespace Webkul\Wallet\Providers;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__).'/Config/payment-methods.php',
            'payment_methods'
        );
        $this->app->register(ModuleServiceProvider::class);
    }

    public function boot(): void
    {
        // Force-disable all non-wallet payment methods
        $methodsToDisable = ['cashondelivery', 'moneytransfer', 'stripe', 'razorpay', 'paypal', 'payubiz', 'payuindia'];
        foreach ($methodsToDisable as $key) {
            if (Config::has("payment_methods.{$key}")) {
                Config::set("payment_methods.{$key}.active", false);
            }
        }
    }
}
```

### ModuleServiceProvider Structure

`ModuleServiceProvider` extends `CoreModuleServiceProvider`. Story 3.1 has no models to register (that's Story 3.2). Keep it minimal:

```php
namespace Webkul\Wallet\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    protected $models = [];
}
```

### Config/payment-methods.php Structure

Follow `packages/Webkul/Payment/src/Config/payment-methods.php` pattern exactly:

```php
use Webkul\Wallet\Payment\WalletPayment;

return [
    'wallet' => [
        'class'       => WalletPayment::class,
        'code'        => 'wallet',
        'title'       => 'Pay with Wallet',
        'description' => 'Pay using your wallet balance.',
        'active'      => true,
        'sort'        => 1,
    ],
];
```

### Root composer.json PSR-4 Entry

Add to `autoload.psr-4` section:

```json
"Webkul\\Wallet\\": "packages/Webkul/Wallet/src"
```

### bavix/laravel-wallet v11 Key Facts

- **Version**: `^11.0` — supports Laravel 11+12, PHP 8.3+, MIT license
- **LTS end of support**: September 2026
- **Migration publish tag**: `php artisan vendor:publish --tag=laravel-wallet-migrations`
- **Trait for multiple wallets**: `Bavix\Wallet\Traits\HasWallets` (Story 3.2 will use this)
- **Float methods**: `depositFloat()`, `withdrawFloat()`, `safeWithdrawFloat()` — use float/decimal amounts (not integer cents)
- **safeWithdrawFloat()**: Does NOT throw on insufficient balance — returns `null` instead of throwing. Always check return value.
- **decimal_places**: Set per-wallet in `wallets` table (`decimal_places` column, default 2); aligns with Bagisto's 2-decimal currency display
- **Tables created**: `wallets`, `transactions`, `transfers` — no conflict with Bagisto's `order_transactions`
- **No auto-boot**: Unlike some packages, bavix/laravel-wallet does NOT auto-register service provider in Laravel 12 — you must add it (it is auto-discovered via `extra.laravel.providers` in package's composer.json, so it IS auto-registered — verify after `composer require`)

### Rewards Package Conflict Warning (Story 3.2 Preview)

The `Rewards` package at line 215 of `RewardsServiceProvider.php` calls:
```php
$this->app->concord->registerModel(\Webkul\Customer\Contracts\Customer::class, \Webkul\Rewards\Models\Customer::class);
```

**Story 3.2 (not this story)** will need `WalletCustomer` to extend `Webkul\Rewards\Models\Customer` (not base `Customer`) to avoid breaking the Rewards trait chain. Story 3.1 does NOT touch Customer model binding — leave that entirely to Story 3.2.

### Verification Status Field

Before implementing `isAvailable()`, run:
```bash
grep -rn "verification_status\|is_verified\|approved" packages/Webkul/CustomerVerification/src/Models/
```
to confirm the exact field name/relationship used by the CustomerVerification package for the approved state.

### Test Structure

Tests use **Pest 3** with package-specific TestCase. Look at an existing test in another package for the TestCase setup pattern. The test file should be at:
`packages/Webkul/Wallet/tests/Feature/WalletPaymentAvailabilityTest.php`

You will need a `tests/Pest.php` bootstrap file and potentially a `TestCase.php`. Model these after `packages/Webkul/CustomerVerification/tests/` if that directory exists, or after `packages/Webkul/Stripe/tests/` as a fallback.

### Project Structure Notes

- **Template package**: `packages/Webkul/CustomerVerification/` — use its `Providers/` structure exactly
- **Payment method pattern**: `packages/Webkul/Payment/src/Config/payment-methods.php` and `CashOnDelivery.php`
- **concord.php registration**: Lines 30, 41, 49 show `ModuleServiceProvider` entries — add Wallet's after line 49 (after Stripe)
- **bootstrap/providers.php**: Add `WalletServiceProvider::class` after `UserServiceProvider::class` (currently last in list)
- **NEVER edit core Bagisto packages** (`packages/Webkul/Payment/`, `packages/Webkul/Customer/`, etc.) — only add via config merging and service provider boot

### References

- Template provider: `packages/Webkul/CustomerVerification/src/Providers/CustomerVerificationServiceProvider.php`
- Payment base class: `packages/Webkul/Payment/src/Payment/Payment.php` — `isAvailable()` at line 22
- Payment facade: `packages/Webkul/Payment/src/Payment.php` — `getPaymentMethods()` iterates `config('payment_methods')` and calls `isAvailable()` per method
- CashOnDelivery example: `packages/Webkul/Payment/src/Payment/CashOnDelivery.php`
- Payment config: `packages/Webkul/Payment/src/Config/payment-methods.php`
- Rewards Customer binding conflict: `packages/Webkul/Rewards/src/Providers/RewardsServiceProvider.php` line 215
- ModuleServiceProvider pattern: `packages/Webkul/CustomerVerification/src/Providers/ModuleServiceProvider.php`
- concord.php: `config/concord.php` lines 30, 41, 49 for existing module entries
- bootstrap/providers.php: current last entry before add is `UserServiceProvider::class`
- bavix docs: https://bavix.github.io/laravel-wallet/
- bavix multi-wallet: https://bavix.github.io/laravel-wallet/guide/multi/new-wallet.html

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Debug Log References

### Completion Notes List

- Installed `bavix/laravel-wallet:^11.0` via ddev composer
- Migrations NOT published — package auto-loads from vendor via `loadMigrationsFrom()`; `decimal_places` defaults to 2 in vendor migration — aligns with AC 5
- Scaffolded `packages/Webkul/Wallet/` package with WalletServiceProvider, ModuleServiceProvider, Config/payment-methods.php, Payment/WalletPayment.php
- Disabled list extended with actual registered codes: `paypal_smart_button`, `paypal_standard`, `payu` in addition to story spec list
- `WalletServiceProvider` added as last entry in `bootstrap/providers.php` as required by ordering constraint
- All 4 Pest feature tests pass (4/4); pre-existing Core unit test failure confirmed unrelated to this story
- `composer dump-autoload && php artisan optimize:clear` completed with no errors

### File List

- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (new)
- `packages/Webkul/Wallet/src/Providers/ModuleServiceProvider.php` (new)
- `packages/Webkul/Wallet/src/Config/payment-methods.php` (new)
- `packages/Webkul/Wallet/src/Payment/WalletPayment.php` (new)
- `packages/Webkul/Wallet/tests/WalletTestCase.php` (new)
- `packages/Webkul/Wallet/tests/Feature/WalletPaymentAvailabilityTest.php` (new)
- `bootstrap/providers.php` (modified — added WalletServiceProvider)
- `config/concord.php` (modified — added Wallet ModuleServiceProvider)
- `composer.json` (modified — added Webkul\\Wallet\\ PSR-4 namespaces)
- `composer.lock` (modified — bavix/laravel-wallet added)
- `phpunit.xml` (modified — added Wallet Feature Test suite)
- `tests/Pest.php` (modified — added WalletTestCase binding)

## Change Log

- Implemented bavix/laravel-wallet installation, Webkul/Wallet package scaffold, WalletPayment class with verification gating, and Pest feature tests (Date: 2026-04-23)
