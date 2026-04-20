# Story 2.2: Unverified Status & Missing Documents Dashboard

Status: review

<!-- Note: Validation is optional. Run validate-create-story for quality check before dev-story. -->

## Story

As a registered customer,
I want a dashboard that shows my verification status and missing documents,
so that I can complete onboarding and get approved.

## Acceptance Criteria

1. A customer account page shows the current verification status (incomplete, pending, approved, rejected) based on `customers.verification_status`.
2. The page lists required documents (id_document, driver_license, address_proof) and indicates which are uploaded vs missing.
3. Customers can upload missing documents from the dashboard without re-registering.
4. Uploaded files follow the same validation rules and storage paths as registration (file types: ID/Driver/Address; max 5MB per file).
5. When all required documents are uploaded (one of each type present), status automatically changes to `pending` (awaiting admin review).
6. The dashboard integrates into the existing customer account navigation as a new menu item ("Verification Status") and displays at `/customer/verification`.
7. The dashboard uses the existing Bagisto account layout and theme styles (no new UI design work).
8. Implementation uses the custom package created in Story 2.1 (no core package edits).
9. Authorization: Customers can only view/edit their own verification documents; unauthorized access returns 403.
10. Translation keys for labels, status values, document types, buttons, and error messages are added under common translation namespace.

## Tasks / Subtasks

- [x] Task 1: Create authentication, routing, and controller for verification dashboard (AC: 6, 9)
  - [x] Add route `GET /customer/verification` to CustomerVerification package routes
  - [x] Create controller `VerificationDashboardController` with `index()` and optional `store()` actions
  - [x] Add middleware to ensure customer is authenticated and can only access own dashboard (customer.auth)
  - [x] Add authorization check: customer can only view/edit their own records
  - [x] Return 403 Forbidden if customer attempts unauthorized access

- [x] Task 2: Create verification dashboard Blade view and integrate into account navigation (AC: 1, 2, 6, 7)
  - [x] Create view file: `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/verification-dashboard.blade.php`
  - [x] Display current verification status with visual indicator (badge: incomplete/pending/approved/rejected)
  - [x] List all three required document types with upload state (✓ Uploaded / ✗ Missing) and upload date
  - [x] Display rejection reason if status is "rejected" (sourced from audit log or admin notes)
  - [x] Add navigation menu item in account sidebar linking to verification dashboard
  - [x] Use existing account layout component (`x-shop::layouts.account`) and theme styles

- [x] Task 3: Implement document upload form and validation (AC: 3, 4, 10)
  - [x] Add form fields for each missing document type (only show fields for documents not yet uploaded)
  - [x] Apply same file validation as registration: type validation (ID/Driver: PNG/JPG/WEBP, Address: PNG/JPG/PDF), max 5MB per file
  - [x] On form submit, validate files, store to `storage/app/public/customer-documents/{customer_id}/{doc_type}/`
  - [x] Create/update record in `customer_verification_documents` table
  - [x] Handle errors: file upload failures, validation failures, duplicate types
  - [x] Display success or error messages to user

- [x] Task 4: Implement automatic status transition logic (AC: 5)
  - [x] After successful document upload, check if customer now has all three required document types
  - [x] If all three types present AND current status is "incomplete" or "rejected", set status to "pending" and save
  - [x] Do NOT change status if customer is already "approved" (they remain approved)
  - [x] Emit event `customer.verification.documents_complete` when status transitions to pending
  - [x] Log the status change in audit trail with timestamp

- [x] Task 5: Add comprehensive translation keys and labels (AC: 10)
  - [x] Status labels: `common.verification_status_incomplete`, `common.verification_status_pending`, `common.verification_status_approved`, `common.verification_status_rejected`
  - [x] Document type labels: `common.document_type_id_document`, `common.document_type_driver_license`, `common.document_type_address_proof`
  - [x] Form labels: `common.verification_dashboard_title`, `common.verification_upload_label`, `common.verification_upload_button`, `common.verification_status_label`
  - [x] Status messages: `common.verification_all_docs_uploaded`, `common.verification_docs_complete`, `common.verification_rejected_reason`
  - [x] Error messages: `common.verification_file_too_large`, `common.verification_invalid_file_type`, `common.verification_upload_failed`
  - [x] Hints/help text: `common.verification_upload_hint`, `common.verification_document_missing_hint`

- [x] Task 6: Add tests for verification dashboard and upload flow (AC: All)
  - [x] Unit tests for StatusTransitionService (if extracted to service layer)
  - [x] Feature tests for verification dashboard route access and authorization
  - [x] Feature tests for document upload with valid and invalid files
  - [x] Feature tests for status transition logic (all docs uploaded → pending)
  - [x] Feature tests for edge cases: re-upload existing document, upload with wrong file type, file too large
  - [x] Feature tests for rejected status (verify cannot auto-transition without admin action if explicitly rejected)

## Dev Notes

### Core Implementation Requirements

- Use the custom package created in Story 2.1: `Webkul/CustomerVerification`.
- Tables/columns introduced in Story 2.1:
  - `customer_verification_documents` (customer_id, type, path, original_name, mime, size, created_at, updated_at)
  - `customers.verification_status` (values: incomplete, pending, approved, rejected)
- Required document types: `id_document`, `driver_license`, `address_proof` (order does not matter).
- File validation rules (same as registration, see Story 2.1):
  - `id_document`: PNG, JPG, WEBP (max 5MB per file)
  - `driver_license`: PNG, JPG, WEBP (max 5MB per file)
  - `address_proof`: PNG, JPG, PDF (max 5MB per file)
- Storage convention (same as registration):
  - Path: `storage/app/public/customer-documents/{customer_id}/{doc_type}/{filename}`
  - Filename: `{timestamp}_{customer_id}_{document_type}.{extension}` (sanitized)

### Status Transition Logic (CRITICAL)

**Status machine for verification_status column:**
- **incomplete** → pending: ONLY when all three document types exist AND triggered by document upload
- **pending** → approved/rejected: Admin action only (Story 2.3)
- **approved** → (no change): Customers cannot change approved status by uploading
- **rejected** → pending: ONLY if customer uploads after rejection (implicit retry mechanism)
- **rejected** → (no query change): If rejected document already exists, uploading a new version of same type replaces it (soft update)

**Status update trigger:**
- After EACH successful document upload, query: COUNT(DISTincT type) FROM customer_verification_documents WHERE customer_id = X
- If count == 3 (all types present) AND current status IN ('incomplete', 'rejected') → set to 'pending' and save

### Routing & Authentication

- Add routes in `packages/Webkul/CustomerVerification/src/Routes/customer-routes.php`
- Route group: `Route::middleware(['customer.auth'])->prefix('customer')->name('shop.customer.')` (if not already defined)
- Route: `GET /verification` → `VerificationDashboardController@index` (name: `shop.customer.verification.index`)
- Route: `POST /verification/upload` → `VerificationDashboardController@upload` (name: `shop.customer.verification.upload`)
- Authorization: Use Gate or Policy to ensure `$customer->id == auth()->id()` before showing/updating documents
- Return 403 Forbidden for unauthorized access

### View & Navigation Integration

- Theme view location: `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/verification-dashboard.blade.php`
- Reference existing account navigation: `packages/Webkul/Shop/src/Resources/views/customers/account.blade.php` or similar
- Add menu item in account sidebar (look for account navigation component in AutoLeading theme)
- Menu item should highlight if verification status is NOT approved

### Form & Error Handling

- Upload form should include CSRF token
- Use Laravel FormRequest validation (create `CustomerVerificationUploadRequest` in package)
- Validation rules:
  - Each file field: `nullable|file|required_if:document_type_required,true|mimes:png,jpg,webp|max:5120` (for ID/Driver)
  - Each file field: `nullable|file|required_if:document_type_required,true|mimes:png,jpg,webp,pdf|max:5120` (for Address)
- Handle edge cases:
  - Customer uploads when all docs already exist → reject with message
  - Concurrent uploads same document type → queue or lock mechanism to prevent race condition
  - Upload fails partway → rollback transaction, show error, form remains populated
  - File disk full → gracefully handle storage error

### Extraction & Reuse from Story 2.1

- **Validation rules:** Extract into `CustomerVerification\Services\FileValidationService` if not already done
- **File storage:** Reuse method from `HandleCustomerRegistration` listener (Story 2.1)
- **Status initialization:** Story 2.1 sets status during registration; Story 2.2 updates it on dashboard upload
- **Document model:** Reuse `CustomerVerificationDocument` model and repository created in Story 2.1

### Translation & Localization

- Use translation namespace: `common` (per instruction file)
- Keys should follow pattern: `common.verification_*` for verification-specific terms
- All UI text (labels, buttons, status text, errors, hints) MUST use translation keys
- Ensure keys exist in all active language files

### Project Structure Notes

- New routes: `packages/Webkul/CustomerVerification/src/Routes/customer-routes.php` (create if doesn't exist)
- New controller: `packages/Webkul/CustomerVerification/src/Http/Controllers/Customer/VerificationDashboardController.php`
- New form request: `packages/Webkul/CustomerVerification/src/Http/Requests/Customer/CustomerVerificationUploadRequest.php`
- New view: `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/verification-dashboard.blade.php`
- No changes to core Shop package or core Customer package
- Register routes in `CustomerVerificationServiceProvider` or `ModuleServiceProvider`

### References & Dependencies

- Account layout component: `packages/Webkul/Shop/src/Resources/views/customers/account/index.blade.php` (reference for layout)
- Account sidebar navigation: Look for sidebar/menu template in `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/`
- Registration controller (for patterns): `packages/Webkul/CustomerVerification/src/Http/Controllers/Customer/RegistrationController.php`
- Customer model: `packages/Webkul/Customer/src/Models/Customer.php`
- File validation reference: Review `RegistrationWithDocumentsRequest` from Story 2.1
- Bagisto auth middleware: `Webkul\Customer\Http\Middleware\Authenticate` (or `customer.auth` alias)
- Event patterns: Review `EventServiceProvider` and event listeners in CustomerVerification package

### Anti-Patterns to Avoid

- ❌ Do NOT duplicate file validation logic – extract to service if not done in Story 2.1
- ❌ Do NOT check document count manually in controller – use repository query or service
- ❌ Do NOT use hardcoded status strings – use constants or config values
- ❌ Do NOT modify Shop package account views directly – override via theme or publish
- ❌ Do NOT allow customers to change status except via document upload (no manual "request review" button, etc.)
- ❌ Do NOT hardcode file paths – use Storage facade with disk('public')
- ❌ Do NOT skip authorization checks – use Gate or Policy for every customer data access
- ❌ Do NOT store translation keys as UI strings – all text MUST be translated

### Test Strategy

- **Unit**: Status transition logic can be tested separately if extracted to service
- **Feature**: Test full workflow: authenticate → upload 1 doc → verify status not changed → upload 2nd doc → verify still not changed → upload 3rd doc → verify status=pending
- **Edge case**: Upload fails → form still pre-populated, upload same doc twice → should replace, upload after rejection → should transition to pending

## Dev Agent Record

### Agent Model Used

GPT-5.3-Codex

### Debug Log References

- Implemented customer verification route wiring via package provider + `Routes/customer-routes.php`.
- Added dashboard flow classes: request, controller, document service, status service, constants support.
- Added AutoLeading theme dashboard Blade and `common.*` translation keys in EN + ZH locales.
- Executed test suite: `ddev php artisan test packages/Webkul/Shop/tests/Feature/Customers/VerificationDashboardTest.php packages/Webkul/Shop/tests/Feature/Customers/VerificationStatusServiceTest.php` (pass).
- Executed regression check: `ddev php artisan test packages/Webkul/Shop/tests/Feature/RegistrationPageTest.php packages/Webkul/Shop/tests/Feature/Customers/AccountTest.php` (AccountTest pass; existing RegistrationPageTest redirect assertions fail due current registration redirect behavior in baseline code).

### Completion Notes List

- Implemented dashboard route endpoints at `/customer/verification` and `/customer/verification/upload` with customer auth middleware and explicit owner check (`customer_id` mismatch returns 403).
- Implemented verification dashboard UI in AutoLeading theme using existing account layout/navigation.
- Added document state rendering for all required document types with uploaded/missing indicators and timestamps.
- Added upload form for missing documents only, FormRequest validation, and translated error/success messaging.
- Added reusable document service for validation metadata, storage path convention, and upsert-by-document-type behavior.
- Added reusable status service for transition rules (`incomplete/rejected -> pending`, skip approved), event emission, and timestamped audit log entry.
- Added customer menu integration through package config merge (`menu.customer`) without modifying core Shop views.
- Added unit/feature coverage for route auth, upload validation, re-upload replacement, rejected retry transition, and status service behavior.

### File List

- `packages/Webkul/CustomerVerification/src/Providers/CustomerVerificationServiceProvider.php`
- `packages/Webkul/CustomerVerification/src/Config/menu.php`
- `packages/Webkul/CustomerVerification/src/Routes/customer-routes.php`
- `packages/Webkul/CustomerVerification/src/Support/Verification.php`
- `packages/Webkul/CustomerVerification/src/Services/CustomerVerificationDocumentService.php`
- `packages/Webkul/CustomerVerification/src/Services/CustomerVerificationStatusService.php`
- `packages/Webkul/CustomerVerification/src/Http/Requests/Customer/CustomerVerificationUploadRequest.php`
- `packages/Webkul/CustomerVerification/src/Http/Controllers/Customer/VerificationDashboardController.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/verification-dashboard.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/lang/en/app.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/lang/zh_CN/app.php`
- `packages/Webkul/Shop/tests/Feature/Customers/VerificationDashboardTest.php`
- `packages/Webkul/Shop/tests/Feature/Customers/VerificationStatusServiceTest.php`

### Change Log

- 2026-04-16: Implemented Story 2.2 verification dashboard end-to-end (routes, controller/request/services, theme view/menu integration, translations, and test coverage).
