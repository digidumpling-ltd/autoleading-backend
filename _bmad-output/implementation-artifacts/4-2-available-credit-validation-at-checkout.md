# Story 4.2: Available Credit Validation Gate at Checkout

Status: done

## Story

As a customer,
I want the system to check my wallet balance before I can place a booking,
so that I am only shown the "Confirm Order" button when I have sufficient credit, and directed to top up when I don't.

## Acceptance Criteria

1. **Given** I am at the checkout confirmation step with `wallet` selected as payment method, **when** the page loads or the payment method changes, **then** a wallet status widget appears showing my current balance and whether I can afford the order.

2. **Given** `can_afford` is `true`, **when** the review step is reached, **then** the widget shows a green note "You will be charged [formatted_grand_total] from your wallet".

3. **Given** `can_afford` is `false`, **when** the review step is reached, **then** the widget shows a red "Insufficient Balance — Top Up Wallet" link-button (orange) pointing to `route('shop.customers.account.wallet.index')` with query params `?reason=insufficient_balance&required=[shortfall]`.

4. **Given** the server receives a POST to `shop.checkout.onepage.orders.store`, **when** the payment method is `wallet` and the authenticated customer's balance is less than `$cart->grand_total`, **then** `WalletCheckoutMiddleware` returns `{"message": "..."}` with HTTP 500 — the frontend shows it as a flash error.

5. **Given** the `WalletService` class, **when** `canAfford($customer, 200.00)` is called, **then** it delegates to `$customer->canWithdrawFloat(200.00)`.

6. **Given** all tests, **when** Pest feature tests run, **then** all 34 pass with no regressions.

## Tasks / Subtasks

- [x] Task 1: Create `WalletService` (AC: 1, 5)
  - [x] Create `packages/Webkul/Wallet/src/Services/WalletService.php`
  - [x] `canAfford(WalletCustomer $customer, float $amount): bool` — delegates to `$customer->canWithdrawFloat($amount)`
  - [x] `shortfall(WalletCustomer $customer, float $amount): float` — returns `max(0.0, $amount - $customer->balanceFloatNum)`
  - [x] Register in WalletServiceProvider: `$this->app->singleton(WalletService::class)`

- [x] Task 2: Create `WalletCheckoutMiddleware` for server-side validation (AC: 4)
  - [x] Create `packages/Webkul/Wallet/src/Http/Middleware/WalletCheckoutMiddleware.php`
  - [x] Only activates for `shop.checkout.onepage.orders.store` + wallet payment method
  - [x] Returns JSON 500 with `bagisto-wallet::app.checkout.insufficient-balance-server` message when balance insufficient
  - [x] Registered via `pushMiddlewareToGroup('web', WalletCheckoutMiddleware::class)` in WalletServiceProvider

- [x] Task 3: Extend `WalletPayment::getRedirectUrl()` for client-side redirect (AC: 3)
  - [x] Modify `packages/Webkul/Wallet/src/Payment/Wallet.php`
  - [x] Returns wallet top-up URL with `?reason=insufficient_balance&required=[shortfall]` when balance insufficient
  - [x] Returns null when balance is sufficient (no redirect)

- [x] Task 4: Create checkout status API endpoint (AC: 1, 2, 3)
  - [x] Create `packages/Webkul/Wallet/src/Http/Controllers/Shop/Checkout/WalletCheckoutController.php`
  - [x] `GET /shop/api/wallet/checkout-status` → `shop.wallet.checkout.status`
  - [x] Returns: `can_afford`, `balance`, `formatted_balance`, `shortfall`, `wallet_url`
  - [x] Add route in `packages/Webkul/Wallet/src/Routes/shop-routes.php`

- [x] Task 5: Create wallet balance widget via view_render_event injection (AC: 2, 3)
  - [x] Create `packages/Webkul/Wallet/src/Resources/views/shop/checkout/wallet-balance-widget.blade.php`
  - [x] Injects `<v-wallet-checkout-balance>` Vue component into checkout summary via `bagisto.shop.checkout.onepage.summary.grand_total.after`
  - [x] Component fetches status API, shows green/affordability state with charge note or red top-up link-button
  - [x] Register event listener in WalletServiceProvider

- [x] Task 6: Add translation keys (AC: 2, 3, 4)
  - [x] Modify `packages/Webkul/Wallet/src/Resources/lang/en/app.php`
  - [x] Added `'checkout'` key with: `insufficient-balance-button`, `wallet-charge-note`, `wallet-balance-label`, `insufficient-balance-hint`, `insufficient-balance-server`

- [x] Task 7: Write Pest feature tests (AC: 1–6)
  - [x] Create `packages/Webkul/Wallet/tests/Feature/WalletCheckoutValidationTest.php`
  - [x] WalletService unit tests (6 tests)
  - [x] Checkout status API endpoint tests (3 tests)
  - [x] WalletCheckoutMiddleware integration tests (2 tests)
  - [x] All 34 wallet tests pass

### Review Follow-ups (AI)

- [x] [AI-Review][Critical] Add missing `checkout.insufficient-balance-hint` translation key used by the checkout wallet widget. [packages/Webkul/Wallet/src/Resources/views/shop/checkout/wallet-balance-widget.blade.php:46] [packages/Webkul/Wallet/src/Resources/lang/en/app.php:51]
- [x] [AI-Review][Medium] Update story documentation to include actual modified sprint tracking file in Dev Agent Record File List (or remove unrelated change). [_bmad-output/implementation-artifacts/sprint-status.yaml:73]

## Dev Notes

### Architecture: Wallet-Package-Only (No Core File Modifications)

All checkout validation logic is implemented exclusively within `packages/Webkul/Wallet/`. No core Shop package files were modified.

### Key Components

- **`WalletService`** — `canAfford()` + `shortfall()` methods; singleton registered in WalletServiceProvider
- **`WalletCheckoutMiddleware`** — intercepts `shop.checkout.onepage.orders.store`; validates balance for wallet orders; returns JSON 500 on insufficient balance; registered via `pushMiddlewareToGroup('web', ...)`
- **`WalletPayment::getRedirectUrl()`** — returns top-up URL when insufficient; Vue frontend handles `{redirect: true, redirect_url: ...}` automatically
- **`WalletCheckoutController::status()`** — `GET shop.wallet.checkout.status`; returns affordability data for the Vue widget
- **`wallet-balance-widget.blade.php`** — injected via `view_render_event('bagisto.shop.checkout.onepage.summary.grand_total.after')`; renders `<v-wallet-checkout-balance>` Vue component

### Single-Balance Wallet

`$customer->canWithdrawFloat(amount)` and `$customer->balanceFloatNum`. Always call `$customer->fresh()` after balance mutations (bavix caches on instance).

### Wallet Route URL

Route name `shop.customers.account.wallet.index`, path `/customer/account/wallet` (NOT `/customer/wallet`).

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Completion Notes List

- Implemented without modifying any core Shop package files, using middleware + event injection pattern
- `WalletCheckoutMiddleware` registered via `pushMiddlewareToGroup('web', ...)` from WalletServiceProvider
- Balance widget injected into checkout summary via `bagisto.shop.checkout.onepage.summary.grand_total.after` view_render_event
- All 34 wallet tests pass (0 failures)
- ✅ Resolved review finding [Critical]: Added missing `checkout.insufficient-balance-hint` translation key to `packages/Webkul/Wallet/src/Resources/lang/en/app.php`
- ✅ Resolved review finding [Medium]: Added `_bmad-output/implementation-artifacts/sprint-status.yaml` to File List

### File List

- `packages/Webkul/Wallet/src/Services/WalletService.php` — new
- `packages/Webkul/Wallet/src/Http/Middleware/WalletCheckoutMiddleware.php` — new
- `packages/Webkul/Wallet/src/Http/Controllers/Shop/Checkout/WalletCheckoutController.php` — new
- `packages/Webkul/Wallet/src/Routes/shop-routes.php` — modified (added checkout-status route)
- `packages/Webkul/Wallet/src/Payment/Wallet.php` — modified (getRedirectUrl + isAvailable)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` — modified (middleware, singleton, event listener)
- `packages/Webkul/Wallet/src/Resources/views/shop/checkout/wallet-balance-widget.blade.php` — new
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` — modified (checkout translations)
- `packages/Webkul/Wallet/tests/Feature/WalletCheckoutValidationTest.php` — new
- `_bmad-output/implementation-artifacts/sprint-status.yaml` — modified (story status updated)

## Change Log

- Initial implementation: wallet checkout validation with WalletService, WalletCheckoutMiddleware, checkout status API, balance widget, and Pest tests — all 34 tests pass (Date: 2026-04-24)
- Addressed code review findings — 2 items resolved (Date: 2026-04-24)
- Post-implementation fix: `Wallet::isAvailable()` was gating wallet visibility behind `verification_status === 'approved'`, causing wallet to not appear in payment method selection for unverified customers. Verification belongs in CustomerVerification package, not here. Removed the check — wallet now shows for any authenticated customer (Date: 2026-04-25)
