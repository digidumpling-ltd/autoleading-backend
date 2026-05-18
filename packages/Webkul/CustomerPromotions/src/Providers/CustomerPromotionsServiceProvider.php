<?php

namespace Webkul\CustomerPromotions\Providers;

use Illuminate\Support\ServiceProvider;

class CustomerPromotionsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadRoutesFrom(__DIR__.'/../Routes/shop-routes.php');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'customer_promotions');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'customer_promotions');
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../Config/menu.php',
            'menu.customer'
        );
    }
}
