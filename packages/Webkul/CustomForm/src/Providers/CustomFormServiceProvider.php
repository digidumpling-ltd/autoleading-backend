<?php

namespace Webkul\CustomForm\Providers;

use Illuminate\Support\ServiceProvider;

class CustomFormServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__.'/../Database/Migrations');

        $this->loadTranslationsFrom(__DIR__.'/../Resources/lang', 'customform');

        $this->loadViewsFrom(__DIR__.'/../Resources/views', 'customform');

        $this->loadRoutesFrom(__DIR__.'/../Routes/shop-routes.php');

        $this->loadRoutesFrom(__DIR__.'/../Routes/admin-routes.php');

        $this->mergeConfigFrom(
            __DIR__.'/../Config/admin-menu.php',
            'menu.admin'
        );
    }
}
