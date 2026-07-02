<?php

namespace Webkul\Wallet\Providers;

use Bavix\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Http\Middleware\WalletCheckoutMiddleware;
use Webkul\Wallet\Http\Middleware\WalletTopUpGatingMiddleware;
use Webkul\Wallet\Listeners\WalletNotificationListener;
use Webkul\Wallet\Listeners\WalletTransactionListener;
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

        Event::listen(WalletBalanceUpdated::class, WalletNotificationListener::class);

        Event::listen(TransactionCreatedEventInterface::class, WalletTransactionListener::class);

        $this->app['router']->pushMiddlewareToGroup('web', WalletCheckoutMiddleware::class);

        $this->app['router']->pushMiddlewareToGroup('web', WalletTopUpGatingMiddleware::class);

        Event::listen('bagisto.shop.checkout.onepage.summary.grand_total.after', function ($event) {
            $event->addTemplate('wallet::shop.checkout.wallet-balance-widget');
        });

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
