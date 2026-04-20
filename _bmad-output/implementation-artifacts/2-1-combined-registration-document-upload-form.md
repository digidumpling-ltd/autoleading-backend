# Story 2.1: Combined Registration + Document Upload Form

Status: ready-for-dev

<!-- Note: Validation is optional. Run validate-create-story for quality check before dev-story. -->

## Story

As a prospective customer,
I want to register and upload required verification documents in one form,
so that my onboarding can start immediately for admin review.

## Acceptance Criteria

1. Registration form includes standard Bagisto fields (first name, last name, email, password, password confirmation) and adds three required document uploads.
2. Document upload fields match the provided Chinese labels:
  - ID document (upload ID document)
  - Driver license (upload driver license)
  - Address proof (upload address proof)
3. File type validation matches the reference image:
   - ID document: PNG, JPG, WEBP
   - Driver license: PNG, JPG, WEBP
   - Address proof: PNG, JPG, PDF
4. If any required document is missing, registration still succeeds but the customer is marked as unverified for onboarding purposes.
5. Uploaded files are stored using Laravel Storage (public disk) with a structured path per customer.
6. Document metadata (type, path, mime, size, status) is persisted in a dedicated table tied to customer records.
7. Implementation does not modify core packages directly; it uses a custom package and Bagisto extension patterns.
8. UI uses the existing Bagisto registration layout and form components without new design work.

## Tasks / Subtasks

- [ ] Task 1: Create data model for customer verification documents (AC: 5, 6)
  - [ ] Add migration for customer_documents table (customer_id, type, path, mime, size, status, timestamps)
  - [ ] Add optional customer onboarding status field (e.g., verification_status) via migration
  - [ ] Create Eloquent model and repository for documents in a custom package

- [ ] Task 2: Extend registration flow to accept document uploads (AC: 1, 2, 3, 4)
  - [ ] Add document fields to the registration Blade view in the active theme
  - [ ] Implement upload validation for allowed file types and required docs
  - [ ] Persist documents during registration using event hooks or custom controller
  - [ ] Mark onboarding status as unverified/pending when docs missing

- [ ] Task 3: Integrate storage and security requirements (AC: 5, 6, 7)
  - [ ] Store files under a customer-scoped directory with sanitized filenames
  - [ ] Ensure authorization and validation are enforced via FormRequest or equivalent
  - [ ] Add translation keys for labels and errors in common translation files

## Dev Notes

- Follow Bagisto modular package conventions. Do not modify core package source directly.
- Preferred pattern: create a new package (e.g., Webkul/CustomerVerification) that registers:
  - Migrations for document table and onboarding status field
  - Models, repositories, and service provider
  - Route override or event listener for registration to capture file uploads
- Existing registration flow:
  - Controller: Webkul\Shop\Http\Controllers\Customer\RegistrationController
  - Request: Webkul\Shop\Http\Requests\Customer\RegistrationRequest
  - View: packages/Webkul/Shop/src/Resources/views/customers/sign-up.blade.php
- Use event hooks already emitted during registration:
  - customer.registration.before / customer.create.after / customer.registration.after
- Use Laravel Storage and store files in: storage/app/public/customer-documents/{customer_id}/{doc_type}/
- Use customer is_verified for email verification only. Introduce separate onboarding status to avoid conflict.
- Use common translation namespace for button text and labels.

### Project Structure Notes

- New package should live in packages/Webkul/CustomerVerification (or similar) and follow Bagisto package structure.
- Avoid editing core views directly; override the registration view via theme or publish into the custom theme package.

### References

- Registration Controller: packages/Webkul/Shop/src/Http/Controllers/Customer/RegistrationController.php
- Registration Request: packages/Webkul/Shop/src/Http/Requests/Customer/RegistrationRequest.php
- Registration View: packages/Webkul/Shop/src/Resources/views/customers/sign-up.blade.php
- Customer Model: packages/Webkul/Customer/src/Models/Customer.php
- Customers Migration: packages/Webkul/Customer/src/Database/Migrations/2018_07_24_082930_create_customers_table.php

## Dev Agent Record

### Agent Model Used

GPT-5.2-Codex

### Debug Log References

### Completion Notes List

### File List
