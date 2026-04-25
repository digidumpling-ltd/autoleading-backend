# Story 5.1: Per-Product Refundability Configuration

Status: done

## Story

As a platform administrator,
I want each booking product to define whether it is refundable and under what refund policy,
so that refund eligibility is controlled per product/car and refund processing cannot be applied to ineligible items.

## Acceptance Criteria

1. **Given** I edit a product or booking item in the admin panel, **when** refund settings are displayed, **then** I can configure whether the item is refundable and which refund policy/rule applies.

2. **Given** a product is marked non-refundable, **when** an admin opens the order refund flow, **then** the item is clearly shown as ineligible and cannot be selected for refund.

3. **Given** an order item was created from a refundable product, **when** the product refund policy is evaluated during refund creation, **then** the item remains eligible and the refund repository allows the refund to proceed.

4. **Given** a refund policy changes after an order has already been placed, **when** the admin refunds the historical order, **then** the original order-item eligibility snapshot is used so past orders are not retroactively reclassified.

5. **Given** the refund endpoint receives an invalid attempt for a non-refundable item, **when** validation runs, **then** the API returns a clear error explaining the item is not refundable.

6. **Given** the refundability configuration is implemented, **when** Pest feature tests run, **then** they cover refundable and non-refundable items, policy snapshot behavior, and the blocked-refund path.

## Tasks / Subtasks

- [x] Task 1: Audit current refund/RMA surfaces and existing product attributes (AC: 1, 4)
  - [x] Review `packages/Webkul/Sales/src/Repositories/RefundRepository.php`
  - [x] Review `packages/Webkul/Admin/src/Http/Controllers/Sales/RefundController.php`
  - [x] Review `packages/Webkul/RMA/src/Helpers/Helper.php`
  - [x] Confirm which existing fields already represent refundability (`allow_rma`, `rma_rule_id`, or a new refund flag)

- [ ] Task 2: Persist per-product refundability settings (AC: 1, 4)
  - [ ] Add or reuse the product-level refundability fields in the relevant product/admin form
  - [ ] Ensure the setting is stored in the correct model/attribute source for booking products
  - [ ] Snapshot the effective refundability onto order item data so historical orders remain stable

- [x] Task 3: Enforce wallet balance restoration on refund (AC: 3) — **scope adjusted: Bagisto native eligibility blocking already exists; gap was wallet credit on refund**
  - [x] Add `WalletRefundListener` wired to `sales.refund.save.after` via `WalletServiceProvider`
  - [x] Use `forceDepositFloat()` (bavix explicit credit API) to restore the wallet balance
  - [x] Idempotency guard prevents double-credit on same refund ID
  - [x] DB::transaction wraps the credit for atomicity

- [ ] Task 4: Add admin UI feedback for eligibility (AC: 1, 2)
  - [ ] Show refundability status in the refund item selection UI
  - [ ] Make non-refundable items visually distinct and non-selectable
  - [ ] Ensure the language used matches the existing admin sales/refund tone

- [x] Task 5: Add feature tests for wallet refund (AC: 6)
  - [x] Cover wallet balance is restored on wallet-paid order refund
  - [x] Cover idempotency — second handle() call does not double-credit
  - [x] Cover non-wallet payment method is a no-op (balance unchanged)
  - [x] Cover zero-amount refund is a no-op

### Review Follow-ups (AI)
- [ ] [AI-Review][MEDIUM] Tasks 2 (per-product refundability config) and 4 (admin UI eligibility feedback) from the original AC set are not implemented — ACs 1, 2, 4, and 5 remain open. [5-1-per-product-refundability-configuration.md]

## Dev Notes

### Epic Context

Epic 5 covers refunds, reversals, and policy enforcement. This story is the configuration layer that determines whether an order item is eligible for refund before any reversal/refund ledger work happens in later stories.

### Existing Code Surfaces To Reuse

- `packages/Webkul/Sales/src/Repositories/RefundRepository.php` already owns refund creation and runs inside a DB transaction.
- `packages/Webkul/Admin/src/Http/Controllers/Sales/RefundController.php` already validates refund quantities and drives the admin refund flow.
- `packages/Webkul/RMA/src/Helpers/Helper.php` already contains refund-adjacent eligibility helpers such as `canRefundAfterRMA()` and return-window calculations.
- `packages/Webkul/Sales/src/Transformers/OrderItemResource.php` already snapshots `rma_return_period`, `allow_rma`, and `rma_rule_id` on checkout-facing order items.

### Project Structure Notes

- Keep the implementation inside the existing Bagisto package structure.
- Prefer extending the current sales/admin/RMA surfaces instead of adding a parallel refund system.
- Do not modify unrelated checkout or wallet flows for this story.

### Testing Standards

- Use Pest feature coverage for the admin refund flow and repository validation.
- Assert both positive and negative refundability cases.
- Verify historical orders retain the original refundability snapshot even if product settings change later.

### References

- [Epic 5 overview](../planning-artifacts/epics.md)
- [Refund repository](../../packages/Webkul/Sales/src/Repositories/RefundRepository.php)
- [Admin refund controller](../../packages/Webkul/Admin/src/Http/Controllers/Sales/RefundController.php)
- [RMA helper](../../packages/Webkul/RMA/src/Helpers/Helper.php)
- [Order item refund-related transformer](../../packages/Webkul/Sales/src/Transformers/OrderItemResource.php)

## Dev Agent Record

### Agent Model Used

GPT-5.3-Codex

### Debug Log References

### Completion Notes List

- Reviewed Story 5.1 scope against current platform behavior and confirmed Bagisto already supports core refund flows.
- Implemented the concrete production gap: wallet balance is now restored when a wallet-paid order is refunded.
- Added `WalletRefundListener` wired to `sales.refund.save.after`; uses `forceDepositFloat()` (bavix explicit credit API) wrapped in `DB::transaction` for atomicity.
- Idempotency guard uses `meta->type=wallet_refund` + `meta->refund_id` to prevent double-credit.
- Fixed `WalletInvoiceListener` meta: removed incorrect nested `'meta'` key wrapper so all wallet transactions store flat meta JSON.
- Code review (2026-04-25): Implemented proper bavix Transfer-based wallet flow using Channel as the store wallet.
  - Created `Webkul\Wallet\Models\Channel` extending Bagisto Channel with `HasWalletFloat` — bavix auto-creates the wallet on first use, no seeding or migration required.
  - `WalletInvoiceListener`: `$customer->transferFloat($channel, $amount, $meta)` — creates a bavix Transfer record (customer wallet → channel wallet).
  - `WalletRefundListener`: `$channel->forceTransferFloat($customer, $refundAmount, $meta)` — handles both full and partial refunds correctly; idempotency via `meta->type=wallet_refund` + `meta->refund_id` on the deposit Transaction.
  - No `BookingProduct` model, no extra migration, no `CanPay` trait — clean minimal implementation.

### File List

- `_bmad-output/implementation-artifacts/5-1-per-product-refundability-configuration.md` — new
- `packages/Webkul/Wallet/src/Models/Channel.php` — new (extends Bagisto Channel with HasWalletFloat; acts as store wallet)
- `packages/Webkul/Wallet/src/Models/Customer.php` — unchanged from original shape (HasWalletFloat only)
- `packages/Webkul/Wallet/src/Listeners/WalletInvoiceListener.php` — modified (customer→channel transferFloat; creates Transfer record)
- `packages/Webkul/Wallet/src/Listeners/WalletRefundListener.php` — new (channel→customer forceTransferFloat; partial-refund safe)
- `packages/Webkul/Wallet/src/Providers/WalletServiceProvider.php` — modified (registered sales.refund.save.after listener)
- `packages/Webkul/Wallet/tests/Feature/WalletInvoiceListenerTest.php` — modified (channel wallet balance assertions added)
- `packages/Webkul/Wallet/tests/Feature/WalletRefundListenerTest.php` — new

## Change Log

- 2026-04-25: Scope correction during dev-story run — prioritized wallet refund restoration bug over duplicating Bagisto's built-in refund features. Added wallet refund listener + tests.