# Story 8.1: Yedpay Payment Gateway Module

Status: review

## Story

As a platform administrator,
I want to configure Yedpay as an enabled payment method in Bagisto,
so that customers can pay for orders using Yedpay at checkout alongside any other enabled gateways.

## Acceptance Criteria

1. **Given** the Yedpay package is installed and credentials are configured in admin, **when** a customer reaches the payment step of checkout, **then** Yedpay appears as a selectable payment method.

2. **Given** a customer selects Yedpay and places an order, **when** the checkout redirect fires, **then** the customer is redirected to the Yedpay hosted payment page.

3. **Given** the customer completes payment on Yedpay, **when** Yedpay redirects back to the return URL, **then** the order is marked as processing, an invoice is created, and an `OrderTransaction` record is saved.

4. **Given** the customer cancels or payment fails on Yedpay, **when** Yedpay redirects to the cancel URL, **then** the customer is returned to cart with an appropriate error message and the order is not created.

5. **Given** Yedpay credentials are missing or invalid, **when** the payment method availability is checked, **then** Yedpay is hidden from the checkout payment list.

6. **Given** the implementation is complete, **when** Pest feature tests run, **then** they cover: successful redirect, successful return + order creation, cancel/failure path, and credential validation.

## Tasks / Subtasks

- [x] Task 1: Scaffold Yedpay package structure (AC: 1, 5)
  - [x] Create `packages/Webkul/Yedpay/` following the Stripe package structure
  - [x] Register `YedpayServiceProvider` in `bootstrap/providers.php`
  - [x] Add PSR-4 autoload entry in root `composer.json`
  - [x] Create `Config/payment-methods.php` registering the `yedpay` method
  - [x] Create `Providers/YedpayServiceProvider.php` loading routes, translations, and merging config
  - [x] Run `composer dump-autoload && php artisan optimize:clear` to verify registration

- [x] Task 2: Implement `YedpayPayment` class (AC: 1, 5)
  - [x] Extend `Webkul\Payment\Payment\Payment`
  - [x] Set `$code = 'yedpay'`
  - [x] Implement `getRedirectUrl()` returning `route('yedpay.standard.redirect')`
  - [x] Implement `isAvailable()` — parent check + `hasValidCredentials()`
  - [x] Implement `getTitle()`, `getDescription()`
  - [x] Implement `hasValidCredentials()` checking `api_key` and `signing_key` from config

- [x] Task 3: Implement `YedpayService` wrapping the Yedpay PHP library (AC: 2, 3)
  - [x] Require `yedpay/php-library` via `ddev composer require yedpay/php-library`
  - [x] Create `Services/YedpayService.php`
  - [x] Implement `createPayment(float $amount, string $customId, string $returnUrl, string $notifyUrl): string` — returns Yedpay hosted payment URL
  - [x] Implement `verifyCallback(array $params): bool` — verifies Yedpay return signature using `signing_key`
  - [x] Bound `YedpayService` in service provider so it can be mocked in tests

- [x] Task 4: Implement `YedpayController` for redirect and callback (AC: 2, 3, 4)
  - [x] Create `Http/Controllers/YedpayController.php`
  - [x] `redirect()`: get cart, store customId + cart_id in session, call `YedpayService::createPayment()`, redirect to Yedpay URL
  - [x] `success()`: verify callback signature; restore cart from session; create order + invoice + OrderTransaction; deactivate cart; redirect to success
  - [x] `cancel()`: flash error; redirect to cart
  - [x] `notify()`: async notify endpoint returns 200 OK

- [x] Task 5: Register routes and lang (AC: 1, 5)
  - [x] Create `Routes/web.php` with `/yedpay/redirect`, `/yedpay/success`, `/yedpay/cancel`, `/yedpay/notify` routes
  - [x] Create English lang file `Resources/lang/en/app.php` with title, description, and all response messages

- [x] Task 6: Feature tests (AC: 6)
  - [x] `tests/Feature/YedpayPaymentTest.php` — 9 tests, all passing
  - [x] Test: credentials invalid when api_key empty
  - [x] Test: credentials invalid when signing_key empty
  - [x] Test: `redirect()` redirects to cart when credentials missing
  - [x] Test: `redirect()` redirects to cart when no cart exists
  - [x] Test: `redirect()` redirects to Yedpay URL on valid redirect
  - [x] Test: `success()` redirects to cart when signature verification fails
  - [x] Test: `cancel()` redirects to cart with error
  - [x] Test: `success()` redirects to cart when session cart_id missing
  - [x] Test: `success()` creates order + invoice + OrderTransaction on valid callback

## Dev Notes

### Reference Implementation

Mirror the Stripe package (`packages/Webkul/Stripe/`) exactly — same structure, same controller pattern, same config registration. Key files to read before implementing:

- `packages/Webkul/Stripe/src/Payment/Stripe.php` — Payment class pattern
- `packages/Webkul/Stripe/src/Http/Controllers/StripeController.php` — redirect/success/cancel flow
- `packages/Webkul/Stripe/src/Providers/StripeServiceProvider.php` — registration pattern
- `packages/Webkul/Stripe/src/Routes/web.php` — route registration
- `packages/Webkul/Stripe/src/Config/payment-methods.php` — payment method config format

### Yedpay PHP Library

- Repo: https://github.com/yedpay/php-library
- Install: `ddev composer require yedpay/php-library`
- Read the library's README before implementing `YedpayService` to understand the exact method signatures for creating a payment and verifying the callback signature.

### Bagisto Payment Registration

- Payment methods are registered via `Config/payment-methods.php` merged into the `payment_methods` config key.
- `Webkul\Payment\Payment\Payment` base class provides `getConfigData($field)` which reads from `core()->getConfigData("payment/{$this->code}/{$field}")`.
- Admin config fields are registered in `Config/system.php` under `sales.payment_methods.yedpay`.

### Key Bagisto Classes

- `Webkul\Checkout\Facades\Cart` — get current cart
- `Webkul\Sales\Transformers\OrderResource` — transforms cart to order creation data
- `Webkul\Sales\Repositories\OrderRepository::create($data)` — creates order
- `Webkul\Sales\Repositories\InvoiceRepository::create($invoiceData)` — creates invoice, fires `sales.invoice.save.after`
- `Webkul\Sales\Repositories\OrderTransactionRepository::create($data)` — records payment transaction

### Testing Standards

- Use Pest feature tests with `RefreshDatabase`
- Mock `YedpayService` in controller tests to avoid real API calls
- Assert `OrderTransaction` is created with correct `payment_method = 'yedpay'`
- Assert `sales.invoice.save.after` event is fired (future wallet top-up listener depends on this)

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Debug Log References

### Completion Notes List

- Installed `yedpay/php-library` via ddev composer. Library uses `Client::onlinePayment()` for hosted payment URL creation and `Client::verifySign()` for callback signature verification.
- `YedpayService` wraps the library with `createPayment()` (returns hosted URL), `verifyCallback()` (HMAC_SHA256 via `verifySign()`), and `queryPayment()`.
- `YedpayController` stores `customId` + `cart_id` in session on redirect; restores cart from session on success callback. Follows Stripe controller pattern exactly.
- `YedpayService` bound in container via `YedpayServiceProvider` so tests can mock it cleanly via `$this->mock(YedpayService::class)`.
- Credential tests use `CoreConfig::updateOrCreate()` instead of `factory()->create()` to avoid duplicate-row resolution issues with `findOneWhere()`.
- `notify()` endpoint returns 200 OK; async Yedpay notifications are acknowledged without double-processing (order is already created in `success()`).
- 9 feature tests, all passing. 3 pre-existing wallet test failures confirmed unrelated.

### File List

- `packages/Webkul/Yedpay/src/Config/payment-methods.php` — new
- `packages/Webkul/Yedpay/src/Http/Controllers/YedpayController.php` — new
- `packages/Webkul/Yedpay/src/Payment/Yedpay.php` — new
- `packages/Webkul/Yedpay/src/Providers/YedpayServiceProvider.php` — new
- `packages/Webkul/Yedpay/src/Resources/lang/en/app.php` — new
- `packages/Webkul/Yedpay/src/Routes/web.php` — new
- `packages/Webkul/Yedpay/src/Services/YedpayService.php` — new
- `packages/Webkul/Yedpay/tests/Feature/YedpayPaymentTest.php` — new
- `packages/Webkul/Yedpay/tests/YedpayTestCase.php` — new
- `bootstrap/providers.php` — modified (registered YedpayServiceProvider)
- `composer.json` — modified (PSR-4 autoload + yedpay/php-library dependency)
- `phpunit.xml` — modified (added Yedpay Feature Test suite)
- `tests/Pest.php` — modified (registered YedpayTestCase)

### Change Log

- 2026-04-26: Implemented Yedpay payment gateway module — full Bagisto payment method with redirect/success/cancel/notify flow, YedpayService wrapper, 9 feature tests passing.
