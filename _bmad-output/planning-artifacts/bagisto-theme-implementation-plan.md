# Bagisto Theme Implementation Plan: Auto Leading Style

## Objective
Replicate the look and feel of https://autoleading.net/ as a custom Bagisto theme, matching layout, colors, typography, and branding. No data/content migration is required.

---

## 1. Theme Package Structure
- Create a new theme package: `packages/Webkul/AutoLeadingTheme/`
- Follow Bagisto theme conventions:
  - `src/Resources/views/` (Blade templates)
  - `src/Resources/assets/` (CSS, JS, images)
  - `src/Config/` (theme config)
  - `vite.config.js` for asset bundling

## 2. Key Pages & Layouts to Replicate
- Homepage (hero, search, featured cars, brands, services, process, blog/FAQ/contact, footer)
- Car list (with filters, cards, pagination)
- Car detail (gallery, specs, booking CTA)
- Brand filter page (tabbed or filtered car list)
- Blog/news list and detail
- FAQ page
- Contact page
- Registration/login
- Rental process & service commitment
- Header (navigation, logo, language, login/register)
- Footer (quick links, contact, hours, social)

## 3. UI Components
- Hero/banner with background image and overlay text
- Search bar with dropdowns and icons
- Car cards (image, name, price, tags, CTA)
- Brand tabs/buttons
- Service icons and commitment list
- Step-by-step process section
- Blog/FAQ/contact preview cards
- Responsive navigation bar
- Footer with multi-column layout

## 4. Color & Typography
- Primary color: Orange (#d18a1b)
- Secondary: Black, white, light beige backgrounds
- Font: Modern sans-serif (match closest Google Fonts or system font)
- Button/CTA: Orange background, white text, rounded corners
- Headings: Bold, uppercase or strong hierarchy

## 5. Branding & Assets
- Use provided logo (SVG/PNG)
- Car images: Use demo images or placeholders
- Icons: Use similar or matching icon set (FontAwesome, Heroicons, etc.)
- Social icons: Footer and contact

## 6. Responsiveness & Accessibility
- Mobile-first, fully responsive layouts
- Accessible color contrast
- Keyboard navigation for menus and forms

## 7. Asset Bundling
- Use Vite for CSS/JS bundling
- Tailwind CSS for utility classes and rapid styling
- Organize assets in `assets/` folder

## 8. Theme Registration
- Register theme in Bagisto config
- Set as default theme for shop

## 9. Customization Points
- Allow easy color/logo swap via config
- Blade sections for extensibility

---

## References
- [Bagisto Theme Development Guide](https://devdocs.bagisto.com/theme-development/getting-started.html)
- [Current Site](https://autoleading.net/)

---

## Next Steps
1. Scaffold theme package structure
2. Build homepage layout and shared components
3. Implement all referenced pages and sections
4. Apply color, font, and branding
5. Test responsiveness and accessibility
6. Register and activate theme in Bagisto
