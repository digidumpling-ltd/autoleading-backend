<?php

namespace Webkul\Wallet\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Wallet\Http\Middleware\WalletCheckoutMiddleware;
use Webkul\Wallet\Services\WalletService;

class WalletServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->registerConfig();
        $this->app->singleton(WalletService::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Database/Migrations');

        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'bagisto-wallet');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/shop-routes.php');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');

        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'wallet');

        Event::listen('checkout.order.save.after', 'Webkul\Wallet\Listeners\GenerateInvoice@handle');

        Event::listen('sales.invoice.save.after', 'Webkul\Wallet\Listeners\WalletInvoiceListener@handle');

        Event::listen('sales.refund.save.after', 'Webkul\Wallet\Listeners\WalletRefundListener@handle');

        $this->app['router']->pushMiddlewareToGroup('web', WalletCheckoutMiddleware::class);

        Event::listen('bagisto.admin.customers.customers.view.filters.after', function ($event) {
            $event->addTemplate('wallet::admin.customers.wallet.button');
        });

        Event::listen('bagisto.shop.checkout.onepage.summary.grand_total.after', function ($event) {
            $event->addTemplate('wallet::shop.checkout.wallet-balance-widget');
        });

        $this->registerTopUpSystemConfig();
    }
    
    /**
     * Register package config.
     *
     * @return void
     */
    /**
     * Register the wallet top-up admin configuration section.
     * Called in boot() so config('payment_methods') is fully populated.
     */
    protected function registerTopUpSystemConfig(): void
    {
        $options = collect(config('payment_methods', []))
            ->filter(function ($m) {
                if (($m['code'] ?? '') === 'wallet') {
                    return false;
                }

                return is_a($m['class'] ?? '', \Webkul\Wallet\Contracts\SupportsWalletTopUp::class, true);
            })
            ->map(fn ($m) => ['title' => $m['title'], 'value' => $m['code']])
            ->values()
            ->toArray();

        config(['core' => array_merge(config('core', []), [
            [
                'key'  => 'sales.wallet',
                'name' => 'bagisto-wallet::app.configuration.index.sales.wallet.title',
                'info' => 'bagisto-wallet::app.configuration.index.sales.wallet.info',
                'sort' => 99,
            ],
            [
                'key'    => 'sales.wallet.settings',
                'name'   => 'bagisto-wallet::app.configuration.index.sales.wallet.settings.title',
                'info'   => 'bagisto-wallet::app.configuration.index.sales.wallet.settings.info',
                'sort'   => 1,
                'fields' => [
                    [
                        'name'          => 'topup_allowed_methods',
                        'title'         => 'bagisto-wallet::app.configuration.index.sales.wallet.topup-allowed-methods',
                        'info'          => 'bagisto-wallet::app.configuration.index.sales.wallet.topup-allowed-methods-info',
                        'type'          => 'multiselect',
                        'options'       => $options,
                        'channel_based' => true,
                        'locale_based'  => false,
                    ],
                ],
            ],
        ])]);
    }

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

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/acl.php', 'acl'
        );
    }
}
