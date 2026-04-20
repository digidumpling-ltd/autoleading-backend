---
title: 'Booking Flow (Phase 1: Manual Payment)'
slug: 'booking-flow-phase-1-manual-payment'
created: '2026-04-19'
status: 'in-progress'
stepsCompleted: [1, 2, 3]
tech_stack:
  - Bagisto BookingProduct
  - Laravel Notifications/Mail
  - AutoLeadingTheme Blade overrides
files_to_modify:
  - packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php
  - packages/Webkul/AutoLeadingTheme/src/Providers/AutoLeadingThemeServiceProvider.php
  - packages/Webkul/Shop/src/Resources/views/products/view/types/booking/booking.blade.php
  - packages/Webkul/Shop/src/Resources/views/products/view/types/booking/rental.blade.php
  - packages/Webkul/Shop/src/Http/Controllers/BookingProductController.php
  - packages/Webkul/Admin/src/Http/Controllers/Sales/BookingController.php
  - packages/Webkul/Admin/src/Routes/sales-routes.php
  - packages/Webkul/Admin/src/Resources/views/sales/bookings/index.blade.php
  - tests/Feature/BookingFlowTest.php
code_patterns:
  - Reuse package view override patterns via theme views/composers
  - Use Bagisto BookingProduct slot APIs and booking data flow
  - Use Laravel Notification and Mail channels for alerts
test_patterns:
  - Feature tests for booking submit and access control
  - Notification/mail assertion tests
  - Admin page visibility and state transition tests
---

# Tech-Spec: Booking Flow (Phase 1: Manual Payment)

**Created:** 2026-04-19

## Overview

### Problem Statement

The project needs a stable booking flow for car rental products where verified customers can submit date-range reservations, admin can review and process manually, and payment/credit deduction is intentionally deferred.

### Solution

Implement Phase 1 by reusing Bagisto native booking/rental capability and only customizing storefront experience in AutoLeadingTheme:

1. Use Bagisto BookingProduct (rental type) as the booking engine.
2. Use existing Bagisto booking slot endpoint and booking product partials.
3. Use native manual payment method flow (Money Transfer out-of-the-box; COD requires a small availability override for booking carts).
4. Treat native `pending` order status as "awaiting admin availability and payment approval".
5. Use existing admin bookings + order management for decisions: reject via order cancel, approve via invoice/fulfillment after offline payment acceptance.
6. Add verification gating and lightweight UX messaging in theme (manual review/payment expected).

### Scope

**In Scope:**
- Booking submission for verified customers using Bagisto rental booking.
- Manual payment via native method without online capture (Money Transfer by default; optional COD override if business requires COD label).
- Frontend customization of product detail booking area in AutoLeadingTheme.
- Admin reservation management using existing Bagisto bookings + orders pages.
- Manual admin approval represented by native order actions (cancel or proceed).
- Optional admin/customer notification enhancements using Laravel built-ins.

**Out of Scope:**
- Store credit deduction flow.
- Automated payment capture.
- New custom booking status engine.
- Deep rewrite of Bagisto booking core logic.
- Core package file modification when override/extension exists.

## Context for Development

### Codebase Patterns

- Bagisto already exposes booking slot data via `shop.booking-product.slots.index`.
- Shop booking UI is split by booking type under booking Blade partials.
- Admin already has sales bookings list/calendar and route wiring.
- AutoLeadingTheme currently has a custom product detail page that does not yet embed Bagisto booking partial flow.

### Files to Reference

| File | Purpose |
| ---- | ------- |
| packages/Webkul/Shop/src/Routes/store-front-routes.php | Defines booking slot endpoint and route name. |
| packages/Webkul/Shop/src/Http/Controllers/BookingProductController.php | Returns booking slot data by booking type helper. |
| packages/Webkul/Shop/src/Resources/views/products/view/types/booking/booking.blade.php | Entry point for booking product rendering. |
| packages/Webkul/Shop/src/Resources/views/products/view/types/booking/rental.blade.php | Built-in rental date/slot UI and validation logic. |
| packages/Webkul/Admin/src/Routes/sales-routes.php | Admin bookings routes and endpoints. |
| packages/Webkul/Admin/src/Http/Controllers/Sales/BookingController.php | Admin bookings list/calendar data provider. |
| packages/Webkul/Admin/src/Resources/views/sales/bookings/index.blade.php | Existing reservation-like management UI. |
| packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php | Theme product detail to customize and embed booking UX. |
| packages/Webkul/AutoLeadingTheme/src/Providers/AutoLeadingThemeServiceProvider.php | Theme-level extension point for view composers/overrides. |

### Technical Decisions

1. Priority order for implementation:
   - First: Customize/extend Bagisto built-in booking behavior.
   - Second: Use Laravel built-ins (notifications/mail/events).
   - Third: Add custom code/package only where no built-in path exists.
2. Task 1 and Task 2 are treated as built-in enablement + UI customization, not new booking engine creation.
3. Reservation management uses existing admin sales bookings and linked order pages; no custom reservation CRUD in Phase 1.
4. Manual approval is represented by native order lifecycle:
  - Reject booking: cancel order (when cancel eligible).
  - Approve booking: continue to invoice/fulfillment after offline payment acceptance.
5. Payment method for this phase should stay manual without online capture; Money Transfer supports booking out-of-the-box, while Cash on Delivery is blocked for non-stockable booking carts unless extended.
6. Note: a payment method record exists on order creation by Bagisto design; this is not an online charge transaction.

## Implementation Plan

### Tasks

- [ ] Task 1: Enable and validate Bagisto rental booking product setup
  - File: packages/Webkul/Admin/src/Resources/views/catalog/products/edit/types/booking.blade.php
  - Action: Confirm admin product setup uses booking type `rental` with required quantity/availability/rental slot data, and enable a manual payment method that supports booking carts (native Money Transfer or COD extension).
  - Notes: No custom booking schema creation.

- [ ] Task 2: Integrate native booking UI into AutoLeading product detail
  - File: packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php
  - Action: Embed booking-type rendering for booking products by reusing Bagisto booking partial path and add theme styling wrappers.
  - Notes: Keep native input names for booking payload compatibility.

- [ ] Task 3: Verification gate before booking submit
  - File: packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php
  - Action: Hide/disable booking submit when customer is not verified; keep existing verification banner and link.
  - Notes: Must block submit server-side as well via request validation guard.

- [ ] Task 4: Add payment deferred message UX
  - File: packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php
  - Action: After successful booking request, show success toast/modal indicating booking is pending admin review (availability + payment approval).
  - Notes: Message uses translation keys under auto-leading-theme::app.

- [ ] Task 5: Booking created notifications (admin in-app + email, customer email)
  - File: app/Notifications/BookingCreated.php
  - Action: Use native order/invoice/cancel mail flows first; add custom listener only if business needs a dedicated booking-specific mail on `booking_product.booking.save.after`.
  - Notes: Prefer Laravel Notification channels; avoid custom mail engine.

- [ ] Task 6: Reservation management in admin without core rewrite
  - File: packages/Webkul/Admin/src/Routes/sales-routes.php
  - Action: Reuse existing bookings route/view as base and linked order view for approve/reject actions; optionally add reservation alias route/menu label for business naming.
  - Notes: Implement as extension/config/menu alias, not core behavior replacement.

- [ ] Task 7: Manual verification workflow SOP and labels
  - File: packages/Webkul/Admin/src/Resources/views/sales/orders/view.blade.php
  - Action: Add/adjust labels/help text so admins interpret `pending` as "awaiting review" and use native cancel or invoice flow for decision.
  - Notes: Keep native status model unchanged.

- [ ] Task 8: Add feature tests for phase 1 flow
  - File: tests/Feature/BookingFlowTest.php
  - Action: Cover verified booking success, unverified booking blocked, default pending status, reject via cancel, and admin visibility.
  - Notes: Explicitly assert no online payment charge/transaction capture at booking placement.

### Acceptance Criteria

1. Given a product is configured as booking type rental, when a customer opens product detail, then native Bagisto rental booking inputs (date/slot) are rendered inside AutoLeadingTheme.
2. Given customer is not logged in or not verified, when they attempt to submit booking, then booking is blocked and verification guidance is shown.
3. Given verified customer submits valid booking date range/slot, when request is processed, then order and booking are created through native Bagisto booking flow and order status is `pending`.
4. Given booking order is `pending`, when admin verifies availability and rejects, then admin can cancel the linked order and booking appears rejected via canceled order state.
5. Given booking order is `pending`, when admin verifies availability and accepts offline payment, then admin proceeds with invoice/fulfillment using native order actions.
6. Given manual payment is configured (native Money Transfer or COD via extension), when customer places booking, then no online charge/transaction capture occurs at placement.
7. Given booking is created, when post-submit UI is shown, then customer sees message that booking is awaiting admin review and payment approval.
8. Given automated tests run for phase 1, when suite completes, then verified/unverified paths, pending default state, and cancel path pass without custom booking status engine.

## Additional Context

### Dependencies

- Existing Bagisto BookingProduct package and booking migrations.
- Existing Admin sales booking datagrid/calendar module.
- Customer verification package already implemented in project.

### Testing Strategy

- Feature tests for booking submit path by auth/verification state.
- Notification fake assertions for admin/customer channels.
- UI assertions for booking section presence on booking products only.
- Regression check for non-booking product detail pages.

### Notes

- This phase intentionally avoids introducing a standalone booking engine.
- If business insists on a dedicated "Reservation" menu, create a thin alias to existing admin bookings module first.
- Only if built-ins prove insufficient should a new custom reservation package be introduced.
- Admin "manual approval" in this phase is operational (pending -> cancel or proceed), not a separate booking-status workflow.
- Native emails are event-driven from order/invoice/cancel/shipment/comment events; there is no default email listener attached directly to `booking_product.booking.save.after`.
