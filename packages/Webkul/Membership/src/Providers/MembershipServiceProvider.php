<?php

namespace Webkul\Membership\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\Theme\ViewRenderEventManager;

class MembershipServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/acl.php',  'acl');
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/menu.php', 'menu.admin');
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Database/Migrations');

        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'bagisto-membership');

        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'membership');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');

        Event::listen(
            \Webkul\Wallet\Events\WalletBalanceUpdated::class,
            \Webkul\Membership\Listeners\AssignMembershipTier::class
        );

        Event::listen('bagisto.shop.customers.account.profile.email.after', function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('membership::shop.customers.profile.membership-group');
        });
    }
}
