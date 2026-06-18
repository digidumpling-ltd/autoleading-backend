<?php

namespace Webkul\MobilePass\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Webkul\MobilePass\Console\SetupGoogleLoyaltyClass;
use Webkul\MobilePass\Console\SyncCustomerPass;
use Webkul\MobilePass\Listeners\SyncGooglePassBalance;
use Webkul\MobilePass\Services\MobilePassService;
use Webkul\Rewards\Models\RewardPoint;
use Webkul\Rewards\Repositories\RewardPointRepository;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class MobilePassServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(MobilePassService::class);

        $this->registerConfig();
    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(dirname(__DIR__).'/Resources/lang', 'mobile-pass');

        $this->loadViewsFrom(dirname(__DIR__).'/Resources/views', 'mobile-pass');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/admin-routes.php');

        $this->loadRoutesFrom(dirname(__DIR__).'/Routes/shop-routes.php');

        $this->publishes([
            dirname(__DIR__).'/Resources/assets/images' => public_path('vendor/mobile-pass/images'),
        ], 'mobile-pass-assets');

        $this->overrideGoogleConfig();

        $this->commands([SetupGoogleLoyaltyClass::class, SyncCustomerPass::class]);

        $this->app->register(EventServiceProvider::class);

        Event::listen(
            WalletBalanceUpdated::class,
            SyncGooglePassBalance::class
        );

        Event::listen('checkout.order.save.after', function ($order) {
            if (! in_array($order->payment->method ?? '', ['wallet'])) {
                return;
            }

            if (! in_array($order->status, ['processing', 'completed'])) {
                return;
            }

            if (! core()->getConfigData('reward.general.general.module-status')) {
                return;
            }

            $rewardRepo = app(RewardPointRepository::class);

            $note = 'Wallet payment reward order:'.$order->id;

            $alreadyRewarded = RewardPoint::where('customer_id', $order->customer_id)
                ->where('note', $note)
                ->exists();

            if ($alreadyRewarded) {
                return;
            }

            $rewardRepo->awardPoints($order->customer_id, 1, $note);
        });
    }

    protected function overrideGoogleConfig(): void
    {
        try {
            $issuerId = core()->getConfigData('sales.mobile_pass.google.issuer_id');
            $serviceAccountKey = core()->getConfigData('sales.mobile_pass.google.service_account_key');

            if ($issuerId) {
                config(['mobile-pass.google.issuer_id' => $issuerId]);
            }

            if ($serviceAccountKey) {
                // Strip whitespace/newlines that textareas or base64 encoders may insert
                $normalized = ltrim((string) $serviceAccountKey);
                if (! str_starts_with($normalized, '{')) {
                    $serviceAccountKey = preg_replace('/\s+/', '', $serviceAccountKey);
                }

                config(['mobile-pass.google.service_account_key' => $serviceAccountKey]);
                config(['mobile-pass.google.service_account_key_path' => null]);
            }
        } catch (\Exception) {
            // DB may not be available during installation
        }
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/system.php', 'core');

    }
}
