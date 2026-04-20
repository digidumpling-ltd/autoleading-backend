# AutoLeading Theme Quick Start

## Working Rule

Implement storefront changes in this package only:

- Views: `packages/Webkul/AutoLeadingTheme/src/Resources/views`
- Assets: `packages/Webkul/AutoLeadingTheme/src/Resources/assets`

Do not edit core Shop package views directly.

## Publish Theme Views

```bash
php artisan vendor:publish --provider="Webkul\\AutoLeadingTheme\\Providers\\AutoLeadingThemeServiceProvider" --tag=auto-leading-theme-views
```

## Build Assets

```bash
cd packages/Webkul/AutoLeadingTheme
npm install
npm run dev
```

For production builds:

```bash
cd packages/Webkul/AutoLeadingTheme
npm run build
```

## Useful Paths

- Published views: `resources/themes/auto-leading-theme/views`
- Built assets: `public/themes/shop/auto-leading-theme/build`