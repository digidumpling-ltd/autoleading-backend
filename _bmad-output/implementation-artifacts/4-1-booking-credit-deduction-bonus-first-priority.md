# Story 4.1: Wallet Deduction on Invoice Confirmation

Status: done

## Story

As an admin,
I want the customer's wallet balance to be automatically deducted when I create an invoice for a wallet-method order,
so that credit is only charged for confirmed bookings and I retain control over when charges are applied.

> **Scope note:** The wallet implementation is single-balance (not dual purchased/bonus — that was de-scoped during Epic 3). The "bonus-first" in the sprint key is a legacy artifact. This story implements deduction from the customer's single wallet on admin invoice creation, with full atomicity and idempotency.

## Acceptance Criteria

1. **Given** admin creates an invoice for an order where `payment.method = 'wallet'`, **when** `sales.invoice.save.after` fires, **then** `WalletInvoiceListener` deducts `$invoice->base_grand_total` from the customer's wallet using `withdrawFloat()`.

2. **Given** the customer has sufficient balance (`canWithdrawFloat($amount)` returns `true`), **when** the deduction succeeds, **then** an `OrderTransaction` record is created with `payment_method = 'wallet'`, `invoice_id`, `order_id`, `amount = $invoice->base_grand_total`, `status = 'paid'`, `type = 'capture'`.

3. **Given** the customer's balance is insufficient at invoice time (`canWithdrawFloat($amount)` returns `false`), **when** the listener fires, **then** it throws an exception — causing the entire `InvoiceRepository::create()` DB transaction to roll back, the invoice is NOT created, and the admin sees the error message in the UI.

4. **Given** a `WalletInvoiceListener` has already processed an invoice (an `OrderTransaction` record already exists for `invoice_id + payment_method = 'wallet'`), **when** the event fires again for the same invoice, **then** the listener exits early without deducting — ensuring idempotency.

5. **Given** an order's `payment.method` is NOT `'wallet'`, **when** `sales.invoice.save.after` fires, **then** `WalletInvoiceListener` exits immediately without any wallet interaction.

6. **Given** the `checkout.order.save.after` listener (`GenerateInvoice`) in the Wallet package, **when** a wallet order is placed, **then** NO invoice is auto-created (the `generate_invoice` system config default for wallet is `0`).

7. **Given** all listener and service code, **when** Pest feature tests run, **then** all deduction scenarios pass with no regressions.

## Tasks / Subtasks

- [x] Task 1: Create WalletInvoiceListener (AC: 1, 2, 3, 4, 5)
  - [x] Create `src/Listeners/WalletInvoiceListener.php`
  - [x] Constructor injects `OrderTransactionRepository`; `WalletCustomer` accessed via static `::find()` (Bagisto pattern — Eloquent models are not constructor-injected)
  - [x] `handle($invoice)`: exit if `$invoice->order->payment->method !== 'wallet'`
  - [x] Idempotency guard: exit if `OrderTransaction` already exists for this `invoice_id` + `payment_method = 'wallet'`
  - [x] Balance check: throw `\Exception` with message "Insufficient wallet balance. Cannot create invoice." if `canWithdrawFloat()` returns false
  - [x] Call `$customer->withdrawFloat($amount, ['meta' => ['type' => 'booking_deduction', 'order_id' => ..., 'invoice_id' => ...]])`
  - [x] Create `OrderTransaction` record via `OrderTransactionRepository::create()`

- [x] Task 2: Register WalletInvoiceListener in WalletServiceProvider (AC: 1)
  - [x] In `WalletServiceProvider::boot()`, add `Event::listen('sales.invoice.save.after', WalletInvoiceListener::class . '@handle')`
  - [x] Remove or keep `checkout.order.save.after` → `GenerateInvoice` listener (kept — guarded by `generate_invoice` config check, no-op by default)

- [x] Task 3: Ensure generate_invoice defaults to 0 for wallet (AC: 6)
  - [x] Verified: `generate_invoice` field in `system.php` has no default key — resolves to `null` (falsy), so `GenerateInvoice::handle()` skips auto-invoice for wallet. No change required.

- [x] Task 4: Write Pest feature tests (AC: 1–7)
  - [x] Test: wallet order invoiced — balance deducted, OrderTransaction created
  - [x] Test: insufficient balance — exception thrown, wallet balance unchanged, no OrderTransaction created
  - [x] Test: idempotency — second call with existing OrderTransaction does not double-deduct
  - [x] Test: non-wallet order — listener exits, wallet untouched
  - [x] Add translation key for insufficient balance error (AC: 3)

### Review Follow-ups (AI)

- [x] [AI-Review][High] Align Task 1 claim with implementation by either injecting `WalletCustomer` in `WalletInvoiceListener` constructor or updating the completed subtask text to match actual implementation. [packages/Webkul/Wallet/src/Listeners/WalletInvoiceListener.php:10]
- [x] [AI-Review][Medium] Add a defensive null check when wallet customer lookup fails and throw a domain-appropriate exception message before method calls on `$customer`. [packages/Webkul/Wallet/src/Listeners/WalletInvoiceListener.php:30]
- [x] [AI-Review][Low] Add an integration-style test that exercises invoice creation via repository/controller path to explicitly validate rollback and admin-facing error behavior for insufficient wallet balance (AC 3). [packages/Webkul/Wallet/tests/Feature/WalletInvoiceListenerTest.php:65]

### Review Follow-ups (AI) - Round 2

- [x] [AI-Review][High] Add admin invoice creation exception handling for wallet deduction failures so insufficient balance errors are surfaced as a UI flash message and redirect back (instead of an unhandled exception path). [packages/Webkul/Admin/src/Http/Controllers/Sales/InvoiceController.php:84]
- [x] [AI-Review][Low] Add an admin flow integration test that asserts the user-visible error message when wallet invoice creation fails due to insufficient balance. [packages/Webkul/Wallet/tests/Feature/WalletInvoiceListenerTest.php:97]

## Dev Notes

### Architecture: Why Deduction Happens at invoice.save.after

`InvoiceRepository::create()` (at `packages/Webkul/Sales/src/Repositories/InvoiceRepository.php`) wraps ALL invoice creation logic in `DB::beginTransaction()`:

```php
DB::beginTransaction();
try {
    // ... creates invoice, items, fires sales.invoice.save.after ...
    Event::dispatch('sales.invoice.save.after', $invoice);
} catch (\Exception $e) {
    DB::rollBack();
    throw $e;  // exception re-thrown to Admin controller
}
DB::commit();
```

**Critical implication**: If `WalletInvoiceListener::handle()` throws an exception, the entire transaction rolls back — the invoice, invoice items, and all DB changes are undone. This is the mechanism for AC 3 (insufficient balance abort).

### Single-Balance Wallet

The wallet is single-balance (implemented in Epic 3). There is no `purchased` / `bonus` wallet split. Use:
- `$customer->balanceFloatNum` — current balance
- `$customer->canWithdrawFloat(float $amount): bool` — balance check (replaces deprecated `safeWithdrawFloat`)
- `$customer->withdrawFloat(float $amount, array $meta)` — deduction

### WalletInvoiceListener Implementation

```php
namespace Webkul\Wallet\Listeners;

use Webkul\Sales\Repositories\OrderTransactionRepository;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletInvoiceListener
{
    public function __construct(
        protected OrderTransactionRepository $orderTransactionRepository
    ) {}

    public function handle($invoice): void
    {
        // AC 5: Skip non-wallet orders
        if ($invoice->order->payment->method !== 'wallet') {
            return;
        }

        // AC 4: Idempotency guard
        $alreadyProcessed = $this->orderTransactionRepository
            ->findWhere([
                'invoice_id'     => $invoice->id,
                'payment_method' => 'wallet',
            ])->isNotEmpty();

        if ($alreadyProcessed) {
            return;
        }

        $customer = WalletCustomer::find($invoice->order->customer_id);
        $amount   = (float) $invoice->base_grand_total;

        // AC 3: Balance check — throw to trigger DB rollback in InvoiceRepository
        if (! $customer->canWithdrawFloat($amount)) {
            throw new \Exception(
                trans('bagisto-wallet::app.listeners.wallet-invoice.insufficient-balance')
            );
        }

        // AC 1: Deduct wallet
        $customer->withdrawFloat($amount, ['meta' => [
            'type'       => 'booking_deduction',
            'order_id'   => $invoice->order_id,
            'invoice_id' => $invoice->id,
        ]]);

        // AC 2: Record OrderTransaction
        $this->orderTransactionRepository->create([
            'transaction_id' => 'wallet_tx_' . $invoice->id . '_' . uniqid(),
            'status'         => 'paid',
            'type'           => 'capture',
            'amount'         => $invoice->base_grand_total,
            'payment_method' => 'wallet',
            'invoice_id'     => $invoice->id,
            'order_id'       => $invoice->order_id,
        ]);
    }
}
```

### Registering the Listener

In `WalletServiceProvider::boot()`, add AFTER existing registrations:
```php
Event::listen('sales.invoice.save.after', 'Webkul\Wallet\Listeners\WalletInvoiceListener@handle');
```

Keep the existing `checkout.order.save.after` → `GenerateInvoice` registration — it is guarded by `generate_invoice` config check inside `GenerateInvoice::handle()` and will be a no-op as long as the config remains `0`.

### Transaction Meta in bavix v11

```php
$customer->withdrawFloat($amount, ['meta' => [...]]);
// meta stored in transactions.meta JSON column
```

### OrderTransaction Fields (from migration)

```
transaction_id  string (unique identifier, e.g. 'wallet_tx_42_abc123')
status          string nullable ('paid')
type            string nullable ('capture')
amount          decimal(12,4) ($invoice->base_grand_total)
payment_method  string nullable ('wallet')
invoice_id      unsigned int ($invoice->id)
order_id        unsigned int ($invoice->order_id)
```

### Test Strategy

Tests must be Pest feature tests using `WalletTestCase` (configured in `tests/Pest.php` — `uses(WalletTestCase::class)->in('../packages/Webkul/Wallet/tests')`).

For listener tests, instantiate the listener directly and pass a minimal Invoice mock, OR use the Order/Invoice factories:

**Minimal approach (direct listener call):**
```php
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\Invoice;
use Webkul\Sales\Models\OrderPayment;
use Webkul\Customer\Models\Customer as BaseCustomer;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Listeners\WalletInvoiceListener;
use Webkul\Sales\Repositories\OrderTransactionRepository;

it('deducts wallet on invoice confirmation for wallet order', function () {
    $base     = BaseCustomer::factory()->create();
    $customer = WalletCustomer::find($base->id);
    $customer->depositFloat(500.00);

    // Create minimal order + payment + invoice
    $order = Order::factory()->create(['customer_id' => $customer->id, 'status' => 'pending']);
    $payment = OrderPayment::create(['order_id' => $order->id, 'method' => 'wallet']);
    $invoice = Invoice::create([
        'order_id'           => $order->id,
        'state'              => 'paid',
        'base_grand_total'   => 200.00,
        'base_currency_code' => 'USD',
    ]);
    // Force-load payment on order for the listener
    $invoice->load('order.payment');

    $listener = app(WalletInvoiceListener::class);
    $listener->handle($invoice);

    expect($customer->fresh()->balanceFloatNum)->toBe(300.0);
    expect(\Webkul\Sales\Models\OrderTransaction::where('invoice_id', $invoice->id)->exists())->toBeTrue();
});
```

**Key gotcha**: After `depositFloat()` / `withdrawFloat()`, call `$customer->fresh()` to reload balance from DB (bavix caches balance on the model instance).

### Translation Keys to Add

In `src/Resources/lang/en/app.php`, add under a new `listeners` key:
```php
'listeners' => [
    'wallet-invoice' => [
        'insufficient-balance' => 'Insufficient wallet balance. Cannot create invoice.',
    ],
],
```

### Invoice Model Relationships Used

- `$invoice->order` — BelongsTo Order
- `$invoice->order->payment` — HasOne OrderPayment (method field)
- `$invoice->order->customer_id` — int
- `$invoice->order_id` — int (foreign key on Invoice)
- `$invoice->base_grand_total` — decimal, the amount to deduct
- `$invoice->id` — int

### Files to Create/Modify

- `packages/Webkul/Wallet/src/Listeners/WalletInvoiceListener.php` (new)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (modify — add invoice listener registration)
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` (modify — add listeners.wallet-invoice strings)
- `packages/Webkul/Wallet/tests/Feature/WalletInvoiceListenerTest.php` (new)

### Project Structure Notes

- All code stays within `packages/Webkul/Wallet/` — no core packages modified
- Listener registered from `WalletServiceProvider::boot()` using `Event::listen()`
- `OrderTransactionRepository` injected via constructor (Bagisto uses repository pattern throughout)
- Translation namespace: `bagisto-wallet::` (not `wallet::` — bavix owns that namespace)

### References

- `InvoiceRepository::create()` DB transaction: `packages/Webkul/Sales/src/Repositories/InvoiceRepository.php` lines 44–193
- `OrderTransaction` schema: `packages/Webkul/Sales/src/Database/Migrations/2021_03_11_212124_create_order_transactions_table.php`
- `WalletCustomer` model: `packages/Webkul/Wallet/src/Models/Customer.php`
- `WalletServiceProvider` boot: `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php`
- bavix v11 canWithdrawFloat pattern: Story 3.2 Dev Notes (no `safeWithdrawFloat` in v11)
- Existing `GenerateInvoice` listener: `packages/Webkul/Wallet/src/Listeners/GenerateInvoice.php`
- Test setup: `tests/Pest.php` line 38 — `uses(WalletTestCase::class)->in('../packages/Webkul/Wallet/tests')`
- Admin test pattern: `packages/Webkul/Wallet/tests/Feature/AdminWalletControllerTest.php`
- `OrderTransaction` model: `packages/Webkul/Sales/src/Models/OrderTransaction.php`

## Dev Agent Record

### Agent Model Used

claude-sonnet-4-6

### Debug Log References

### Completion Notes List

- `WalletInvoiceListener` created and registered on `sales.invoice.save.after`; deducts wallet on admin invoice confirmation for wallet-method orders
- Single-balance wallet (no purchased/bonus split) — `canWithdrawFloat()` + `withdrawFloat()` pattern (bavix v11)
- Insufficient balance throws `\Exception`, causing `InvoiceRepository::create()` DB transaction rollback — invoice NOT created
- Idempotency enforced via `order_transactions` check for `invoice_id + payment_method='wallet'`
- `generate_invoice` for wallet defaults to null (falsy) — no auto-invoice on order placement; admin manually creates invoices
- 6 Pest tests (added rollback integration test); 22 total wallet tests passing, zero regressions
- ✅ Resolved review finding [High]: updated subtask text to accurately describe static `WalletCustomer::find()` pattern
- ✅ Resolved review finding [Medium]: null guard added after `WalletCustomer::find()` — throws domain exception if customer not found
- ✅ Resolved review finding [Low]: integration-style rollback test added — verifies invoice is absent from DB after transaction rollback on insufficient balance
- ✅ Resolved review finding [High R2]: `InvoiceController::store()` now wraps `invoiceRepository->create()` in try-catch; wallet exceptions surface as admin flash error + redirect back (no more 500)
- ✅ Resolved review finding [Low R2]: admin flow test added — mocks InvoiceRepository::create() throwing wallet exception, asserts error flash and redirect; 23 total tests, all passing
- ✅ Re-review passed: no remaining review findings

### File List

- `packages/Webkul/Wallet/src/Listeners/WalletInvoiceListener.php` (new)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` (modified — added sales.invoice.save.after listener)
- `packages/Webkul/Wallet/src/Resources/lang/en/app.php` (modified — added listeners.wallet-invoice strings)
- `packages/Webkul/Wallet/tests/Feature/WalletInvoiceListenerTest.php` (new)
- `packages/Webkul/Admin/src/Http/Controllers/Sales/InvoiceController.php` (modified — try-catch around invoiceRepository->create())

## Change Log

- Wallet deduction on invoice confirmation: WalletInvoiceListener deducts balance when admin creates invoice, creates OrderTransaction record, enforces idempotency and insufficient-balance rollback; 5 Pest tests added (Date: 2026-04-24)
- Code review follow-ups added: logged AI review action items for constructor/task-claim alignment, null-safety guard, and AC3 rollback/UI integration coverage; story moved back to in-progress (Date: 2026-04-24)
- Re-review action items added (Round 2): logged open follow-ups for admin invoice UI error surfacing and admin flow assertion coverage; story remains in-progress (Date: 2026-04-24)
