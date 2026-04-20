# Story 2.2: Unverified Status & Missing Documents Dashboard

Status: ready-for-dev

<!-- Note: Validation is optional. Run validate-create-story for quality check before dev-story. -->

## Story

As a registered customer,
I want a dashboard that shows my verification status and missing documents,
so that I can complete onboarding and get approved.

## Acceptance Criteria

1. A customer account page shows the current verification status (incomplete, pending, approved, rejected) based on `customers.verification_status`.
2. The page lists required documents and indicates which are uploaded vs missing.
3. Customers can upload missing documents from the dashboard without re-registering.
4. Uploaded files follow the same validation rules and storage paths as registration.
5. When all required documents are uploaded, status changes to `pending` (awaiting admin review).
6. The dashboard uses the existing Bagisto account layout and theme styles (no new UI design work).
7. Implementation uses the custom package created in Story 2.1 (no core package edits).
8. Translation keys for labels and messages are added under the common translation namespace.

## Tasks / Subtasks

- [ ] Task 1: Create customer account dashboard page (AC: 1, 2, 6)
  - [ ] Add a new customer account route and controller action in the custom package
  - [ ] Create a Blade view in the AutoLeading theme under customers/account
  - [ ] Render verification status and document list with upload state

- [ ] Task 2: Implement missing document upload flow (AC: 3, 4)
  - [ ] Add upload form fields for missing document types
  - [ ] Reuse validation rules from registration documents
  - [ ] Store documents in `customer_verification_documents` and update status

- [ ] Task 3: Status transition and messaging (AC: 5, 8)
  - [ ] When all required docs exist, set `verification_status` to `pending`
  - [ ] Add translation keys for status labels, hints, and upload button text
  - [ ] Add tests for status and uploads from the dashboard

## Dev Notes

- Use the custom package created in Story 2.1: `Webkul/CustomerVerification`.
- Tables/columns introduced in Story 2.1:
  - `customer_verification_documents`
  - `customers.verification_status`
- Required document types: `id_document`, `driver_license`, `address_proof`.
- Reuse validation rules and storage path conventions used in registration:
  - Storage path: `storage/app/public/customer-documents/{customer_id}/{doc_type}/`
  - Allowed file types match the registration rules.
- Use the existing customer account layout (`x-shop::layouts.account`) and theme conventions.
- Use Bagisto routing and middleware for authenticated customer pages.
- Button text and labels should use common translation keys per instructions.

### Project Structure Notes

- New routes and controller should live under the custom package, not core Shop package.
- Theme view should live under `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/`.

### References

- Account layout: packages/Webkul/Shop/src/Resources/views/customers/account/index.blade.php
- Registration controller (custom): packages/Webkul/CustomerVerification/src/Http/Controllers/Customer/RegistrationController.php
- Registration view: packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/sign-up.blade.php
- Customer model: packages/Webkul/Customer/src/Models/Customer.php

## Dev Agent Record

### Agent Model Used

GPT-5.2-Codex

### Debug Log References

### Completion Notes List

### File List
