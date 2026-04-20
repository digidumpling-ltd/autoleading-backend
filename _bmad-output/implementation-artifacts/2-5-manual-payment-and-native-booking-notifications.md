# Story 2.5: Manual Payment Configuration and Native Booking Notifications

Status: ready-for-dev

## Story

As a product/operator team,
I want booking orders to run on native manual payment configuration and native Bagisto notifications,
so that admin and customer receive required booking communications without backend customization.

## Acceptance Criteria

1. Given admin configuration is available, when payment and email settings are configured, then manual-payment and order notification toggles can be managed from admin configuration.

2. Given a booking order is placed successfully, when order-create events fire, then native new order communication is available for customer and admin according to configured toggles.

3. Given booking data is attached to cart/order items, when order-created emails are sent, then booking attributes are visible in email item details for both customer and admin templates.

4. Given admin reviews a pending booking and takes action, when native order lifecycle actions occur (cancel, invoice, shipment, comment), then follow-up communication is sent through native Bagisto listeners/templates according to configuration.

5. Given the business requires a second customer-facing confirmation after approval, when selecting native mechanisms, then invoice/comment flows are preferred before any custom notification implementation.

6. Given selected manual payment method is enabled, when booking checkout is tested, then placement flow behaves as expected for the selected method and no online capture is performed at booking placement.

7. Given this story completes, when handoff notes are reviewed, then operations has a clear configuration matrix for which toggles and actions trigger which communication.

## Tasks / Subtasks

- [ ] Task 1: Capture config matrix for manual payment and email toggles (AC: 1, 7)
  - [ ] Document required admin keys for payment method enablement
  - [ ] Document required admin keys for customer/admin order emails

- [ ] Task 2: Validate order-created communication for booking placement (AC: 2, 3)
  - [ ] Test booking placement with manual payment enabled
  - [ ] Verify customer and admin new-order emails triggered by native listeners
  - [ ] Verify booking attributes appear in email item lines

- [ ] Task 3: Validate post-review communication path (AC: 4, 5)
  - [ ] Test cancel path and resulting customer/admin notifications
  - [ ] Test invoice/comment path as confirmation communication
  - [ ] Record exact operational step that should be treated as "booking confirmed"

- [ ] Task 4: Validate no online capture at placement for selected manual flow (AC: 6)
  - [ ] Confirm behavior in checkout/order creation
  - [ ] Confirm expected order/payment record artifacts

- [ ] Task 5: Produce operator runbook excerpt (AC: 7)
  - [ ] Add concise checklist: toggles to enable, action to take, expected message recipients

## Dev Notes

- Story is configuration validation + communication mapping, not backend feature implementation.
- Use Bagisto native listeners and templates first.
- Only open a follow-up customization story if native flows cannot satisfy the exact business message timing/content.
