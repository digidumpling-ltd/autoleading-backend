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

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'auto-leading-theme');

        $this->loadRoutesFrom(__DIR__.'/../Routes/theme-routes.php');

        $this->publishes([
            __DIR__.'/../Resources/views' => resource_path('themes/auto-leading-theme/views'),
        ], 'auto-leading-theme-views');

        Blade::anonymousComponentPath(
            __DIR__.'/../Resources/views/components',
            'auto-leading-theme'
        );

        view()->composer('auto-leading-theme::home.index', function ($view) {
            /** @var \Webkul\Product\Repositories\ProductRepository $repo */
            $repo = app(\Webkul\Product\Repositories\ProductRepository::class);

            $view->with('featuredProducts', $repo->with(['media'])
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
        });
    }
}