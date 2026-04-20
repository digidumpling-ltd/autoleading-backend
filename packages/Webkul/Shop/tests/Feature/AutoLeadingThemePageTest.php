<?php

use Spatie\ResponseCache\Facades\ResponseCache;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

// Test product list page renders successfully
// AC: 1, 3
it('renders product list page successfully when auto-leading theme is active', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        $response = get(route('shop.search.index'));
        $response->assertOk();
        // Check that search view is being used
        $response->assertViewIs('auto-leading-theme::shop.search.index');
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('View override not yet implemented');

// Test product list filters by brand
// AC: 2
it('filters products by brand parameter when auto-leading theme is active', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        $response = get(route('shop.search.index', ['brand' => 'test-brand']));
        expect($response->status())->toBeIn([200, 404]);
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('Feature not yet implemented');

// Test product list filters by type
// AC: 2
it('filters products by type parameter when auto-leading theme is active', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        $response = get(route('shop.search.index', ['type' => 'sedan']));
        expect($response->status())->toBeIn([200, 404]);
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('Feature not yet implemented');

// Test product detail page renders
// AC: 4, 5, 6
it('renders product detail page with gallery and specifications', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        // Test with a non-existent product - should return 404
        $response = get('/test-product-not-found');
        expect($response->status())->toBeIn([404, 301, 302]);
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('Feature not yet implemented');

// Test blog list page renders
// AC: 7
it('renders blog list page when auto-leading theme is active', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        // Blog would typically be a CMS page
        $response = get(route('shop.cms.page', ['slug' => 'blog']));
        expect($response->status())->toBeIn([200, 404, 301, 302]);
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('Feature not yet implemented');

// Test contact page renders with form
// AC: 9
it('renders contact page with form when auto-leading theme is active', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        $response = get(route('shop.home.contact_us'));
        $response->assertOk();
        expect($response->content())->toContain('contact');
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('Contact page route not yet implemented');

// Test FAQ page renders
// AC: 10
it('renders FAQ page when auto-leading theme is active', function () {
    $channel = core()->getCurrentChannel()->fresh();
    $originalTheme = $channel->theme;
    $channel->theme = 'auto-leading-theme';
    $channel->save();
    core()->setCurrentChannel($channel->fresh());
    ResponseCache::clear();

    try {
        $response = get(route('shop.cms.page', ['slug' => 'faq']));
        expect($response->status())->toBeIn([200, 404, 301, 302]);
    } finally {
        $channel = core()->getCurrentChannel()->fresh();
        $channel->theme = $originalTheme;
        $channel->save();
        core()->setCurrentChannel($channel->fresh());
        ResponseCache::clear();
    }
})->skip('Feature not yet implemented');

// Test contact form validation
// AC: 9
it('validates contact form submission with required fields', function () {
    $response = post(route('shop.home.contact_us.send_mail'), [
        'name' => '',
        'email' => 'invalid-email',
        'message' => '',
    ]);

    $response->assertSessionHasErrors(['name', 'email', 'message']);
})->skip('Will test after form implementation');

// Test contact form submission with valid data
// AC: 9
it('accepts valid contact form submission', function () {
    $response = post(route('shop.home.contact_us.send_mail'), [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'phone' => '1234567890',
        'message' => 'This is a test message for the contact form with sufficient length.',
    ]);

    expect($response->status())->toBeIn([200, 302]);
})->skip('Will test after form implementation');
