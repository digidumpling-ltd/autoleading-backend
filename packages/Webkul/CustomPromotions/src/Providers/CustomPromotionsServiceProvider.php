<?php

namespace Webkul\CustomPromotions\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\CustomPromotions\Listeners\RentalBookingCompleteListener;
use Webkul\CustomPromotions\Listeners\WalletPromotionListener;
use Webkul\CustomPromotions\Services\ConditionEvaluator;
use Webkul\CustomPromotions\Services\PromotionActionHandler;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class CustomPromotionsServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/admin-menu.php', 'menu.admin');
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php', 'acl');

        $this->app->singleton(ConditionEvaluator::class);
        $this->app->singleton(PromotionActionHandler::class);
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Database/Migrations');
        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'custom_promotions');
        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'custom_promotions');
        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');
        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/shop-routes.php');

        Event::listen(WalletBalanceUpdated::class, WalletPromotionListener::class);
        Event::listen('booking_product.booking.save.after', RentalBookingCompleteListener::class);
    }
}
