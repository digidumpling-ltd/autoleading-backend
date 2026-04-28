<?php

use Illuminate\Support\Str;
use Spatie\ResponseCache\Facades\ResponseCache;

use function Pest\Laravel\get;

/**
 * Switch the active channel theme, run a callback, then restore the original.
 */
function withTheme(string $theme, callable $callback): void
{
    $channel       = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;

    $packageHomeView  = base_path('packages/Webkul/AutoLeadingTheme/src/Resources/views/home/index.blade.php');
    $publishedHomeView = resource_path('themes/auto-leading-theme/views/home/index.blade.php');

    if (! is_dir(dirname($publishedHomeView))) {
        mkdir(dirname($publishedHomeView), 0755, true);
    }

    copy($packageHomeView, $publishedHomeView);

    $channel->theme = $theme;
    $channel->save();
    core()->setCurrentChannel($channel->fresh());

    ResponseCache::clear();

    try {
        $callback();
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
    }
}

it('contains the required AutoLeading theme scaffold files', function () {
    $requiredFiles = [
        base_path('packages/Webkul/AutoLeadingTheme/src/Providers/AutoLeadingThemeServiceProvider.php'),
        base_path('packages/Webkul/AutoLeadingTheme/src/Resources/views/home/index.blade.php'),
        base_path('packages/Webkul/AutoLeadingTheme/src/Resources/assets/css/app.css'),
        base_path('packages/Webkul/AutoLeadingTheme/src/Resources/assets/js/app.js'),
        base_path('packages/Webkul/AutoLeadingTheme/package.json'),
        base_path('packages/Webkul/AutoLeadingTheme/vite.config.js'),
        base_path('packages/Webkul/AutoLeadingTheme/tailwind.config.js'),
        base_path('packages/Webkul/AutoLeadingTheme/postcss.config.js'),
    ];

    foreach ($requiredFiles as $requiredFile) {
        expect(file_exists($requiredFile))->toBeTrue("Missing scaffold file: {$requiredFile}");
    }
});

it('registers AutoLeading theme and vite configuration entries', function () {
    expect(config('themes.shop.auto-leading-theme.name'))->toBe('Auto Leading');
    expect(config('themes.shop.auto-leading-theme.assets_path'))->toBe('public/themes/shop/auto-leading-theme');
    expect(config('themes.shop.auto-leading-theme.views_path'))->toBe('resources/themes/auto-leading-theme/views');

    expect(config('themes.shop.auto-leading-theme.vite.hot_file'))->toBe('auto-leading-theme-vite.hot');
    expect(config('themes.shop.auto-leading-theme.vite.build_directory'))->toBe('themes/shop/auto-leading-theme/build');
    expect(config('themes.shop.auto-leading-theme.vite.package_assets_directory'))->toBe('src/Resources/assets');

    expect(config('bagisto-vite.viters.auto-leading-theme.hot_file'))->toBe('auto-leading-theme-vite.hot');
    expect(config('bagisto-vite.viters.auto-leading-theme.build_directory'))->toBe('themes/shop/auto-leading-theme/build');
    expect(config('bagisto-vite.viters.auto-leading-theme.package_assets_directory'))->toBe('src/Resources/assets');
});

it('has product-card blade component view file', function () {
    expect(file_exists(
        base_path('packages/Webkul/AutoLeadingTheme/src/Resources/views/components/product-card.blade.php')
    ))->toBeTrue();
});

it('has footer-column blade component view file', function () {
    expect(file_exists(
        base_path('packages/Webkul/AutoLeadingTheme/src/Resources/views/components/footer-column.blade.php')
    ))->toBeTrue();
});

it('renders footer section when AutoLeading theme is active', function () {
    withTheme('auto-leading-theme', function () {
        $response = get('/');
        $response->assertOk();
        expect($response->content())->toContain('al-footer');
    });
});

it('renders services section when AutoLeading theme is active', function () {
    withTheme('auto-leading-theme', function () {
        $response = get('/');
        $response->assertOk();
        expect($response->content())->toContain('al-services');
    });
});

it('renders homepage using AutoLeading override when channel theme is selected', function () {
    expect(file_exists(
        base_path('packages/Webkul/AutoLeadingTheme/src/Resources/views/home/index.blade.php')
    ))->toBeTrue();

    withTheme('auto-leading-theme', function () {
        $response = get('/');
        $response->assertOk();
        expect(Str::contains($response->content(), trans('auto-leading-theme::app.home.hero_title')))->toBeTrue();
    });
});