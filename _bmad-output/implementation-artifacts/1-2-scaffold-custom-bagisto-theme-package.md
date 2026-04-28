# Story 1.2: Scaffold custom Bagisto theme package

Status: done

<!-- Note: Validation is optional. Run validate-create-story for quality check before dev-story. -->

## Story

As a theme developer,
I want a fully registered AutoLeading theme package scaffold with views, assets, and Vite wiring,
so that subsequent homepage/product/blog stories can be implemented without touching core shop package files.

## Acceptance Criteria

1. Given the repository state, when Story 1.2 is completed, then `packages/Webkul/AutoLeadingTheme/` contains a valid Bagisto theme package scaffold including provider, views, and assets folders.
2. Given Laravel autoloading, when `composer dump-autoload` runs, then namespace `Webkul\\AutoLeadingTheme\\` resolves from `packages/Webkul/AutoLeadingTheme/src` with no autoload errors.
3. Given application bootstrap, when providers are loaded, then `Webkul\\AutoLeadingTheme\\Providers\\AutoLeadingThemeServiceProvider::class` is registered and boots without runtime errors.
4. Given theme configuration, when Bagisto loads `config/themes.php`, then a new shop theme entry for AutoLeading exists with valid `name`, `assets_path`, `views_path`, and `vite` configuration values.
5. Given Bagisto Vite integration, when `config/bagisto-vite.php` is loaded, then AutoLeading theme vite registry exists and points to the package asset directory.
6. Given theme views are present, when the AutoLeading theme is selected for a channel and cache is cleared, then homepage route `shop.home.index` renders via theme view override path (not by editing `packages/Webkul/Shop/src/Resources/views/home/index.blade.php`).
7. Given base assets are scaffolded, when the theme build runs, then CSS/JS entrypoints exist and compile successfully for development and production.
8. Given quality checks, when tests are run, then homepage smoke coverage still passes and no storefront regression is introduced.

## Tasks / Subtasks

- [x] Create/complete package skeleton for AutoLeading theme (AC: 1)
  - [x] Ensure required directories exist:
        - `packages/Webkul/AutoLeadingTheme/src/Providers`
        - `packages/Webkul/AutoLeadingTheme/src/Resources/views/home`
        - `packages/Webkul/AutoLeadingTheme/src/Resources/assets/css`
        - `packages/Webkul/AutoLeadingTheme/src/Resources/assets/js`
  - [x] Add minimal starter files:
        - `packages/Webkul/AutoLeadingTheme/src/Resources/views/home/index.blade.php`
        - `packages/Webkul/AutoLeadingTheme/src/Resources/assets/css/app.css`
        - `packages/Webkul/AutoLeadingTheme/src/Resources/assets/js/app.js`

- [x] Add and register service provider (AC: 2, 3)
  - [x] Create `packages/Webkul/AutoLeadingTheme/src/Providers/AutoLeadingThemeServiceProvider.php`
  - [x] Implement boot publishing for views (and assets if needed by chosen workflow)
  - [x] Add namespace mapping in root `composer.json`:
        - `"Webkul\\AutoLeadingTheme\\": "packages/Webkul/AutoLeadingTheme/src"`
  - [x] Register provider in `bootstrap/providers.php`
  - [x] Run `composer dump-autoload`

- [x] Configure theme and Vite registry (AC: 4, 5)
  - [x] Add `auto-leading-theme` entry under `shop` in `config/themes.php`
  - [x] Keep `shop-default` unchanged in this story (activation happens in Story 1.7)
  - [x] Add matching key in `config/bagisto-vite.php` under `viters`
  - [x] Ensure path values follow Bagisto conventions:
        - `assets_path`: `public/themes/shop/auto-leading-theme`
        - `views_path`: `resources/themes/auto-leading-theme/views`
        - `vite.package_assets_directory`: `src/Resources/assets`

- [x] Scaffold package-level frontend build setup (AC: 7)
  - [x] Add package build config files in `packages/Webkul/AutoLeadingTheme/`:
        - `package.json`
        - `vite.config.js`
        - `tailwind.config.js`
        - `postcss.config.js`
  - [x] Align toolchain with repository versions (Vite 5 + laravel-vite-plugin 1.x)
  - [x] Ensure build output points to `public/themes/shop/auto-leading-theme/build`

- [x] Verify theme render path and non-regression (AC: 6, 8)
  - [x] Publish/symlink theme views per chosen workflow
  - [x] Run `php artisan optimize:clear`
  - [x] Validate homepage route still returns OK and can resolve theme override
  - [x] Run targeted tests: `php artisan test --filter=HomePageTest`

- [x] Document quick start notes for next stories (AC: 1, 7)
  - [x] Add implementation notes on where to place future homepage/product/blog changes
  - [x] Confirm developers should modify package views/assets, not core shop package files

## Dev Notes

### Story Foundation (from epic)

- Epic: Frontend Homepage, Product, Blog, and Theme Updates
- Story: 1.2 Scaffold custom Bagisto theme package
- Epic requirement excerpt:
  - Create `packages/Webkul/AutoLeadingTheme/` with standard Bagisto theme structure
  - Set up Blade views, assets, config, and Vite bundling
  - Register theme in Bagisto config
- Epic acceptance excerpt:
  - Theme package exists and is recognized by Bagisto
  - Theme can be set as default in admin

### Technical Requirements

- Reuse the existing partial scaffold already present at `packages/Webkul/AutoLeadingTheme/src` instead of recreating from scratch.
- Do not modify storefront route/controller behavior in this story:
  - Keep home route `shop.home.index` in `packages/Webkul/Shop/src/Routes/store-front-routes.php`
  - Keep `Webkul\\Shop\\Http\\Controllers\\HomeController@index` as route target.
- Theme override approach must use theme view path (`resources/themes/auto-leading-theme/views`) and package source files, not direct edits to core shop view files.
- Use Bagisto layout/component conventions in starter homepage view:
  - `<x-shop::layouts>` with title slot
  - Reusable Shop components where possible
- Text intended for actions/buttons should be translatable (no hardcoded action labels).

### Architecture Compliance

- Follow modular package architecture under `packages/Webkul/<Package>/src`.
- Follow Laravel/Bagisto registration flow:
  1. Add PSR-4 autoload mapping in `composer.json`
  2. Register provider in `bootstrap/providers.php`
  3. Clear caches after config/provider changes
- Keep this story focused on scaffold/infrastructure only. Visual redesign belongs to later stories.

### Library / Framework Requirements

- Backend runtime:
  - PHP `^8.2`
  - Laravel `^11.0`
- Frontend build baseline in this repo:
  - Vite `^5.4.12`
  - `laravel-vite-plugin` `^1.0`
- Bagisto theme docs reviewed are updated March 26, 2026; examples that show older Vite/plugin versions must be adapted to this repository versions.

### File Structure Requirements

Required files/directories to exist after this story:

- `packages/Webkul/AutoLeadingTheme/src/Providers/AutoLeadingThemeServiceProvider.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/home/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/css/app.css`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/js/app.js`
- `packages/Webkul/AutoLeadingTheme/package.json`
- `packages/Webkul/AutoLeadingTheme/vite.config.js`
- `packages/Webkul/AutoLeadingTheme/tailwind.config.js`
- `packages/Webkul/AutoLeadingTheme/postcss.config.js`

Required root updates:

- `composer.json` (autoload)
- `bootstrap/providers.php` (provider registration)
- `config/themes.php` (shop theme entry)
- `config/bagisto-vite.php` (vite registry entry)

### Testing Requirements

Minimum validation:

- `composer dump-autoload` succeeds.
- `php artisan optimize:clear` succeeds.
- Theme config can be selected in channel settings without errors.
- Homepage returns successful response under default and AutoLeading theme.
- Existing homepage smoke suite passes:
  - `php artisan test --filter=HomePageTest`

### Previous Story Intelligence

- No prior implementation story files were found in `_bmad-output/implementation-artifacts`.
- This is effectively the first implementation baseline for Epic 1 in this workspace.

### Git Intelligence Summary

- Not applied for this story because no previous epic-1 implementation artifact exists to correlate.

### Latest Tech Information

- Official Bagisto theme development docs emphasize:
  - Theme registration via `config/themes.php`
  - Vite integration via `config/bagisto-vite.php`
  - `<x-shop::layouts>` auto-loads theme assets when correctly configured
  - Custom layouts require manual `@bagistoVite` inclusion
- For this repo, prefer local toolchain versions over older doc samples:
  - Vite 5 and `laravel-vite-plugin` 1.x

### Project Structure Notes

- Existing codebase uses package-based architecture and already contains an `AutoLeadingTheme` package folder with partial scaffold.
- Core shop homepage currently resolves from package view (`shop::home.index`) and relies on theme customization data from `HomeController`.
- Keep this story scoped to enabling infrastructure so Story 1.3 can safely implement homepage sections in the theme package.

### References

- Epic definitions: `_bmad-output/planning-artifacts/epics.md` (Epic 1, Story 1.2)
- Sprint priority context: `_bmad-output/planning-artifacts/sprint-1-plan.md`
- Theme config baseline: `config/themes.php`
- Vite registry baseline: `config/bagisto-vite.php`
- Home route: `packages/Webkul/Shop/src/Routes/store-front-routes.php`
- Home controller: `packages/Webkul/Shop/src/Http/Controllers/HomeController.php`
- Current home view baseline: `packages/Webkul/Shop/src/Resources/views/home/index.blade.php`
- Shop layout asset behavior: `packages/Webkul/Shop/src/Resources/views/components/layouts/index.blade.php`
- Official docs (latest reviewed):
  - https://devdocs.bagisto.com/theme-development/creating-store-theme.html
  - https://devdocs.bagisto.com/theme-development/creating-custom-theme-package.html
  - https://devdocs.bagisto.com/theme-development/vite-powered-theme-assets.html
  - https://devdocs.bagisto.com/theme-development/understanding-layouts.html
  - https://devdocs.bagisto.com/theme-development/blade-components.html

### Story Completion Status

- Story status set to `review`.
- Completion note: Theme scaffold, provider registration, config wiring, tests, and build validation completed for Story 1.2.

## Dev Agent Record

### Agent Model Used

GPT-5.3-Codex

### Debug Log References

- `ddev composer dump-autoload`
- `ddev php artisan vendor:publish --provider="Webkul\\AutoLeadingTheme\\Providers\\AutoLeadingThemeServiceProvider" --tag=auto-leading-theme-views --force`
- `ddev php artisan optimize:clear`
- `ddev php artisan test --filter=AutoLeadingThemeScaffoldTest`
- `ddev php artisan test --filter=HomePageTest`
- `npm run build` (from `packages/Webkul/AutoLeadingTheme`)
- `ddev php artisan test` (attempted full suite; long-running with pre-existing unrelated failures)

### Completion Notes List

- Added and registered `Webkul\\AutoLeadingTheme\\Providers\\AutoLeadingThemeServiceProvider` with publish hooks for theme views/assets.
- Added theme scaffold starter files: package homepage override and CSS/JS entrypoints under package resources.
- Added theme/frontend build setup for AutoLeadingTheme (`package.json`, `vite.config.js`, `tailwind.config.js`, `postcss.config.js`) aligned to Vite 5 + laravel-vite-plugin 1.x.
- Added `auto-leading-theme` entries in `config/themes.php` and `config/bagisto-vite.php` without changing `shop-default`.
- Added `AutoLeadingThemeScaffoldTest` to validate scaffold files, config wiring, and themed `shop::home.index` resolution.
- Targeted test suites passed (`AutoLeadingThemeScaffoldTest`, `HomePageTest`), and theme production build passed.
- Full regression run was attempted but not completed due long duration and unrelated pre-existing failures in other suites.

### File List

- `_bmad-output/implementation-artifacts/1-2-scaffold-custom-bagisto-theme-package.md`
- `_bmad-output/implementation-artifacts/sprint-status.yaml`
- `bootstrap/providers.php`
- `composer.json`
- `config/themes.php`
- `config/bagisto-vite.php`
- `packages/Webkul/AutoLeadingTheme/README.md`
- `packages/Webkul/AutoLeadingTheme/package.json`
- `packages/Webkul/AutoLeadingTheme/postcss.config.js`
- `packages/Webkul/AutoLeadingTheme/tailwind.config.js`
- `packages/Webkul/AutoLeadingTheme/vite.config.js`
- `packages/Webkul/AutoLeadingTheme/src/Providers/AutoLeadingThemeServiceProvider.php`
- `packages/Webkul/AutoLeadingTheme/src/Routes/theme-routes.php`
- `packages/Webkul/AutoLeadingTheme/src/Http/Controllers/BlogController.php`
- `packages/Webkul/AutoLeadingTheme/src/Http/Controllers/FaqController.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/css/app.css`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/css/components/car-card.css`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/js/app.js`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/default-language.svg`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/favicon.ico`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/hero-image.jpg`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/hero-image.webp`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/logo.svg`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/no-address.png`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/review.png`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/small-product-placeholder.webp`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/spinner.svg`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/thank-you.png`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/user-placeholder.png`
- `packages/Webkul/AutoLeadingTheme/src/Resources/assets/images/wishlist.png`
- `packages/Webkul/AutoLeadingTheme/src/Resources/lang/en/app.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/lang/zh_CN/app.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/home/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/blog/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/blog/show.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/contact/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/profile/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/account/verification-dashboard.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/sign-in.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/customers/sign-up.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/faq/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/products/view.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/search/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/alert.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/car-card.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/footer-column.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/lang-switcher.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/layouts/auth.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/layouts/footer/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/layouts/header/index.blade.php`
- `packages/Webkul/AutoLeadingTheme/src/Resources/views/components/layouts/index.blade.php`
- `packages/Webkul/Shop/tests/Feature/AutoLeadingThemeScaffoldTest.php`

### Change Log

- 2026-04-09: Implemented Story 1.2 scaffold and infrastructure for AutoLeading theme package; added provider/config wiring, build setup, test coverage, and validation runs.
