---
stepsCompleted:
  - step-01-validate-prerequisites
  - step-03-create-stories
inputDocuments:
  - _bmad-output/planning-artifacts/prd.md
  - _bmad-output/planning-artifacts/product-brief-bagisto-2026-03-14.md
  - _bmad-output/planning-artifacts/bagisto-theme-implementation-plan.md
designSources:
  - https://autoleading.net/ (reference website)
---

# bagisto - Epic Breakdown

## Overview

This document provides the complete epic and story breakdown for bagisto, decomposing the requirements from the PRD, UX Design if it exists, and Architecture requirements into implementable stories.

## Requirements Inventory

### Functional Requirements

FR1: The system shall allow customers to purchase membership plans and convert payment value into purchased credit at a 1:1 ratio.

FR2: The system shall maintain two separate wallet balances per customer: purchased credit and bonus credit.

FR3: The system shall deduct credits when admin confirms an order (creates an invoice), not at booking placement time, using deterministic priority: bonus credit first, then purchased credit.

FR4: The system shall validate available credit at booking time and block booking completion when available credit is insufficient.

FR5: The system shall block booking completion when wallet balance is insufficient and redirect the customer to top up their wallet before retrying.

FR6: The system shall enforce per-product/per-car refundability configuration.

FR7: The system shall support configurable refund destination routing between account transfer and store credit.

FR8: The system shall support admin-issued credit adjustments (grant/add) for authorized users only.

FR9: The system shall record all balance-changing events in an immutable credit ledger with actor, reason, source event, and timestamp.

FR10: The system shall support credit expiry policies for both lifetime and expiring credits.

FR11: The system shall support cancellation/reversal flows that update wallet balances and ledger entries consistently.

FR12: The system shall support recurring customer use cases including repeated bookings, bonus grants, and balance visibility.

FR13: Wallet credit shall be deducted when admin creates an invoice or confirms the order — not at booking placement. Booking placement places the order in pending status with no charge.

FR14: Customer membership level (Bagisto customer group) shall be automatically updated after each invoice confirmation based on cumulative lifetime confirmed spend:
  - Default: $0 – $2,999.99 (no group / standard)
  - Gold Member: $3,000+
  - Platinum Member: $30,000+
  - Diamond Member: $60,000+

FR15: Jadeite Member (invitation only) shall never be auto-assigned. It is assigned exclusively by admin from the admin dashboard and is never auto-downgraded regardless of spend.

### NonFunctional Requirements

NFR1: Membership-to-credit issuance must be atomic with no partial success state.

NFR2: The platform must guarantee no negative wallet balance states.

NFR3: Deduction and refund policy behaviors must be deterministic and test-covered.

NFR4: All credit mutations must be auditable and searchable.

NFR5: Access control must prevent unauthorized wallet mutations.

NFR6: Core credit domain flows must be covered by automated unit and feature tests.

NFR7: Critical acceptance scenarios for insufficient-balance blocking, deduction order, and refund routing must meet 100% pass rate targets.

NFR8: Transaction integrity must achieve zero unreconciled wallet transactions in test/staging.

NFR9: The ledger must be sufficient to reconstruct historical and current balances.

NFR10: Membership level auto-assignment must be idempotent — re-running the assignment for the same invoice must not change group if already at correct level, and must never downgrade a Jadeite member.

### Additional Requirements

- No architecture decision document was found in planning artifacts; architecture-specific setup requirements remain to be defined.
- Phase 1 scope prioritizes store credit and explicitly defers POS workflows to a later phase.
- MVP must align with Bagisto package patterns (modular package design, repository pattern, event-driven integration points).
- **Booking mechanism**: Bookings use Bagisto's built-in COD (Cash on Delivery) payment method behavior + free shipping, so orders go directly to admin with status `pending` by default. `WalletPayment` replicates this COD-style flow (no redirect, no deduction at placement). Wallet deduction is deferred to invoice confirmation.
- **Membership levels in Bagisto**: Implemented as Bagisto Customer Groups. Five groups must be seeded: Default, Gold Member, Platinum Member, Diamond Member, Jadeite Member.

### UX Design Requirements

No UX design specification document was found in planning artifacts at this step.

### FR Coverage Map

| Functional Requirement | Covered by Epic(s) |
|---|---|
| FR1: Customer purchase membership plans | Epic 2: Membership Credit Activation |
| FR2: Dual wallet balances (purchased/bonus) | Epic 3: Wallet Ledger Foundation |
| FR3: Deterministic deduction (bonus-first) | Epic 4: Booking Payment with Wallet Credit |
| FR4: Available credit validation at booking | Epic 4: Booking Payment with Wallet Credit |
| FR5: Insufficient balance block + redirect to top-up | Epic 3: Wallet Ledger Foundation (top-up) + Epic 4: Booking Payment (block) |
| FR6: Per-product refundability config | Epic 5: Refunds, Reversals, and Policy Enforcement |
| FR7: Configurable refund routing | Epic 5: Refunds, Reversals, and Policy Enforcement |
| FR8: Admin credit adjustments | Epic 6: Admin Governance and Operational Control |
| FR9: Immutable credit ledger | Epic 3: Wallet Ledger Foundation |
| FR10: Credit expiry policies | Epic 3: Wallet Ledger Foundation |
| FR11: Cancellation/reversal flows | Epic 5: Refunds, Reversals, and Policy Enforcement |
| FR12: Recurring customer use cases | Epic 2-5: Integrated workflow |
| FR13: Wallet deduction at invoice confirmation (not booking) | Epic 4: Booking Payment with Wallet Credit |
| FR14: Auto membership level update on invoice confirmation | Epic 8: Customer Membership Level Management |
| FR15: Jadeite invitation-only — admin manual assign only | Epic 8: Customer Membership Level Management |

## Epic List



## Epic 1: Frontend Theme & Car Rental Shop Implementation

**Goal**: Implement a custom AutoLeading-branded Bagisto theme with all shop pages, enabling customers to browse rental vehicles with an intuitive, visually-appealing interface.

**Requirements Addressed**: UX-DR1 through UX-DR14 (all UX design requirements)

**UX Components Covered**:
- Header/Navigation (menu, logo, language switcher, auth links)
- Hero section with search bar
- Car type selector (Sedan, Sports, SUV, Convertible)
- Featured cars grid with cards and filtering
- Product list/search results
- Product detail page (gallery, specs, booking CTA)
- Blog, Contact, FAQ pages
- Registration/Login pages
- Footer with multi-column layout
- Design tokens: Orange (#d18a1b), black, white, beige backgrounds
- Responsive & accessible layouts

### Story 1.1: Scaffold AutoLeading Theme Package Structure

**As a** developer,
**I want** to set up the AutoLeading theme package following Bagisto conventions,
**So that** I have a solid foundation for building all theme components and pages.

**Acceptance Criteria:**

**Given** I run the theme scaffolding command
**When** the theme package is created at `packages/Webkul/AutoLeadingTheme/`
**Then** the following directory structure exists:
  - `src/Resources/views/` (for Blade templates)
  - `src/Resources/assets/css/` and `src/Resources/assets/js/` (for styles and scripts)
  - `src/Config/` (for theme configuration)
  - `vite.config.js` (for asset bundling configuration)
  - `package.json` (for npm dependencies like Tailwind)

**And** theme configuration includes:
  - Primary color: #d18a1b (orange)
  - Secondary colors: black, white, light beige
  - Font family: modern sans-serif
  - Logo path configuration

**And** when I run `composer dump-autoload`
**Then** Bagisto recognizes the new theme package

**And** when I log in to Bagisto admin
**Then** the AutoLeading theme appears in the themes list and can be set as default

---

### Story 1.2: Create Reusable Blade Components Library

**As a** developer,
**I want** to build a library of reusable Blade components for common UI elements,
**So that** I can reuse them across all pages and maintain visual consistency.

**Acceptance Criteria:**

**Given** I need to display reusable UI elements
**When** I create components in `src/Resources/views/components/`
**Then** the following reusable components are created:
  - `button.blade.php` (primary orange button with variants: primary, secondary, small, large)
  - `card.blade.php` (generic card container with title, image, content slots)
  - `search-select.blade.php` (dropdown select with search functionality)
  - `badge.blade.php` (tag/badge component with configurable colors and styles)
  - `hero-banner.blade.php` (full-width banner with background image and overlay text)
  - `grid.blade.php` (responsive grid container with configurable column count)
  - `navigation-link.blade.php` (header nav link with active state highlighting)
  - `footer-column.blade.php` (footer column with title and links list)
  - `image-gallery.blade.php` (carousel/gallery with thumbnails and keyboard/touch navigation)
  - `accordion.blade.php` (expandable/collapsible item with animations)

**And** each component has:
  - Clear prop documentation in comments
  - Support for custom CSS classes via `@class` directive
  - Tailwind utility classes for styling
  - Slot support for flexible content

**And** when I use any component in a view
**Then** I can pass props to customize it without modifying the component file

**And** all components are accessible:
  - Semantic HTML tags
  - Proper color contrast (WCAG AA standard)
  - Keyboard navigable interactive elements
  - Proper ARIA attributes for complex components

---

### Story 1.3: Implement Header & Navigation

**As a** customer,
**I want** to see a top navigation bar with logo, menu links, language switcher, and login button,
**So that** I can navigate easily between pages and manage my account.

**Acceptance Criteria:**

**Given** I visit the store homepage
**When** the page loads
**Then** I see a sticky header with:
  - Logo (left side, clickable to homepage, properly sized)
  - Navigation menu (Home, Car Models, About, Blog, Membership, FAQ, Contact)
  - Language switcher (dropdown for English/Chinese with proper symbols)
  - Login/Register link (top right, orange text or button styling)

**And** the header uses the reusable `navigation-link` component
**And** the header background is white with proper shadow/contrast
**And** the logo color and branding follows AutoLeading design
**And** the header sticky positioning works on all screen sizes

**And** when I click any navigation link
**Then** I am taken to the correct page
**And** the active link is highlighted/underlined

**And** when I resize the browser to mobile width (< 768px)
**Then** the navigation becomes a hamburger menu icon (three horizontal lines)
**And** the menu expands/collapses when clicked
**And** all links are still accessible and properly spaced for touch (min 48px height)

**And** when I click outside the mobile menu
**Then** the menu collapses automatically

**And** when I click the language switcher
**Then** the page reloads in the selected language
**And** all UI text switches (menu, buttons, section headings, footer)

---

### Story 1.4: Implement Hero Section with Search Bar

**As a** visitor,
**I want** to see an eye-catching hero banner with a search form to find rental cars,
**So that** I can quickly search for vehicles by brand, type, and category.

**Acceptance Criteria:**

**Given** I land on the homepage
**When** the page loads
**Then** I see a hero section with:
  - Full-width background image of premium car in action
  - Overlay text: "Management Your Rental Cars Professionally" (large, white, bold, centered)
  - Search bar positioned below text with 3 dropdowns: Brand, Type, Category
  - Orange search button with icon on the right side of search bar

**And** the `hero-banner` and `search-select` components are used for consistency
**And** the hero section uses CSS background-image with proper cover/position
**And** the hero section is responsive:
  - On desktop: full-width image with centered search bar
  - On tablet: image height reduced, search bar adjusted
  - On mobile: background image scaled down, search bar full-width below image

**And** when I click on each dropdown
**Then** I see predefined options displayed:
  - Brand: Audi, Mercedes Benz, BMW, Maserati, etc.
  - Type: Sedan, Sports, SUV, Convertible
  - Category: All, New Arrivals, Hot Deals, Special Offers

**And** when I click the search button
**Then** I am taken to the product list page with selected filters applied in URL query parameters
**And** previously selected filters are preserved in the dropdowns

**And** all form elements have proper labels and accessibility attributes
**And** keyboard navigation works: Tab to move between fields, Enter to search

---

### Story 1.5: Implement Car Type Selector & Featured Cars Grid

**As a** customer,
**I want** to see car type categories as visual icons and a grid of featured cars,
**So that** I can quickly explore vehicles by type and see popular rental options.

**Acceptance Criteria:**

**Given** I scroll down on the homepage
**When** I reach the featured cars section
**Then** I see:
  - Section heading: "超值 精選車款" (Gold/orange color #d18a1b, bold font)
  - 4 car type selector buttons horizontally arranged with icons:
    - 轎車 (Sedan) with sedan icon
    - 跑車 (Sports) with sports car icon
    - 多功能車 (SUV) with SUV icon
    - 敞篷車 (Convertible) with convertible icon
  - Featured cars grid displaying 4 cars initially in a row

**And** each car card uses the `card` component with:
  - Car image (thumbnail, square or 4:3 aspect ratio)
  - Car name (bold, dark text)
  - Daily rental price (USD format, large text)
  - Orange "New" or "Hot Deal" badge using `badge` component (top-right corner)
  - Call-to-action button (orange background, white text) with "View Details" text

**And** when I click a car type button
**Then** the featured cars grid updates to show only cars of that type
**And** the selected button is highlighted with orange background/border
**And** transition animation shows the grid refresh

**And** when I click a car card or "View Details" button
**Then** I am taken to the product detail page for that car

**And** the grid is responsive:
  - Desktop (1920px+): 4 columns
  - Tablet (768-1024px): 2 columns with increased card size
  - Mobile (<768px): 1 column, full width

**And** all components have proper hover states:
  - Card shadow increases slightly on hover
  - Button background darkens on hover
  - Smooth transitions (0.3s ease recommended)

---

### Story 1.6: Implement Product List & Search Results Page

**As a** customer,
**I want** to browse a complete list of available rental vehicles with filters and sorting options,
**So that** I can find the perfect car based on my preferences.

**Acceptance Criteria:**

**Given** I navigate to the product list page (from hero search or "Car Models" menu)
**When** the page loads
**Then** I see a two-column layout:
  - Left sidebar (25-30% width) with filter options:
    - Brand filter (checkbox list with car brand names)
    - Type filter (checkbox list: Sedan, Sports, SUV, Convertible)
    - Price range filter (slider input with min/max values, or dual range slider)
  - Main content area (70-75% width) with:
    - Sorting dropdown (Options: Price Low-High, Price High-Low, Newest, Most Popular)
    - Grid of car cards (3 columns on desktop, 2 on tablet, 1 on mobile)
    - Pagination controls (Previous, page numbers 1-N, Next, items per page selector)

**And** car cards display:
  - Image thumbnail, name, price, "New/Hot" badge
  - "View Details" button (orange CTA)

**And** when I select filter checkboxes
**Then** the product list updates to show only matching cars
**And** pagination resets to page 1
**And** the URL updates with query parameters (e.g., ?brand=audi&type=sports&minPrice=500&maxPrice=2000)
**And** the number of results is displayed ("X cars found")

**And** when I change the sorting dropdown
**Then** the product list re-sorts without page refresh
**And** the sort order persists in URL

**And** when I adjust the price range slider
**Then** the list filters automatically with slight debounce (300-500ms)

**And** all filters are keyboard accessible and properly labeled
**And** I can use Tab to navigate filters and Enter to activate checkboxes

**And** when I click a product card
**Then** I am taken to the product detail page for that car

**And** when I click pagination numbers
**Then** the page scrolls to top and displays the selected page results

---

### Story 1.7: Implement Product Detail Page

**As a** customer,
**I want** to view comprehensive details about a specific rental car with images, specs, and booking option,
**So that** I can decide if this car meets my needs before booking.

**Acceptance Criteria:**

**Given** I click on a car from the product list or featured section
**When** I am taken to the product detail page
**Then** I see a two-column layout:
  - Left column (50-60%): Large product image gallery with main image, thumbnail carousel
  - Right column (40-50%) with scrollable content:
    - Car name (very large, bold heading)
    - Daily rental price (prominent, large orange text)
    - Star rating display (if available)
    - Product specifications table:
      - Transmission, Engine, Seats, Year Model, Mileage, Features, etc.
    - Features list (bullet points with icons)
    - Rich text description section
    - Related products section (similar cars, 3-4 cards)
    - "Book Now" button (large, orange, sticky on mobile scroll)

**And** the image gallery functionality:
  - Clicking thumbnails in carousel changes main image
  - Keyboard arrows (left/right) navigate through images
  - Touch swipe on mobile navigates gallery
  - Main image displays at high resolution
  - Thumbnails show 3-4 images visible, scrollable

**And** the "Book Now" button:
  - Orange background (#d18a1b), white text, rounded corners
  - Sticky position on mobile (bottom of viewport)
  - Redirects to checkout/booking page on click

**And** breadcrumb navigation shows:
  - Home > Car Models > [Brand] > [Car Name]
  - Breadcrumb links are clickable

**And** the page is fully responsive:
  - Desktop: two-column side-by-side layout
  - Tablet: image on top, details below, stacked layout
  - Mobile: full-width sections stacked vertically

**And** metadata elements:
  - Page title set to car name (for SEO)
  - Meta description includes price and key specs
  - Open Graph tags for social sharing (image, title, description)

---

### Story 1.8: Implement Blog, Contact, and FAQ Pages

**As a** customer,
**I want** to access blog articles, contact information, and FAQ answers,
**So that** I can learn about the rental service and get support.

**Acceptance Criteria:**

**Blog Page:**

**Given** I navigate to the Blog page (from header menu)
**When** the page loads
**Then** I see:
  - Page heading: "Latest News & Updates"
  - Blog post list with cards showing:
    - Thumbnail image (4:3 aspect ratio)
    - Post title (bold, clickable)
    - Publish date (small text)
    - Excerpt/summary (2-3 lines of text)
    - "Read More" link (orange text)
  - Pagination for blog posts (10 posts per page default)

**And** when I click "Read More" or post title
**Then** I see the full blog post with:
  - Title, publish date, author
  - Featured image (full-width)
  - Full content (rich text with proper formatting)
  - Related posts section (3 similar articles)
  - Back to blog link

**Contact Page:**

**Given** I navigate to the Contact page
**When** the page loads
**Then** I see a layout with:
  - Left side: Business information section
    - Business name
    - Business hours (formatted table or list)
    - Contact methods: Phone number (clickable tel: link), Email (clickable mailto: link)
    - Office address (full address with postal code)
  - Right side: Contact form with fields:
    - Name (text input, required)
    - Email (email input, required, validated)
    - Phone (tel input, optional)
    - Message (textarea, required, min 10 chars)
    - Submit button (orange CTA)
  - Footer section with social media icons (clickable, open in new tab)

**And** when I fill the form and click Submit
**Then** form validation occurs (required fields, email format, message length)
**And** success message displays: "Thank you! We'll get back to you soon."
**And** form data is sent to admin email

**FAQ Page:**

**Given** I navigate to the FAQ page
**Then** I see:
  - Page heading: "Frequently Asked Questions"
  - Search box: "Search FAQs by keyword..."
  - List of FAQ accordion items (all initially collapsed)

**And** each accordion item shows:
  - Question title (clickable header)
  - Answer text (hidden until expanded)

**And** when I click a question title
**Then** the answer expands with smooth animation (0.3s)
**And** the other items remain in their current state (not auto-collapsing)

**And** when I type in the search box
**Then** the FAQ list filters to show only matching questions/answers
**And** matching keywords are highlighted in results

**And** when I click an expanded item again
**Then** it collapses with smooth animation

---

### Story 1.9: Implement Registration & Login Pages

**As a** new customer,
**I want** to register for an account and log in,
**So that** I can book rental cars and manage my account.

**Acceptance Criteria:**

**Login Form:**

**Given** I click the "Login" link in the header
**When** I am taken to the auth page
**Then** I see a login form with:
  - Email input field (with "Email" placeholder)
  - Password input field (with "Password" placeholder, masked text)
  - "Remember Me" checkbox
  - "Login" button (orange, large, full-width or centered)
  - "Forgot Password?" link (gray text, clickable)
  - "Don't have an account? Register here" link or button (switch to registration)

**Registration Form:**

**Given** I click "Register" link or button
**When** the form switches to registration
**Then** I see a registration form with:
  - Full Name field (required, text input)
  - Email field (required, email input with validation)
  - Password field (required, masked, with strength indicator)
    - Shows text like "Weak / Medium / Strong" below field
    - Color coding: red (weak), yellow (medium), green (strong)
  - Confirm Password field (required, masked)
  - Phone number field (optional, tel input)
  - "Register" button (orange, large, full-width)
  - "Already have an account? Login here" link or button

**Form Validation:**

**And** when I interact with form fields
**Then** validation occurs:
  - Required fields are marked with asterisk (*)
  - Email format validation ("Invalid email format" message)
  - Password strength requirements displayed:
    - Minimum 8 characters
    - Must contain uppercase, lowercase, number, special character
  - Passwords must match (error if "Confirm Password" doesn't match)
  - All error messages displayed in red below respective fields

**And** all forms are responsive:
  - Desktop: form width ~400px, centered
  - Mobile: form full-width with padding, stacked layout

**And** form elements have accessibility:
  - Proper label elements (or aria-label)
  - Tab navigation works correctly
  - Focus indicators visible

**And** when I submit the form
**Then** success message displays and redirect occurs (to dashboard or confirmation page)

---

### Story 1.10: Apply Branding, Colors & Typography Theme-Wide

**As a** developer,
**I want** to apply consistent branding, color scheme, and typography across all theme templates,
**So that** the entire store looks professional and on-brand.

**Acceptance Criteria:**

**Given** I have all theme pages built (stories 1.1-1.9)
**When** I apply branding across the theme:
**Then** all pages use consistent styling:
  - Primary color orange (#d18a1b) used for:
    - All primary CTA buttons
    - Hover states on links
    - Badge highlights and accents
    - Section heading underlines or background color
    - Form focus borders
  - Secondary colors:
    - Black (#000000 or #1a1a1a) for body text and borders
    - White (#ffffff) for backgrounds
    - Light beige (#f5f0e8 or #fffbf7) for section backgrounds and lighter areas
    - Gray (#666666 or #999999) for secondary text (dates, descriptions)
  - Typography consistent across all pages:
    - Sans-serif font family (e.g., Inter, Poppins, -apple-system, Ubuntu, or system stack)
    - Headings: Bold weight (700), uppercase or strong Case, strong hierarchy:
      - H1: 36-40px
      - H2: 28-32px
      - H3: 22-24px
    - Body text: Regular weight (400), 16-18px, line-height 1.6
    - Buttons: 14-16px, semi-bold (600-700)
  - Logo appears consistently:
    - Header: left-aligned, appropriate size
    - Footer: centered or left-aligned
    - Favicon in browser tab
  - Button styles uniform across all pages:
    - Background: #d18a1b
    - Text color: white
    - Border-radius: 4-6px
    - Padding: 12-16px (height)
    - Hover state: darker orange (#b8760f or similar)
    - Active state: even darker
    - Disabled state: gray (#cccccc)
  - Tailwind CSS utility classes used consistently:
    - Spacing: 8px increments (p-2, p-4, p-6, m-4, etc.)
    - Shadows: elevation levels for cards and modals
    - Border-radius: consistent rounding (rounded, rounded-lg, etc.)

**And** when I view any page on different devices
**Then** colors and fonts render consistently without distortion

**And** color contrast meets WCAG AA standards:
  - Normal text: 4.5:1 minimum contrast ratio
  - Large text (18pt+): 3:1 minimum
  - All interactive elements clearly distinguishable

**And** the theme config file (src/Config/theme.php or similar) allows customization:
  - Primary color swap (update single value, propagates everywhere)
  - Logo URL swap (uploads and points to new logo)
  - Font family override (system font, Google Font link, or custom)
  - Optional: secondary color customization

**And** Tailwind CSS is configured with:
  - Custom colors added to tailwind.config.js
  - Theme colors properly mapped
  - Purge/content configured to remove unused styles

---

### Story 1.11: Test & Finalize Theme (Responsiveness, Accessibility, Performance)

**As a** QA/developer,
**I want** to ensure the theme works across all devices, browsers, and accessibility standards,
**So that** all customers have an excellent shopping experience.

**Acceptance Criteria:**

**Responsive Design Testing:**

**Given** I test the theme on different devices/orientations
**When** I resize the browser or use mobile devices
**Then** all pages are fully responsive:
  - Desktop (1920px and above):
    - 4-column grids display clearly
    - Full layouts without horizontal scroll
    - All content readable at default zoom
  - Tablet (768-1024px):
    - 2-3 column grids with proper spacing
    - Touch-friendly button sizes (min 44-48px)
    - Navigation adapted (hamburger menu if needed)
  - Mobile (less than 768px):
    - 1-column stacked layouts throughout
    - Hamburger navigation menu functional
    - All text readable (min 16px)
    - Touch targets minimum 48px height
    - No horizontal scrolling
  - All text remains readable at all sizes
  - Font sizes scale appropriately per device
  - Images scale without distortion

**Keyboard Navigation Testing:**

**And** when I test keyboard navigation on all pages
**Then** I can:
  - Tab through all interactive elements (links, buttons, form fields)
  - Tab order is logical (left-to-right, top-to-bottom)
  - Focus indicators are visible (outline, highlight, underline)
  - Use Enter/Space to activate buttons and links
  - Use Enter to submit forms
  - Access all form fields using Tab alone (no mouse required)
  - Use Escape to close modals, menus, dropdowns
  - Arrow keys work in carousels/galleries and accordions

**Color Contrast & Accessibility:**

**And** when I test color contrast
**Then** all text meets WCAG AA standards:
  - Normal text (body): 4.5:1 contrast ratio minimum
  - Large text (18pt+, bold 14pt+): 3:1 contrast ratio minimum
  - All UI components with text have sufficient contrast
  - Information is not conveyed by color alone (icons or text also present)

**And** screen reader testing (NVDA, JAWS, or Apple VoiceOver):
  - Headings are marked with proper semantic tags (h1-h6)
  - Form labels associated with inputs (label htmlFor or aria-label)
  - Buttons have descriptive text (not just "click here")
  - Images have alt text
  - Decorative images have empty alt=""
  - ARIA roles and attributes used appropriately
  - List items marked with ul/ol/li elements

**Browser Compatibility Testing:**

**And** when I test on different browsers:
  - Chrome (latest): all functionality works, styles render correctly
  - Firefox (latest): all functionality works, styles render correctly
  - Safari (latest): all functionality works, styles render correctly
  - Edge (latest): all functionality works, styles render correctly
  - Mobile browsers (Chrome Android, Safari iOS): touch interactions work

**Performance Testing:**

**And** when I run Lighthouse or PageSpeed audits
**Then** performance scores are acceptable:
  - Largest Contentful Paint (LCP) less than 2.5 seconds
  - Cumulative Layout Shift (CLS) less than 0.1
  - First Input Delay (FID) less than 100ms
  - Lighthouse Performance score: 85+ (green)

**And** when I bundle assets with Vite
**Then**:
  - CSS and JS are minified and bundled
  - Unused Tailwind styles are purged (CSS size < 50KB)
  - JS is properly code-split (critical path loading)
  - Images are optimized (no large uncompressed images)
  - No console errors or warnings

**Theme Integration Testing:**

**And** when I set the theme as default in Bagisto admin
**Then**:
  - The shop homepage loads with AutoLeading theme applied
  - All Bagisto default routes work with theme (checkout, account, cart, etc.)
  - Admin panel remains unaffected (still uses default admin theme)
  - Theme assets (CSS, JS, images) load correctly without 404 errors
  - No layout shifts or missing styles

**Documentation:**

**And** I create comprehensive theme documentation including:
  - Installation instructions (how to activate theme in Bagisto)
  - File structure overview (directory layout explanation)
  - Color/logo customization guide (step-by-step for changing primary color or logo)
  - CSS customization (how to override Tailwind classes)
  - Blade section extension points (how plugins can extend theme)
  - Typography system explanation (font sizes, weights, line heights)
  - Component library reference (each reusable component's props and usage)
  - Troubleshooting guide (common issues and solutions)

**Final Verification:**

**And** when I verify the final theme delivery
**Then**:
  - All stories 1.1-1.10 are complete and functional
  - No remaining placeholders or TODOs in code
  - All assets (CSS, JS, images) are properly bundled
  - Theme is ready for production deployment
  - Theme documentation is complete and accessible

---


## Epic 2: Customer Registration and Admin Approval Onboarding

**Goal**: Enable new users to register, upload documents, and complete admin approval in a unified onboarding flow, ensuring only verified users can transact.

**Workflow Summary**:
1. User fills registration form (email, name, contact details) and uploads required documents (license, insurance, etc.)
2. User submits registration (can be partial, status "unverified" if docs missing)
3. User dashboard shows missing docs and allows uploads at any time
4. Admin dashboard shows all pending and incomplete registrations
5. Admin reviews, approves, or rejects users with notes
6. User receives status updates and can re-submit if rejected
7. **Gate**: User cannot purchase membership, top-up, or book cars until verification is approved

**Covers Requirements**: None explicitly (foundational gate for all subsequent wallet operations)

### Story 2.1: Combined Registration + Document Upload Form
...existing code...
### Story 2.2: Unverified Status & Missing Documents Dashboard
...existing code...
### Story 2.3: Admin Verification Dashboard
...existing code...
### Story 2.4: Admin Approval/Rejection Workflow
...existing code...
## Epic 2 Complete Summary
**Epic 2: Customer Registration and Admin Approval Onboarding** includes **4 stories**:
- ✅ Story 2.1: Combined Registration + Document Upload Form
- ✅ Story 2.2: Unverified Status & Missing Documents Dashboard
- ✅ Story 2.3: Admin Verification Dashboard
- ✅ Story 2.4: Admin Approval/Rejection Workflow

**FR Coverage for Epic 2**: None explicitly, but **foundational gate** for all subsequent wallet operations (Epic 3-7).

**Key Gate Rule Enforced**: All stories in Epic 3-7 will verify user status is "verified" before allowing wallet operations.

**Key Improvements**:
- Combined registration + document form (single submission)
- Partial submission allowed: shows "unverified" with missing documents list
- Customer can see exactly which documents are missing and add them anytime
- Automatic status upgrade when all documents are complete
- Admin dashboard shows both complete and incomplete submissions

**Goal**: Enable verified users to purchase membership plans and receive purchased credit safely and atomically at 1:1 ratio.

**Requirements Addressed**: FR1, also supports FR2 (dual-balance setup), FR12 (recurring use)

**Stories - To be defined in Step 3**:
- Story 2.1: Membership Plan Selection and Purchase
- Story 2.2: 1:1 Credit Issuance (Atomic Transaction)
- Story 2.3: Membership Expiry and Renewal Support

---

## Epic 3: Wallet Ledger Foundation

**Goal**: Implement a reliable dual-balance wallet system (purchased credit / bonus credit) using `bavix/laravel-wallet` as the underlying engine, with immutable ledger, expiry enforcement, and full balance reconstruction capability. The wallet is the **sole payment instrument** for all bookings — payment gateways (Stripe, Razorpay, etc.) are used exclusively to top up the wallet.

**Requirements Addressed**: FR2, FR5 (top-up side), FR9, FR10, FR13 (deduction mechanism), NFR2, NFR4, NFR8, NFR9

**Package Decision**: `bavix/laravel-wallet` v11.x (supports PHP 8.3+, Laravel 12, MIT license, 10M+ downloads, battle-tested race condition handling via atomic DB transactions).

**Payment Flow Architecture**:
```
Customer → Top Up Wallet (via Stripe / Razorpay / any gateway)
                ↓
         Wallet Balance increases (purchased credit)
                ↓
Customer → Checkout → "Pay with Wallet" (ONLY option)
         Balance pre-checked: must be ≥ order total
                ↓
         Order placed → status: PENDING  ← no deduction here
                ↓
         Admin reviews booking in admin panel
                ↓
         Admin creates Invoice / confirms order
                ↓
         Wallet deducted (bonus-first → purchased)
         Membership level auto-updated
         Order status → PROCESSING
```
Payment gateways are **never** invoked at checkout. They are top-up instruments only.
Wallet deduction is **deferred to invoice creation** — this mirrors COD behavior and gives admin control over when charges are applied.

**Implementation Strategy**:
- Add `HasWallets` trait to the `Customer` model via Bagisto's Proxy/Concord binding (never edit core models directly)
- Create two named wallets per customer: `purchased` and `bonus`
- `bavix/laravel-wallet` owns the `wallets` and `transactions` tables — no conflict with Bagisto's `order_transactions`
- All balance mutations go through `depositFloat()` / `withdrawFloat()` / `safeWithdrawFloat()` — never raw DB writes
- `WalletPayment` is registered as the **only active** Bagisto payment method; all other payment methods (Stripe, Razorpay, CashOnDelivery, etc.) are disabled at checkout
- Credit expiry implemented via `bavix`'s `ExpirationInterface` on the `transactions` table

**Stories**:
- Story 3.1: Install and Configure bavix/laravel-wallet
- Story 3.2: Dual-Balance Wallet Structure (Purchased/Bonus) on Customer Model
- Story 3.3: Customer Wallet UI (Balance, Top-Up, Transaction History)
- Story 3.4: Credit Expiry Configuration and Enforcement
- Story 3.5: Admin Wallet View and Manual Adjustment

---

### Story 3.1: Install and Configure bavix/laravel-wallet

**As a** developer,
**I want** to install and configure `bavix/laravel-wallet` within the Bagisto package system,
**So that** the wallet engine is available as a foundation for all credit operations.

**Acceptance Criteria:**

**Given** I run the package installation
**When** `composer require bavix/laravel-wallet` completes
**Then**:
  - Package is listed in `composer.json` as a dependency of the new `packages/Webkul/Wallet/` package
  - `php artisan vendor:publish --tag=laravel-wallet-migrations` has been run and migrations are present
  - `php artisan migrate` creates the following tables without errors:
    - `wallets` (id, holder_type, holder_id, name, slug, uuid, description, meta, balance, decimal_places, created_at, updated_at, deleted_at)
    - `transactions` (id, payable_type, payable_id, wallet_id, type, amount, confirmed, meta, uuid, created_at, updated_at)
    - `transfers` (id, deposit_id, withdraw_id, status, status_last, from_type, from_id, to_type, to_id, discount, fee, uuid, created_at, updated_at)

**And** a new Bagisto package `packages/Webkul/Wallet/` is scaffolded with:
  - `src/Providers/WalletServiceProvider.php` (registered in `bootstrap/providers.php`)
  - `src/Providers/ModuleServiceProvider.php` (registered in `config/concord.php`)
  - `src/Config/payment-methods.php` (registers `WalletPayment` as a Bagisto payment method)
  - `src/Payment/WalletPayment.php` (extends Bagisto's `Payment` abstract class)
  - PSR-4 namespace `Webkul\Wallet` added to root `composer.json`

**And** `WalletPayment` is the **only** active payment method at checkout:
  - All other payment methods (Stripe, Razorpay, CashOnDelivery, MoneyTransfer) have `active = false` in their config or are overridden via `WalletServiceProvider::boot()` to force-disable them at checkout
  - `WalletPayment::isAvailable()` returns `true` only for verified, authenticated customers
  - The checkout payment step displays only "Pay with Wallet" with no other options

**And** `bavix/laravel-wallet` config is published and the `decimal_places` is aligned with Bagisto's currency decimal setting (default: 2)

**And** when I run `composer dump-autoload && php artisan optimize:clear`
**Then** no errors are thrown and the Wallet package is recognized by Laravel

---

### Story 3.2: Dual-Balance Wallet Structure (Purchased/Bonus) on Customer Model

**As a** developer,
**I want** the `Customer` model to support two named wallets (purchased and bonus),
**So that** the system can track and operate on each credit type independently with deterministic deduction order.

**Acceptance Criteria:**

**Given** the Wallet package is installed (Story 3.1 complete)
**When** I extend the Customer model
**Then**:
  - A `WalletCustomer` class in `packages/Webkul/Wallet/src/Models/WalletCustomer.php` extends `Webkul\Customer\Models\Customer`
  - `WalletCustomer` uses `Bavix\Wallet\Traits\HasWallets`
  - `WalletServiceProvider` binds `Webkul\Customer\Contracts\Customer` to `WalletCustomer` via Concord (no core model editing)

**And** two named wallets are auto-created for each customer on first use (lazy initialization):
  - `slug: 'purchased'` — holds credits from membership top-ups and direct top-ups
  - `slug: 'bonus'` — holds credits from admin grants, referral bonuses, promotions

**And** a `WalletService` class in `packages/Webkul/Wallet/src/Services/WalletService.php` provides:
  ```php
  getOrCreatePurchasedWallet(Customer $customer): Wallet
  getOrCreateBonusWallet(Customer $customer): Wallet
  getPurchasedBalance(Customer $customer): string   // formatted float
  getBonusBalance(Customer $customer): string
  getTotalBalance(Customer $customer): string        // purchased + bonus
  canAfford(Customer $customer, float $amount): bool // checks total balance >= amount
  ```

**And** all wallet creation calls include meta:
  ```php
  ['meta' => ['created_by' => 'system', 'purpose' => 'purchased|bonus']]
  ```

**And** `NFR2` is enforced — `safeWithdrawFloat()` is used for all deductions (returns `false` on insufficient funds instead of throwing), never allowing negative balance states

**And** a Pest feature test covers:
  - New customer has no wallets initially
  - `getOrCreatePurchasedWallet()` creates wallet on first call, returns same wallet on subsequent calls
  - `canAfford()` returns `false` when total balance < requested amount
  - `canAfford()` returns `true` when total balance >= requested amount

---

### Story 3.3: Customer Wallet UI (Balance, Top-Up, Transaction History)

**As a** verified customer,
**I want** to view my wallet balance, top up via a payment gateway, and see my transaction history,
**So that** I have sufficient credit loaded before making a car rental booking — since wallet is the only way to pay.

**Context**: Top-up is the **required pre-step** before any booking. Customers cannot pay at checkout with a card — they must fund their wallet first. This story owns the entire gateway integration path.

**Acceptance Criteria:**

**Given** I am logged in as a verified customer
**When** I navigate to `/customer/wallet`
**Then** I see a wallet dashboard with:
  - Purchased Credit balance (formatted to 2 decimal places with currency symbol)
  - Bonus Credit balance (formatted to 2 decimal places with currency symbol)
  - Total Balance (sum of both, prominently displayed)
  - "Top Up Wallet" button (orange, primary CTA — this is the entry point to fund the account)
  - Transaction history table (paginated, 15 rows per page) with columns:
    - Date/Time, Type (Top-Up / Booking Deduction / Bonus Grant / Refund / Expiry / Admin Adjustment), Amount (+/-), Balance After, Reference

**And** the Critical Gate Rule is enforced:
  - Unverified customers who navigate to `/customer/wallet` see a warning: "Your account is pending verification. Wallet features are available once your account is approved."
  - The "Top Up Wallet" button is disabled/hidden for unverified customers

**Top-Up Flow (Gateway Integration Entry Point):**

**Given** I click "Top Up Wallet"
**When** the top-up form appears
**Then** I see:
  - Amount input (numeric, min/max configurable in Admin > Wallet Settings)
  - Payment gateway selector showing all active top-up gateways (e.g., Stripe, Razorpay)
  - Summary line: "You are topping up [amount] to your Purchased Credit wallet"
  - "Proceed to Payment" button (orange)

**And** when I submit the top-up form
**Then** I am redirected to the selected gateway's hosted checkout (Stripe Checkout Session / Razorpay order page)

**And** when the gateway returns a success callback to `/wallet/topup/callback/{gateway}`
**Then**:
  - Server validates the payment server-side (never trust client-side callback alone)
  - `$customer->getOrCreatePurchasedWallet()->depositFloat($amount, ['meta' => ['type' => 'topup', 'gateway' => $gateway, 'transaction_id' => $gatewayTxId]])`
  - Customer is redirected to `/customer/wallet` with success flash: "Your wallet has been topped up with [amount]. You can now proceed to book a car."
  - New transaction appears at top of history with type "Top-Up"

**And** when the gateway returns a failure or cancel callback
**Then**:
  - No balance change occurs
  - Customer is redirected to `/customer/wallet` with an error/info message
  - Failed top-up attempt is logged (not in wallet ledger, in a separate `wallet_topup_attempts` log)

**Redirect-to-Top-Up Trigger (from checkout):**

**And** when a customer arrives at `/customer/wallet` with query param `?reason=insufficient_balance&required=[amount]`
**Then** the page displays a contextual banner: "Your wallet balance is insufficient to complete your booking. You need at least [amount]. Please top up to continue."
**And** the top-up form is pre-filled with the required shortfall amount

**And** the wallet page is accessible only to authenticated customers (redirect to login if unauthenticated)

---

### Story 3.4: Credit Expiry Configuration and Enforcement

**As a** platform administrator,
**I want** to configure expiry policies for bonus credits,
**So that** promotional credits are time-limited while purchased credits never expire.

**Acceptance Criteria:**

**Given** the dual-wallet structure is in place (Story 3.2 complete)
**When** the expiry system is configured
**Then**:
  - **Purchased credits** (`purchased` wallet): **never expire** — no expiry meta is set on deposit transactions
  - **Bonus credits** (`bonus` wallet): expiry is configurable per-grant via `meta`:
    ```php
    $bonusWallet->depositFloat($amount, [
        'meta' => [
            'type'       => 'bonus_grant',
            'expires_at' => now()->addDays($expiryDays)->toISOString(),
            'reason'     => $reason,
        ]
    ]);
    ```

**And** a scheduled command `wallet:expire-bonus-credits` runs daily via Laravel scheduler:
  - Finds all `transactions` on `bonus` wallets where `meta->expires_at` is in the past and `meta->expired` is not `true`
  - For each expired transaction, creates a compensating deduction:
    ```php
    $bonusWallet->withdrawFloat($expiredAmount, ['meta' => ['type' => 'expiry', 'source_transaction_id' => $txId]]);
    ```
  - Marks original transaction `meta->expired = true` (via JSON update, NOT deleting the record — NFR9)
  - Deduction only occurs up to available balance (uses `safeWithdrawFloat()`)

**And** an admin setting exists at `Admin > Wallet Settings`:
  - "Default Bonus Credit Expiry (days)" — integer, 0 = never expire, default: 90
  - "Minimum Top-Up Amount" — decimal
  - "Maximum Top-Up Amount" — decimal

**And** expiry deductions appear in the customer transaction history with type "Bonus Expiry"

**And** a Pest feature test covers:
  - Bonus credit with past `expires_at` is deducted on command run
  - Purchased credit is never touched by the expiry command
  - Partial expiry (available balance less than expired amount) deducts only available balance
  - Already-expired transactions are not processed twice

---

### Story 3.5: Admin Wallet View and Manual Adjustment

**As a** platform administrator,
**I want** to view any customer's wallet balances and make authorized credit adjustments,
**So that** I can resolve disputes, grant promotions, and correct errors.

**Acceptance Criteria:**

**Given** I am logged in as an admin with `wallet.adjust` ACL permission
**When** I navigate to `Admin > Customers > [Customer] > Wallet`
**Then** I see:
  - Customer name, email, verification status
  - Purchased Credit balance
  - Bonus Credit balance
  - Total Balance
  - Full transaction ledger for this customer (all wallet types, paginated, sortable by date)
  - "Add Credit" button (opens adjustment form)
  - "Deduct Credit" button (opens adjustment form)

**And** the adjustment form includes:
  - Wallet selector: "Purchased" or "Bonus"
  - Amount (decimal input, required, positive value)
  - Reason (text input, required, min 10 characters)
  - For bonus credit: optional "Expires in (days)" field
  - "Confirm" button (orange)

**And** when I submit an "Add Credit" adjustment
**Then**:
  - `$wallet->depositFloat($amount, ['meta' => ['type' => 'admin_grant', 'admin_id' => auth()->id(), 'reason' => $reason]])`
  - A success message is shown: "Credit adjustment recorded."
  - The transaction appears in the ledger with type "Admin Grant" and the admin's name as actor

**And** when I submit a "Deduct Credit" adjustment
**Then**:
  - `$wallet->safeWithdrawFloat($amount, ['meta' => ['type' => 'admin_deduct', 'admin_id' => auth()->id(), 'reason' => $reason]])`
  - If balance is insufficient, an error is shown: "Insufficient balance. Available: [amount]."
  - On success, the transaction appears with type "Admin Deduction" and the admin's name

**And** NFR5 is enforced — the adjustment endpoints are protected by:
  - Bagisto ACL check: `bouncer()->can('wallet.adjust')` (or equivalent gate)
  - 401 response if unauthorized admin attempts the action

**And** all admin adjustments satisfy NFR4 — every transaction record includes:
  - `type` (admin_grant / admin_deduct)
  - `admin_id` of the acting admin
  - `reason` text
  - Timestamp (via `created_at` on `transactions` table)

**And** a Pest feature test covers:
  - Admin can grant purchased credit and it appears in ledger
  - Admin can grant bonus credit with expiry and it appears in ledger
  - Admin deduction fails gracefully when balance is insufficient
  - Non-admin user cannot access the adjustment endpoint (401 response)

---

## Epic 4: Booking Payment with Wallet Credit

**Goal**: Enable car rental bookings to be paid exclusively via wallet credit, with a balance pre-check gate at booking placement, COD-style deferred deduction at invoice confirmation, and deterministic bonus-first deduction order.

**Requirements Addressed**: FR3, FR4, FR5 (block side), FR13, NFR1, NFR3, NFR7

**Payment Model**:
- Booking placement: `WalletPayment` behaves like COD — no redirect, no deduction, order placed as `pending`
- Balance is pre-checked at placement to prevent under-funded bookings reaching admin
- Deduction is triggered by the `sales.invoice.save.after` event when admin creates the invoice
- No payment gateway is invoked at any point in the checkout or confirmation flow

**Stories**:
- Story 4.1: Register WalletPayment as Sole Checkout Payment Method
- Story 4.2: Available Credit Validation Gate at Checkout
- Story 4.3: Insufficient Balance Block and Redirect to Wallet
- Story 4.4: Atomic Booking + Wallet Deduction (Bonus-First)

---

### Story 4.1: Register WalletPayment as Sole Checkout Payment Method

**As a** developer,
**I want** the checkout payment step to offer only "Pay with Wallet" and hide all other payment methods,
**So that** all bookings flow through wallet deduction with no gateway invoked at checkout.

**Acceptance Criteria:**

**Given** I am at the checkout payment selection step
**When** the payment methods list is rendered
**Then**:
  - Only `WalletPayment` (code: `wallet`) appears in the list
  - All other payment methods (Stripe, Razorpay, CashOnDelivery, MoneyTransfer) are absent
  - The payment step displays the customer's current wallet balance beneath the "Pay with Wallet" option: "Available balance: [total balance]"

**And** `WalletPayment` is force-selected automatically (no manual selection needed if it is the only method)

**And** all other payment method packages remain installed but are disabled for checkout via config override in `WalletServiceProvider`:
  ```php
  // Overrides active flag at runtime — does not delete other package configs
  Config::set('payment_methods.stripe.active', false);
  Config::set('payment_methods.razorpay.active', false);
  // etc.
  ```

**And** `WalletPayment::getRedirectUrl()` returns `null` (no redirect — payment is processed inline)

**And** a Pest test confirms only `wallet` appears in `Payment::getSupportedPaymentMethods()`

---

### Story 4.2: Available Credit Validation Gate at Checkout

**As a** customer,
**I want** the system to check my wallet balance before I can place a booking,
**So that** I am only shown the "Confirm Order" button when I have sufficient credit.

**Acceptance Criteria:**

**Given** I am at the checkout confirmation step
**When** the page loads
**Then**:
  - `WalletService::canAfford($customer, $cart->grand_total)` is evaluated
  - If `true`: "Confirm Order" button is enabled; balance summary shows "You will be charged [amount] from your wallet"
  - If `false`: "Confirm Order" button is replaced with "Insufficient Balance — Top Up Wallet" (orange, links to `/customer/wallet?reason=insufficient_balance&required=[shortfall]`)

**And** the server-side order placement endpoint re-validates balance even if client-side check passed (NFR2 double-check)

**And** a Pest test covers:
  - Customer with exact balance can proceed
  - Customer with zero balance cannot proceed
  - Customer with partial balance cannot proceed

---

### Story 4.3: Insufficient Balance Block and Redirect to Wallet

**As a** customer,
**I want** to be clearly told my balance is insufficient and taken directly to the top-up page,
**So that** I know exactly how much to load and can immediately resolve it.

**Acceptance Criteria:**

**Given** the server receives an order placement request
**When** `WalletService::canAfford($customer, $amount)` returns `false` at the server level
**Then**:
  - Order is NOT created
  - HTTP response returns validation error with message: "Insufficient wallet balance. Required: [amount], Available: [total balance]. Please top up your wallet."
  - Frontend redirects customer to `/customer/wallet?reason=insufficient_balance&required=[shortfall]`

**And** on the wallet page, the contextual banner (Story 3.3) is shown with the shortfall pre-filled in the top-up form

**And** after a successful top-up the customer can navigate back to checkout and retry

**And** no partial order state is left behind — the failed attempt produces no order record, no invoice, no ledger entry

**And** NFR7: this scenario is covered by a dedicated Pest acceptance test confirming 0 unintended side effects

---

### Story 4.4: Wallet Deduction on Invoice Confirmation (Bonus-First)

**As an** admin,
**I want** the customer's wallet to be debited automatically when I create an invoice for a booking,
**So that** credit is only charged for confirmed bookings and I retain control over when charges are applied.

**Context**: Booking placement creates a `pending` order with no charge (COD-style). This story handles the deduction that fires when admin creates the invoice via `Admin > Orders > [Order] > Create Invoice`.

**Acceptance Criteria:**

**Order Placement (COD-style — no deduction):**

**Given** a customer places a booking at checkout with sufficient wallet balance
**When** the order is submitted
**Then**:
  - Order is created with `status = pending`, `payment.method = 'wallet'`
  - **No wallet deduction occurs**
  - `WalletPayment::getRedirectUrl()` returns `null` (no redirect, no gateway call)
  - The `GenerateInvoice` listener is **disabled** for `wallet` payment method (deduction must be admin-triggered, not automatic)

**Invoice Creation (deduction trigger):**

**Given** admin opens an order and clicks "Create Invoice"
**When** the `sales.invoice.save.after` event fires
**Then** a `WalletInvoiceListener` executes the following inside a single DB transaction (NFR1):
  1. Re-validate balance: `WalletService::canAfford($customer, $invoice->base_grand_total)` — abort if insufficient (edge case: balance dropped due to another invoice)
  2. Deduct bonus credit first (up to available bonus balance):
     ```php
     $bonusWallet->safeWithdrawFloat(
         min($bonusBalance, $invoiceTotal),
         ['meta' => ['type' => 'booking_deduction', 'order_id' => $orderId, 'invoice_id' => $invoiceId, 'priority' => 'bonus']]
     )
     ```
  3. Deduct remaining from purchased credit:
     ```php
     $purchasedWallet->withdrawFloat(
         $remainder,
         ['meta' => ['type' => 'booking_deduction', 'order_id' => $orderId, 'invoice_id' => $invoiceId, 'priority' => 'purchased']]
     )
     ```
  4. Create `OrderTransaction` record: `transaction_id = wallet_tx_{uuid}`, `type = wallet`, `amount = $invoiceTotal`
  5. Update order status to `processing`

**And** if wallet deduction fails (e.g. edge-case insufficient balance), the invoice creation is aborted and admin sees an error: "Insufficient wallet balance. Cannot create invoice."

**And** if any step in the DB transaction fails, the entire transaction rolls back — no partial deduction, no orphaned invoice

**And** the customer's transaction history (Story 3.3) shows the new entry with type "Booking Deduction", order reference, and updated balance

**And** a Pest feature test covers:
  - Bonus-first deduction: bonus exhausted before purchased is touched
  - Pure purchased deduction: when bonus balance is zero
  - Mixed deduction: partial bonus + remainder from purchased
  - Edge case: balance dropped between booking and invoice — invoice creation blocked with error
  - Transaction rollback: if order update fails after deduction, balance is restored
  - Resulting `order_transactions` record has correct amount, wallet reference, and invoice ID
  - `WalletInvoiceListener` is idempotent: second invoice on same order does not double-deduct

---

## Epic 5: Refunds, Reversals, and Policy Enforcement

**Goal**: Enable post-booking financial correctness with refundability rules, destination routing, and reversal consistency.

**Requirements Addressed**: FR6, FR7, FR11, NFR3, NFR8

**Stories - To be defined in Step 3**:
- Story 5.1: Per-Product Refundability Configuration
- Story 5.2: Refund Destination Routing (Account vs Store Credit)
- Story 5.3: Cancellation and Reversal Workflow
- Story 5.4: Refund Ledger Recording

---

## Epic 6: Admin Governance and Operational Control

**Goal**: Enable secure admin controls for adjustments, approvals, and full audit/compliance visibility.

**Requirements Addressed**: FR8, NFR4, NFR5

**Stories - To be defined in Step 3**:
- Story 6.1: Admin Credit Adjustment Interface
- Story 6.2: User Verification Approval Workflow (supports Epic 1)
- Story 6.3: Audit Log and Compliance Reporting
- Story 6.4: Role-Based Access Control for Admin Functions

---

## Critical Gate Rules

**Gate 1 — Verification (enforced in ALL stories covering wallet operations, Epic 2-6)**:
```
Given a user is not verified or registration/docs are not approved
When the user attempts to [purchase membership / top-up wallet / complete booking payment]
Then the action is blocked
And an error message directs user to complete registration and await admin approval
```

**Gate 2 — Wallet-Only Checkout (enforced in ALL checkout and payment stories, Epic 4)**:
```
Given a customer is at checkout
When the payment step is reached
Then only "Pay with Wallet" is available
And no payment gateway (Stripe, Razorpay, etc.) is presented or invoked
And payment gateways are accessible only via the Wallet Top-Up flow (/customer/wallet)
```

**Gate 3 — Sufficient Balance (enforced in Epic 4, Stories 4.2 and 4.3)**:
```
Given a customer attempts to place a booking
When their total wallet balance (purchased + bonus) is less than the order total
Then the order is blocked server-side
And the customer is redirected to /customer/wallet with the shortfall amount pre-filled
```

---

## Epic 7: Frontend Homepage, Product, Blog, and Theme Updates

**Goal**: Modernize and customize the customer-facing experience by updating the homepage, car listing (product) page, blog pages, and overall shop theme.

**Stories:**
- Story 7.1: Redesign and implement the homepage with new layout, banners, and featured sections
- Story 7.2: Update the product (car list) page to improve car browsing, filtering, and visual presentation
- Story 7.3: Create or update blog pages for content marketing and SEO
- Story 7.4: Customize the shop theme (colors, typography, layout) to match brand identity and improve UX

---

## Epic 8: Customer Membership Level Management

**Goal**: Automatically classify customers into membership tiers (Gold, Platinum, Diamond) based on cumulative lifetime confirmed spend, and support admin-only assignment of the Jadeite (invitation-only) tier — implemented as Bagisto Customer Groups.

**Requirements Addressed**: FR14, FR15, NFR10

**Membership Tiers**:
| Level | Threshold | Assignment |
|---|---|---|
| Default | $0 – $2,999.99 | Auto (starting group) |
| Gold Member | $3,000+ | Auto on invoice confirmation |
| Platinum Member | $30,000+ | Auto on invoice confirmation |
| Diamond Member | $60,000+ | Auto on invoice confirmation |
| Jadeite Member | Invitation Only | Admin manual only — never auto |

**Implementation Notes**:
- Tiers map to Bagisto's built-in `customer_groups` table
- Cumulative spend = sum of `base_grand_total` across all `invoices` with `state = paid` for the customer
- Membership update is triggered by the same `sales.invoice.save.after` event as wallet deduction (Story 4.4), fired after deduction succeeds
- Jadeite customers are never auto-upgraded or auto-downgraded regardless of spend

**Stories**:
- Story 8.1: Seed Membership Customer Groups
- Story 8.2: Auto-Assign Membership Level on Invoice Confirmation
- Story 8.3: Admin Manual Jadeite Assignment
- Story 8.4: Membership Level Display (Admin and Customer Facing)

---

### Story 8.1: Seed Membership Customer Groups

**As a** developer,
**I want** the five membership customer groups to exist in the database from initial setup,
**So that** auto-assignment and admin assignment have valid groups to reference.

**Acceptance Criteria:**

**Given** I run `php artisan db:seed` (or the Wallet package seeder)
**When** the `MembershipGroupSeeder` runs
**Then** the following Bagisto customer groups exist in `customer_groups`:
  | code | name | is_default |
  |---|---|---|
  | `default` | Default | true |
  | `gold_member` | Gold Member | false |
  | `platinum_member` | Platinum Member | false |
  | `diamond_member` | Diamond Member | false |
  | `jadeite_member` | Jadeite Member | false |

**And** the seeder is idempotent — running it twice does not create duplicate groups

**And** the seeder is registered in `WalletServiceProvider` so it runs as part of standard setup

---

### Story 8.2: Auto-Assign Membership Level on Invoice Confirmation

**As a** platform,
**I want** to automatically update a customer's membership group after each invoice is confirmed,
**So that** their tier reflects their lifetime spend without any manual admin action.

**Context**: Triggered by the same `sales.invoice.save.after` event as wallet deduction (Story 4.4). The `MembershipLevelListener` runs after `WalletInvoiceListener` succeeds.

**Acceptance Criteria:**

**Given** admin creates an invoice for a customer's booking
**When** `sales.invoice.save.after` fires and wallet deduction completes
**Then** `MembershipLevelListener` calculates the customer's new cumulative spend:
  ```php
  $lifetime = Invoice::where('order.customer_id', $customerId)
                     ->where('state', 'paid')
                     ->sum('base_grand_total');
  ```

**And** the correct group is resolved:
  ```
  $lifetime >= 60000  → diamond_member
  $lifetime >= 30000  → platinum_member
  $lifetime >= 3000   → gold_member
  otherwise           → default
  ```

**And** if the customer is currently `jadeite_member`, **no change is made** (NFR10 — Jadeite is never auto-downgraded)

**And** if the resolved group is higher than current group, `$customer->customer_group_id` is updated

**And** if the resolved group is the same as current group, no DB write occurs (idempotent — NFR10)

**And** downgrading is not supported (a customer who reaches Gold at $3,000 is never dropped back to Default even if invoices are voided — spend only goes forward)

**And** a Pest feature test covers:
  - New customer starts at Default
  - Customer reaches $3,000 lifetime spend → upgraded to Gold
  - Customer reaches $30,000 → upgraded to Platinum
  - Customer reaches $60,000 → upgraded to Diamond
  - Jadeite customer invoiced → remains Jadeite (no downgrade)
  - Listener is idempotent: running twice on same invoice does not change group a second time

---

### Story 8.3: Admin Manual Jadeite Assignment

**As an** admin,
**I want** to manually assign a customer to the Jadeite membership level from the admin panel,
**So that** I can extend invitation-only membership to selected high-value customers.

**Acceptance Criteria:**

**Given** I am logged in as an admin with `customers.edit` permission
**When** I navigate to `Admin > Customers > [Customer] > Edit`
**Then** I see a "Membership Level" field (read-only display of current group) with an "Assign Jadeite" button visible only if the customer is not already Jadeite

**And** when I click "Assign Jadeite"
**Then** a confirmation dialog appears: "Assign [Customer Name] to Jadeite Member (Invitation Only)? This cannot be auto-reversed."

**And** when I confirm
**Then**:
  - `$customer->customer_group_id` is set to the `jadeite_member` group ID
  - An admin activity log entry is created: `"Admin [admin_name] manually assigned Jadeite membership to [customer_name] on [date]"`
  - Success message: "Customer assigned to Jadeite Member."

**And** the `MembershipLevelListener` (Story 8.2) will never override this assignment regardless of spend (NFR10)

**And** a Pest feature test confirms Jadeite cannot be auto-overwritten by the invoice listener

---

### Story 8.4: Membership Level Display (Admin and Customer Facing)

**As a** customer and admin,
**I want** to see the current membership level clearly displayed,
**So that** the customer knows their tier and the admin can see it at a glance.

**Acceptance Criteria:**

**Customer-facing (Shop):**

**Given** I am logged in as a customer
**When** I visit `/customer/account` or `/customer/wallet`
**Then** I see my membership level displayed as a badge:
  - Default: no badge (or "Standard" in gray)
  - Gold Member: gold-colored badge
  - Platinum Member: silver/platinum-colored badge
  - Diamond Member: blue/diamond-colored badge
  - Jadeite Member: green/jade-colored badge with "★ Invitation Only" label

**Admin-facing:**

**Given** I am in the admin panel viewing a customer record
**When** I open `Admin > Customers > [Customer]`
**Then** the customer's membership level is displayed prominently next to their name

**And** the customer list grid (`Admin > Customers`) has a "Membership Level" column that shows the current tier and is filterable/sortable

**And** a Pest test confirms correct badge/label is returned for each group code
