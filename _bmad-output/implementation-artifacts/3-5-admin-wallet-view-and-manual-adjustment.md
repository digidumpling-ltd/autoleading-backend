# Story 3.5: Admin Wallet View and Manual Adjustment

Status: review

## Story

As a platform administrator,
I want to view any customer's wallet balance and make authorized credit adjustments,
So that I can resolve disputes, grant credit, and correct errors.

> **Scope note:** Single-balance wallet only (no dual purchased/bonus structure — de-scoped). `safeWithdrawFloat()` does not exist in bavix v11 — use `canWithdrawFloat()` + `withdrawFloat()` instead. Routes registered from Wallet package only — no core admin files modified.

## Acceptance Criteria

1. **Given** an admin with `customers.wallet` ACL navigates to `Admin > Customers > [Customer] > Wallet` (`/admin/customers/{id}/wallet`), **then** they see the customer's name, email, current wallet balance, and full paginated transaction ledger.

2. **Given** the admin clicks "Add Credit", **when** they submit a valid amount and reason, **then** `depositFloat()` is called with `meta` containing `type=admin_grant`, `admin_id`, and `reason`, and a success flash is shown.

3. **Given** the admin clicks "Deduct Credit", **when** the customer has sufficient balance, **then** `withdrawFloat()` is called with `meta` containing `type=admin_deduct`, `admin_id`, and `reason`, and a success flash is shown.

4. **Given** the admin submits a deduction that exceeds the available balance, **then** validation fails and an error is shown: "Insufficient balance."

5. **Given** an admin WITHOUT `customers.wallet` permission attempts to access the wallet endpoint, **then** a 401 response is returned.

6. **Given** all routes are registered from the Wallet package, **then** no core Admin package files are modified.

## Tasks / Subtasks

- [x] Task 1: Register admin routes and ACL from WalletServiceProvider (AC: 1, 5, 6)
  - [x] Create `src/Routes/admin-routes.php` with wallet view and adjust routes
  - [x] Create `src/Config/acl.php` with `customers.wallet` ACL key
  - [x] Update `WalletServiceProvider::register()` to merge ACL config
  - [x] Update `WalletServiceProvider::boot()` to load admin routes

- [x] Task 2: Implement AdminWalletController (AC: 1, 2, 3, 4, 5)
  - [x] Create `src/Http/Controllers/Admin/WalletController.php`
  - [x] `index($id)` — load WalletCustomer, paginated transactions, return view
  - [x] `adjust($id)` — validate, depositFloat or withdrawFloat with meta, redirect

- [x] Task 3: Create admin Blade view (AC: 1, 2, 3, 4)
  - [x] `src/Resources/views/admin/customers/wallet/index.blade.php` — balance card + transaction table + adjustment form

- [x] Task 4: Add translation strings (AC: 1–5)
  - [x] Update `src/Resources/lang/en/app.php` with `admin.customers.wallet.*` strings

- [x] Task 5: Write Pest feature tests (AC: 1–5)
  - [x] Admin with permission can view wallet page
  - [x] Admin can add credit — depositFloat called, ledger updated
  - [x] Admin can deduct credit when sufficient balance
  - [x] Deduction fails when balance insufficient
  - [x] Non-permitted admin gets 401

### Review Follow-ups (AI)

- [ ] [AI-Review][High] Reconcile story File List claims with git evidence (attach commit/hash or restore diff traceability) [_bmad-output/implementation-artifacts/3-5-admin-wallet-view-and-manual-adjustment.md:197]
- [ ] [AI-Review][Medium] Add assertion that insufficient deduction returns the exact expected message "Insufficient balance." [packages/Webkul/Wallet/tests/Feature/AdminWalletControllerTest.php:93]
- [ ] [AI-Review][Medium] Add required locale coverage for new wallet translation keys, or document/approve an English-only exception for this package [packages/Webkul/Wallet/src/Resources/lang/en/app.php:7]

## Dev Notes

### Route Registration (from Wallet package — no core admin files modified)

Register in `WalletServiceProvider::boot()`:
```php
$this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');
```

`admin-routes.php`:
```php
use Webkul\Core\Http\Middleware\NoCacheMiddleware;
use Webkul\Wallet\Http\Controllers\Admin\WalletController;

Route::group([
    'middleware' => ['admin', NoCacheMiddleware::class],
    'prefix'    => config('app.admin_url'),
], function () {
    Route::prefix('customers/{id}/wallet')->group(function () {
        Route::get('', [WalletController::class, 'index'])->name('admin.customers.wallet.index');
        Route::post('adjust', [WalletController::class, 'adjust'])->name('admin.customers.wallet.adjust');
    });
});
```

### ACL Registration

`Config/acl.php`:
```php
return [[
    'key'   => 'customers.wallet',
    'name'  => 'bagisto-wallet::app.admin.customers.wallet.acl-title',
    'route' => 'admin.customers.wallet.index',
    'sort'  => 10,
]];
```

Merge in `WalletServiceProvider::register()`:
```php
$this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php', 'acl');
```

### AdminWalletController Pattern

```php
namespace Webkul\Wallet\Http\Controllers\Admin;

use Webkul\Admin\Http\Controllers\Controller;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletController extends Controller
{
    public function __construct(protected CustomerRepository $customerRepository) {}

    public function index(int $id)
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $customer = WalletCustomer::findOrFail($id);
        $transactions = $customer->transactions()->latest()->paginate(20);

        return view('wallet::admin.customers.wallet.index', compact('customer', 'transactions'));
    }

    public function adjust(Request $request, int $id)
    {
        abort_if(! bouncer()->hasPermission('customers.wallet'), 401);

        $request->validate([
            'type'   => 'required|in:add,deduct',
            'amount' => 'required|numeric|min:0.01',
            'reason' => 'required|string|min:5',
        ]);

        $customer = WalletCustomer::findOrFail($id);
        $meta = [
            'admin_id' => auth()->guard('admin')->id(),
            'reason'   => $request->reason,
        ];

        if ($request->type === 'add') {
            $customer->depositFloat($request->amount, ['meta' => array_merge($meta, ['type' => 'admin_grant'])]);
            $msg = trans('bagisto-wallet::app.admin.customers.wallet.adjust-add-success');
        } else {
            if (! $customer->canWithdrawFloat($request->amount)) {
                return back()->withErrors(['amount' => trans('bagisto-wallet::app.admin.customers.wallet.insufficient-balance')]);
            }
            $customer->withdrawFloat($request->amount, ['meta' => array_merge($meta, ['type' => 'admin_deduct'])]);
            $msg = trans('bagisto-wallet::app.admin.customers.wallet.adjust-deduct-success');
        }

        return redirect()->route('admin.customers.wallet.index', $id)->with('success', $msg);
    }
}
```

### Transaction Meta in bavix v11

In bavix v11, the extra array structure is:
```php
$customer->depositFloat($amount, ['meta' => [...]])
// meta is stored in transactions.meta JSON column
```

### bavix v11 — No safeWithdrawFloat

`safeWithdrawFloat()` was removed in v11. Guard with `canWithdrawFloat()` first.

### Admin View Pattern

Use `x-admin::layouts` wrapper. Keep simple — no DataGrid needed. Copy structure from any admin blade view.

### Translation Namespace

Use `bagisto-wallet::` (not `wallet::` — bavix owns that namespace).

### Files to Create/Modify

- `packages/Webkul/Wallet/src/Routes/admin-routes.php` (new)
- `packages/Webkul/Wallet/src/Config/acl.php` (new)
- `packages/Webkul/Wallet/src/Http/Controllers/Admin/WalletController.php` (new)
- `packages/Webkul/Wallet/src/Resources/views/admin/customers/wallet/index.blade.php` (new)
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` (modify — add admin strings)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (modify — admin routes, ACL)
- `packages/Webkul/Wallet/tests/Feature/AdminWalletControllerTest.php` (new)

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Debug Log

### Completion Notes

- Routes registered from `WalletServiceProvider::boot()` — no core Admin files modified (AC 6 confirmed via route:list)
- ACL key `customers.wallet` merged into `acl` config — admins with `permission_type=all` get access; custom roles require explicit `customers.wallet` in permissions array
- Admin routes wrapped in `web` middleware (matching AdminServiceProvider pattern) so `ShareErrorsFromSession` shares `$errors` to views
- `depositFloat()`/`withdrawFloat()` called with flat meta array: `['type', 'admin_id', 'reason']` — stored in `transactions.meta` JSON column
- `canWithdrawFloat()` used as guard before `withdrawFloat()` (no `safeWithdrawFloat` in bavix v11)
- 6 new tests, 16 total — all passing (zero regressions)

### File List

- `packages/Webkul/Wallet/src/Routes/admin-routes.php` (new)
- `packages/Webkul/Wallet/src/Config/acl.php` (new)
- `packages/Webkul/Wallet/src/Http/Controllers/Admin/WalletController.php` (new)
- `packages/Webkul/Wallet/src/Resources/views/admin/customers/wallet/index.blade.php` (new)
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` (modified — added admin.customers.wallet strings)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (modified — admin routes + ACL config)
- `packages/Webkul/Wallet/tests/Feature/AdminWalletControllerTest.php` (new)

## Change Log

- Admin wallet view and manual adjustment: balance display, add/deduct credit form with ACL protection, transaction ledger; all from Wallet package with no core Admin files modified (Date: 2026-04-24)
