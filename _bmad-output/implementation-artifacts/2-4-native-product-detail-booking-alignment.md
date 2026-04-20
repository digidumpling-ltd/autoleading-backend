# Story 2.4: Native Product Detail Booking Alignment (Theme Only)

Status: review

## Story

As a theme developer,
I want the AutoLeading product detail page to follow Bagisto's native product-detail structure and booking flow,
so that booking products work end-to-end without hardcoded UI/data and without backend customization.

## Acceptance Criteria

1. Given the AutoLeading theme is active, when a product detail page loads, then the page structure follows Bagisto's native product view flow (meta, form, type includes, actions) instead of hardcoded product fields.

2. Given a booking product is viewed, when the page renders, then native booking product type rendering is present via Bagisto product-type includes and booking inputs use native payload keys.

3. Given non-booking products are viewed, when the page renders, then existing native product type behavior (simple/configurable/grouped/bundle/downloadable) continues to work without regression.

4. Given product attributes vary by item, when detail page sections render, then specifications/additional information are data-driven from Bagisto attribute/view helpers rather than hardcoded rows.

5. Given related and upsell sections render, when products are loaded, then they use native association flows/components (no inline repository query in Blade).

6. Given an authenticated customer with non-approved verification status views a rental booking product, when action area renders, then the verification guidance banner is shown and booking action is gated per project policy.

7. Given theme overrides are used, when runtime view and package source are compared, then the active runtime file is the authoritative implementation and package source is kept in sync only as needed for maintainability.

8. Given tests are run, when product detail page scenarios execute, then booking and non-booking render paths pass with no hardcoded-content dependency.

## Tasks / Subtasks

- [x] Task 1: Baseline and map native Bagisto product-detail building blocks (AC: 1, 2, 3)
  - [x] Reference native file: packages/Webkul/Shop/src/Resources/views/products/view.blade.php
  - [x] Identify required blocks to preserve: form shell, type includes, quantity/action area, associations, tabs/accordions

- [x] Task 2: Refactor active AutoLeading product view to native-compatible structure (AC: 1, 2, 3, 4)
  - [x] Target runtime override file first: resources/themes/auto-leading-theme/views/products/view.blade.php
  - [x] Remove hardcoded specs/features/rating placeholders
  - [x] Use helper-driven additional data and real product content sections

- [x] Task 3: Keep booking flow native while preserving project-specific gate UX (AC: 2, 6)
  - [x] Ensure booking type include path is retained in product type includes
  - [x] Keep verification banner logic and map gate behavior to accepted policy

- [x] Task 4: Remove inline repository queries from Blade and use native association presentation (AC: 5)
  - [x] Replace hardcoded related-product query block with native related/up-sell components

- [x] Task 5: Sync source template and runtime override strategy (AC: 7)
  - [x] Decide whether package source file must mirror runtime override
  - [x] Document precedence and maintenance note in story implementation notes

- [x] Task 6: Test coverage updates (AC: 8)
  - [x] Add/adjust feature tests for booking product render path
  - [x] Add/adjust feature tests for non-booking product render path
  - [x] Assert no hardcoded placeholder text appears in final rendering

## Dev Notes

- Scope is frontend/theme and view composition only.
- Do not customize booking engine, order lifecycle logic, or core backend behavior in this story.
- Prefer Bagisto native view flow first, then theme-specific wrappers/styling.
- Active runtime theme path currently exists under resources/themes/auto-leading-theme/views.

## Dev Agent Record

### Implementation Plan

Refactored `packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php` to follow the native Bagisto product view structure while preserving AL-theme CSS classes for styling.

**Task 5 — Runtime Override Strategy:**
`resources/themes/auto-leading-theme/views` is a symlink to `packages/Webkul/AutoLeadingTheme/src/Resources/views`. They are the same file — no separate sync needed. Any edit to the package source is immediately the runtime override.

### Completion Notes

- Removed all hardcoded specs (transmission, engine, seats, year with fallback values), hardcoded features list, and hardcoded star rating `★★★★★`
- Added `@inject` for `reviewHelper` and `productViewHelper` — ratings and additional info are now fully data-driven
- Preserved AL breadcrumb and `al-` CSS classes for theming
- Included all native type includes: simple, configurable, grouped, bundle, downloadable, **booking**
- Fixed verification banner type check: was `'rental'`, corrected to `'booking'` to match Bagisto's actual product type key
- Replaced inline `ProductRepository::scopeQuery(...)` related-product block with native `<v-product-associations />` Vue component using lazy-load IntersectionObserver
- Added SEO meta push, og/twitter meta tags, full Vue `v-product` component with add-to-cart, wishlist, compare, buy-now, scrollToReview methods
- Added 6 feature tests in `packages/Webkul/Shop/tests/Feature/Product/ProductDetailViewTest.php` covering AC 1, 3, 4, 5, 6; all pass

### Debug Log

- Pre-existing `HomePageTest` failures are unrelated (search page override — see project memory)

## File List

- packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php (modified)
- packages/Webkul/Shop/tests/Feature/Product/ProductDetailViewTest.php (added)
- _bmad-output/implementation-artifacts/sprint-status.yaml (updated)
- _bmad-output/implementation-artifacts/2-4-native-product-detail-booking-alignment.md (updated)

## Change Log

- 2026-04-19: Refactored AutoLeading product detail view to native Bagisto structure; removed hardcoded content; added native type includes, associations, SEO meta, and real ratings; fixed verification banner type check; added 6 passing feature tests.
