<?php

namespace Webkul\AutoLeadingTheme\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AutoLeadingThemeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'auto-leading-theme');

        $this->publishes([
            __DIR__.'/../Resources/views' => resource_path('themes/auto-leading-theme/views'),
        ], 'auto-leading-theme-views');

        Blade::anonymousComponentPath(
            __DIR__.'/../Resources/views/components',
            'auto-leading-theme'
        );

        view()->composer('shop::home.index', function ($view) {
            /** @var \Webkul\Product\Repositories\ProductRepository $repo */
            $repo = app(\Webkul\Product\Repositories\ProductRepository::class);

            $view->with('featuredProducts', $repo->with(['images'])
                ->scopeQuery(fn ($q) => $q
                    ->join('product_flat as pf', 'products.id', '=', 'pf.product_id')
                    ->where('pf.status', 1)
                    ->where('pf.visible_individually', 1)
                    ->where('pf.locale', app()->getLocale())
                    ->select('products.*')
                    ->orderBy('products.created_at', 'desc')
                    ->limit(4)
                )
                ->all());

            $wishlistedProductIds = [];

            if (auth()->guard('customer')->check()) {
                $wishlistedProductIds = app(\Webkul\Customer\Repositories\WishlistRepository::class)
                    ->findWhere(['customer_id' => auth()->guard('customer')->id()])
                    ->pluck('product_id')
                    ->toArray();
            }

            $view->with('wishlistedProductIds', $wishlistedProductIds);
        });

        view()->composer('shop::search.index', function ($view) {
            /** @var \Webkul\Product\Repositories\ProductRepository $repo */
            $repo = app(\Webkul\Product\Repositories\ProductRepository::class);

            $locale = app()->getLocale();
            $perPage = (int) request('limit', 12);

            $products = $repo->with(['images'])
                ->scopeQuery(function ($q) use ($locale) {
                    $q->join('product_flat as pf', 'products.id', '=', 'pf.product_id')
                      ->where('pf.status', 1)
                      ->where('pf.visible_individually', 1)
                      ->where('pf.locale', $locale)
                      ->select('products.*');

                    if ($name = request('query') ?: request('name')) {
                        $q->where('pf.name', 'like', '%' . $name . '%');
                    }

                    if ($brand = request('brand')) {
                        $q->whereHas('attribute_values', fn ($av) =>
                            $av->whereHas('attribute', fn ($a) => $a->where('code', 'brand'))
                               ->where('integer_value', $brand)
                        );
                    }

                    if ($type = request('type')) {
                        $q->whereHas('attribute_values', fn ($av) =>
                            $av->whereHas('attribute', fn ($a) => $a->where('code', 'product_type'))
                               ->where('integer_value', $type)
                        );
                    }

                    match (request('sort')) {
                        'price_low'  => $q->orderBy('pf.price', 'asc'),
                        'price_high' => $q->orderBy('pf.price', 'desc'),
                        default      => $q->orderBy('products.created_at', 'desc'),
                    };

                    return $q;
                })
                ->paginate($perPage)
                ->withQueryString();

            $view->with('products', $products);

            $wishlistedProductIds = [];

            if (auth()->guard('customer')->check()) {
                $wishlistedProductIds = app(\Webkul\Customer\Repositories\WishlistRepository::class)
                    ->findWhere(['customer_id' => auth()->guard('customer')->id()])
                    ->pluck('product_id')
                    ->toArray();
            }

            $view->with('wishlistedProductIds', $wishlistedProductIds);
        });
    }
}