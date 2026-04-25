# Story 2.3: Admin Verification Dashboard & Rental Gating

Status: review

<!-- Note: Validation is optional. Run validate-create-story for quality check before dev-story. -->

## Story

As an admin,
I want to review customer verification documents, approve/reject applications, and gate car rentals until customers are verified,
so that only verified customers can rent cars and compliance is maintained.

## Acceptance Criteria

### Admin Verification Dashboard

1. An admin page lists all customers with verification status (`incomplete`, `pending`, `approved`, `rejected`).
2. Admins can click on a customer to view their documents (ID, Driver License, Address Proof) inline or in lightbox.
3. Admins can approve a customer (status → `approved`, documents marked reviewed).
4. Admins can reject a customer and provide rejection reason/notes.
5. Rejected customers can re-upload documents and retry (status → `pending` on upload).
6. Admin actions are logged for audit trail (action, timestamp, admin user).
7. Implementation uses the custom package created in Story 2.1 (no core admin package edits).
8. Authorization: Only admin users with verification management role can access this dashboard.
9. The page integrates into the admin menu under a new "Verification" section.

### Rental Gating Logic

10. Unverified customers (status ≠ `approved`) cannot add items to cart if they are car rental products.
11. Unverified customers attempting to add car rentals see an error message and redirect to verification dashboard.
12. Verified customers (status = `approved`) can add rentals to cart normally.
13. Gating is checked via a listener on the `cart.item.adding` event.
14. Authorization: Customers viewing rental products should see a banner if they are unverified (status ≠ `approved`).

## Tasks / Subtasks

- [x] Task 1: Create admin verification dashboard controller and routes (AC: 1, 8, 9)
  - [x] Add route `GET /admin/verification` to list pending/incomplete verifications
  - [x] Add route `GET /admin/verification/{customer_id}` to view customer documents
  - [x] Add route `POST /admin/verification/{customer_id}/approve` to approve
  - [x] Add route `POST /admin/verification/{customer_id}/reject` to reject with reason
  - [x] Create controller `VerificationManagementController` in Admin namespace
  - [x] Add authorization check: ACL config registered as `customers.verifications` via `Config/acl.php` merged into Bagisto ACL system
  - [x] Return 403 Forbidden for unauthorized access (enforced via Bagisto ACL role assignment)
  - [x] Add admin menu item linking to verification dashboard

- [x] Task 2: Create admin dashboard views and document viewer (AC: 2, 3, 4, 5)
  - [x] Create view `packages/Webkul/CustomerVerification/src/Resources/views/admin/verifications/index.blade.php`
    - Show table: Customer Name, Email, Status, Documents Uploaded, Date Submitted
    - Filter by status (all/pending/incomplete/approved/rejected)
    - Actions: View, Approve, Reject
  - [x] Create view `packages/Webkul/CustomerVerification/src/Resources/views/admin/verifications/show.blade.php`
    - Display customer info
    - Show document grid with thumbnails (ID, Driver, Address)
    - Display download links for each document
    - Show approval/rejection form
  - [x] Add rejection reason form field (text area, required when rejecting)

- [x] Task 3: Implement approval/rejection actions and audit logging (AC: 3, 4, 5, 6)
  - [x] Create service `AdminVerificationActionService`
    - Approve action: set status → `approved`, log action
    - Reject action: set status → `rejected`, save rejection reason, log action
    - Both emit verification event: `verification.admin.approved` / `verification.admin.rejected`
  - [x] Create audit log entry for each admin action (user, action type, timestamp, reason)
  - [x] Persist rejection reason (column `rejection_reason` on `customers` table)
  - [x] On rejection, customer can see reason on verification dashboard (from Story 2.2)

- [x] Task 4: Implement cart item listener for rental gating (AC: 10, 11, 12, 13)
  - [x] Create listener `PreventUnverifiedRentalAddToCartListener` bound to `checkout.cart.add.before` event (corrected from `cart.item.adding` which does not exist in Bagisto core)
  - [x] Check if added product is a rental product type (detect via `$product->type === 'rental'`)
  - [x] Get current customer's verification status
  - [x] If customer status ≠ `approved` AND product is rental: throw exception with dashboard link
  - [x] If customer is verified OR product is not rental: allow cart addition

- [x] Task 5: Add product-level verification requirements (AC: 10, 12, 14)
  - [x] Product type detection via `type === 'rental'` (no core table migration needed; avoids Bagisto upgrade conflicts)
  - [x] Frontend banner on rental product pages: "Verification required to rent"
  - [x] Banner hidden if customer status = `approved`
  - [x] Banner includes link to verification dashboard

- [x] Task 6: Add translation keys for admin UI and gating messages (AC: All)
  - [x] All keys moved to `CustomerVerification` package lang files (`customer-verification::app.common.*`)
  - [x] EN + zh_CN translations complete

- [x] Task 7: Add tests for admin verification and rental gating (AC: All)
  - [x] Feature tests for admin dashboard: list, filter, view details (6 tests, all passing)
  - [x] Feature tests for approve action: status change, audit log
  - [x] Feature tests for reject action: status change, reason saved
  - [x] Feature tests for cart gating: 3 tests (unverified blocked, verified allowed, non-rental allowed)

## Dev Notes

### Core Implementation Requirements

- Use the custom package created in Story 2.1: `Webkul/CustomerVerification`.
- Tables used/updated:
  - `customer_verification_documents` (read: view documents)
  - `customers.verification_status` (update: set to approved/rejected)
  - New table `verification_audit_logs` for admin actions (admin_id, action, customer_id, reason, created_at)
- Rejection reason storage: Add `rejection_reason` column to `customers` table OR store in audit log (choice of implementation).

### Admin Verification Workflow

1. Admin visits `/admin/verification` → sees list of all customers with pending/incomplete status
2. Click on customer → view their documents and current status
3. Admin approves: status → `approved`, customer can now rent
4. Admin rejects: status → `rejected`, reason saved, customer notified (or views reason on dashboard)
5. Rejected customer uploads new docs on dashboard → status → `pending` (implicit retry, Story 2.2 handles this)
6. Admin reviews again, approves

### Rental Gating Workflow

1. Unverified customer (status ≠ `approved`) attempts to add a car rental to cart
2. `cart.item.adding` event fires with product info
3. Listener checks:
   - Is customer authenticated? (if not, allow; they'll be forced to register)
   - Is customer verification status = `approved`? 
   - Is product a rental product (has `requires_verification` flag)?
4. If unverified AND rental: throw exception, display error toast
5. If verified OR non-rental: allow addition

### ACL / Authorization

- New admin role or permission: `manage-customer-verifications`
- Only admins with this role can see/access `/admin/verification` routes

### Event Dispatch

- `verification.admin.approved` with customer/admin user context
- `verification.admin.rejected` with customer/admin user/reason context

### Tables & Schema Updates

#### Migration: Add `verification_audit_logs` table
```sql
CREATE TABLE verification_audit_logs (
  id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
  admin_id BIGINT UNSIGNED NOT NULL,
  customer_id BIGINT UNSIGNED NOT NULL,
  action VARCHAR(50), -- 'approved', 'rejected', 'viewed'
  reason TEXT NULLABLE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES admin_users(id),
  FOREIGN KEY (customer_id) REFERENCES customers(id)
);
```

#### Migration: Add `rejection_reason` to `customers` table (alternative)
```sql
ALTER TABLE customers ADD COLUMN rejection_reason TEXT NULLABLE;
```

#### Migration: Add `requires_verification` to `products` table or product attributes
```sql
ALTER TABLE products ADD COLUMN requires_verification BOOLEAN DEFAULT FALSE;
```

### File References

- Admin controller: `packages/Webkul/CustomerVerification/src/Http/Controllers/Admin/VerificationManagementController.php`
- Admin views: `packages/Webkul/CustomerVerification/src/Resources/views/admin/verifications/{index.blade.php,show.blade.php}`
- Listener: `packages/Webkul/CustomerVerification/src/Listeners/PreventUnverifiedRentalAddToCartListener.php`
- Service: `packages/Webkul/CustomerVerification/src/Services/AdminVerificationActionService.php`
- Config: Add to `packages/Webkul/CustomerVerification/src/Config/menu.php` (admin section)

### Known Constraints

- Admin and customer verification workflows must not block each other (no race conditions)
- Rejection reason should be visible to customer (display on verification dashboard from Story 2.2)
- Rental gating should not prevent admins from testing checkout (optional: add bypass for admin role)

## Dev Agent Record

### Completion Notes

- **Critical bug fixed**: Listener was bound to `cart.item.adding` (non-existent event). Corrected to `checkout.cart.add.before` (actual Bagisto core event in `Webkul/Checkout/src/Cart.php:258`).
- **Critical bug fixed**: Listener implemented `ShouldQueue` (async), preventing exceptions from blocking cart addition. Removed — listener is now synchronous.
- **Critical bug fixed**: `handle($event)` received an int product ID but treated it as an object with `->product`. Fixed to resolve product by ID from repository, with object passthrough for test compatibility.
- **ACL**: Registered `customers.verifications` permission in `Config/acl.php` merged into Bagisto ACL system. Admin role assignment controls dashboard access.
- **Translation refactor**: All verification strings moved from `auto-leading-theme` to `customer-verification` package lang files for theme-independence.
- **Product banner**: Added verification warning to `AutoLeadingTheme/products/view.blade.php` for unverified customers on rental products.
- **Tests**: `VerificationManagementTest` fixed (wrong Admin model namespace `Webkul\Admin` → `Webkul\User`). All 9 tests pass.

### File List

- `packages/Webkul/CustomerVerification/src/Providers/EventServiceProvider.php` — fixed event name
- `packages/Webkul/CustomerVerification/src/Listeners/PreventUnverifiedRentalAddToCartListener.php` — removed ShouldQueue, fixed handle() signature
- `packages/Webkul/CustomerVerification/src/Config/acl.php` — created ACL config
- `packages/Webkul/CustomerVerification/src/Providers/CustomerVerificationServiceProvider.php` — registered acl.php and lang files
- `packages/Webkul/CustomerVerification/src/Resources/lang/en/app.php` — created
- `packages/Webkul/CustomerVerification/src/Resources/lang/zh_CN/app.php` — created
- `packages/Webkul/CustomerVerification/src/Http/Controllers/Admin/VerificationManagementController.php` — created
- `packages/Webkul/CustomerVerification/src/Routes/admin-routes.php` — created
- `packages/Webkul/CustomerVerification/src/Resources/views/admin/verifications/index.blade.php` — created
- `packages/Webkul/CustomerVerification/src/Resources/views/admin/verifications/show.blade.php` — created
- `packages/Webkul/CustomerVerification/src/Config/admin-menu.php` — created
- `packages/Webkul/CustomerVerification/src/Services/AdminVerificationActionService.php` — created
- `packages/Webkul/CustomerVerification/src/Models/VerificationAuditLog.php` — created
- `packages/Webkul/CustomerVerification/src/Database/Migrations/2026_04_17_add_admin_verification_tables.php` — created
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php` — added verification banner
- `packages/Webkul/Shop/tests/Feature/Admin/VerificationManagementTest.php` — fixed Admin model namespace
- `packages/Webkul/Shop/tests/Feature/Customers/RentalGatingTest.php` — updated to match new listener signature

## Changelog

- 2026-04-17: Story created with admin dashboard + rental gating tasks
- 2026-04-19: Implementation complete — all tasks checked, bugs fixed, 9 tests passing
- 2026-04-25: Verification gating refactored — `PreventUnverifiedRentalAddToCartListener` replaced by `PreventUnverifiedAddToCartListener` (applies to all product types, not just rental). New `VerificationCheckoutMiddleware` added to block unverified customers at order placement. Both gates made admin-configurable via new system config (`customer_verification.checkout.require_verification_add_to_cart` and `customer_verification.checkout.require_verification_checkout`, default on). New `Config/system.php` registered. Translations added for both en and zh_CN.
