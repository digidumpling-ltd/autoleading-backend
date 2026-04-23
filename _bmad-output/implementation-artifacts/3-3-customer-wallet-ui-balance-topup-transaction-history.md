# Story 3.3: Customer Wallet UI (Balance, Top-Up, Transaction History)

Status: review

## Story

As a verified customer,
I want a wallet section in my account dashboard,
So that I can view my balance, top up funds, and see my transaction history.

> **Scope note:** Top-up in this story directly credits the wallet (no payment gateway). Gateway integration for top-up is deferred to a later story. This gives the full UI and flow with real wallet operations.

## Acceptance Criteria

1. **Given** a verified customer navigates to `/customer/account/wallet`, **then** they see their current wallet balance.

2. **Given** the wallet page loads, **then** a "Wallet" link appears in the account sidebar navigation.

3. **Given** the customer has transactions, **when** they view the wallet page, **then** transaction history is shown (type, amount, date) with pagination.

4. **Given** the customer clicks "Top Up", **when** they submit a valid amount, **then** the wallet is credited and they are redirected back to the wallet page with the updated balance.

5. **Given** all routes are registered from the Wallet package, **then** no core Shop package files are modified.

## Tasks / Subtasks

- [x] Task 1: Register routes, views, and menu from WalletServiceProvider (AC: 2, 5)
  - [x] Create `src/Routes/shop-routes.php` with wallet account routes (auth:customer middleware)
  - [x] Create `src/Config/menu.php` with `account.wallet` menu item
  - [x] Update `WalletServiceProvider::register()` to merge menu config
  - [x] Update `WalletServiceProvider::boot()` to load routes and views

- [x] Task 2: Implement WalletController (AC: 1, 3, 4)
  - [x] Create `src/Http/Controllers/Shop/Account/WalletController.php`
  - [x] `index()` — fetch balance + paginated transactions, return view
  - [x] `topup()` — show top-up form
  - [x] `processTopup()` — validate amount, call `depositFloat()`, redirect with success

- [x] Task 3: Create Blade views (AC: 1, 2, 3, 4)
  - [x] `src/Resources/views/shop/customers/account/wallet/index.blade.php` — balance card + transaction table
  - [x] `src/Resources/views/shop/customers/account/wallet/topup.blade.php` — top-up form

- [x] Task 4: Add translation strings (AC: 1-4)
  - [x] Update `src/Resources/lang/en/app.php` with wallet UI strings

- [x] Task 5: Manual smoke test — confirm pages load and top-up works

## Dev Notes

### Route Registration (from Wallet package)

Register routes inside auth:customer + web middleware in `WalletServiceProvider::boot()`:

```php
$this->loadRoutesFrom(dirname(__DIR__).'/Routes/shop-routes.php');
$this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'wallet');
```

`shop-routes.php`:
```php
Route::middleware(['web', 'theme', 'locale', 'currency', 'auth:customer'])
    ->prefix(config('app.url_prefix', '') . 'customer/account/wallet')
    ->group(function () {
        Route::get('', [WalletController::class, 'index'])->name('shop.customers.account.wallet.index');
        Route::get('topup', [WalletController::class, 'topup'])->name('shop.customers.account.wallet.topup');
        Route::post('topup', [WalletController::class, 'processTopup'])->name('shop.customers.account.wallet.topup.store');
    });
```

### Menu Registration

Merge into `menu.customer` from `WalletServiceProvider::register()`:
```php
$this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.customer');
```

`Config/menu.php`:
```php
return [[
    'key'   => 'account.wallet',
    'name'  => 'wallet::app.customers.account.wallet.title',
    'route' => 'shop.customers.account.wallet.index',
    'icon'  => 'icon-wallet',
    'sort'  => 2,
]];
```

### WalletController Pattern

Follow `packages/Webkul/Shop/src/Http/Controllers/Customer/Account/OrderController.php`:

```php
namespace Webkul\Wallet\Http\Controllers\Shop\Account;

use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletController extends Controller
{
    public function index()
    {
        $customer = WalletCustomer::find(auth()->guard('customer')->id());
        $transactions = $customer->transactions()->latest()->paginate(15);
        return view('wallet::shop.customers.account.wallet.index', compact('customer', 'transactions'));
    }

    public function topup()
    {
        $customer = WalletCustomer::find(auth()->guard('customer')->id());
        return view('wallet::shop.customers.account.wallet.topup', compact('customer'));
    }

    public function processTopup(Request $request)
    {
        $request->validate(['amount' => 'required|numeric|min:1']);
        $customer = WalletCustomer::find(auth()->guard('customer')->id());
        $customer->depositFloat($request->amount, ['description' => 'Manual top-up']);
        return redirect()->route('shop.customers.account.wallet.index')
            ->with('success', trans('wallet::app.customers.account.wallet.topup-success'));
    }
}
```

### Views Pattern

Views use `x-shop::layouts.account` wrapper, same as orders/addresses views. Copy structure from `packages/Webkul/Shop/src/Resources/views/customers/account/orders/index.blade.php`.

Transaction history: Use a simple HTML table over `$transactions` (paginated Eloquent collection). The bavix transaction model has: `type` (deposit/withdraw), `amount` (integer cents), `amountFloat` (float), `confirmed`, `created_at`.

### Translation Keys Needed

Under `wallet::app.customers.account.wallet`:
- `title` — "My Wallet"
- `balance` — "Current Balance"
- `topup` — "Top Up"
- `topup-amount` — "Amount"
- `topup-success` — "Wallet topped up successfully."
- `transactions` — "Transaction History"
- `no-transactions` — "No transactions yet."
- `type-deposit` — "Top Up"
- `type-withdraw` — "Payment"
- `date` — "Date"
- `amount` — "Amount"

### Files to Create/Modify

- `packages/Webkul/Wallet/src/Routes/shop-routes.php` (new)
- `packages/Webkul/Wallet/src/Config/menu.php` (new)
- `packages/Webkul/Wallet/src/Http/Controllers/Shop/Account/WalletController.php` (new)
- `packages/Webkul/Wallet/src/Resources/views/shop/customers/account/wallet/index.blade.php` (new)
- `packages/Webkul/Wallet/src/Resources/views/shop/customers/account/wallet/topup.blade.php` (new)
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` (modify)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (modify)

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Debug Log

### Completion Notes

- Routes registered from WalletServiceProvider (not modifying core Shop package) — confirmed via `php artisan route:list --name=wallet`
- Menu item `account.wallet` merged into `menu.customer` config — confirmed via tinker
- WalletController uses `WalletCustomer::find()` to get wallet-capable Customer instance
- Top-up directly credits wallet via `depositFloat()` — no payment gateway (deferred to later story)
- Balance displayed via `core()->formatPrice($customer->balanceFloatNum)`
- Transaction type badge: green for deposit, red for withdraw
- All 10 existing Wallet tests still pass (no regressions)

### File List

- `packages/Webkul/Wallet/src/Routes/shop-routes.php` (new)
- `packages/Webkul/Wallet/src/Config/menu.php` (new)
- `packages/Webkul/Wallet/src/Http/Controllers/Shop/Account/WalletController.php` (new)
- `packages/Webkul/Wallet/src/Resources/views/shop/customers/account/wallet/index.blade.php` (new)
- `packages/Webkul/Wallet/src/Resources/views/shop/customers/account/wallet/topup.blade.php` (new)
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` (modified — added customers.account.wallet strings)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (modified — routes, views, menu)

## Change Log

- Customer wallet UI: balance display, top-up form, transaction history; routes/views/menu registered from Wallet package only (Date: 2026-04-23)
