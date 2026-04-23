<?php

namespace Webkul\Wallet\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'bagisto-wallet');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/shop-routes.php');

        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'wallet');

        Event::listen('checkout.order.save.after', 'Webkul\Wallet\Listeners\GenerateInvoice@handle');
    }
    
    /**
     * Register package config.
     *
     * @return void
     */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/payment-methods.php', 'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php', 'menu.customer'
        );
    }
}
