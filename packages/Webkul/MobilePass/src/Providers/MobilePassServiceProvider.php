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

        $this->overrideAppleConfig();

        $this->commands([SetupGoogleLoyaltyClass::class, SyncCustomerPass::class]);

        $this->app->register(EventServiceProvider::class);

        Event::listen(
            WalletBalanceUpdated::class,
            SyncGooglePassBalance::class
        );

        /*
         * Keep the wallet passes in step when a customer's reward-point balance
         * changes (admin allocation, registration bonus, redemption, etc). The
         * rewards module fires these events with the affected RewardPoint model
         * (which carries customer_id) wrapped in an array; the wallet-balance
         * listener above only covers money changes, so without this a points
         * change would leave the pass's Points field stale.
         */
        Event::listen([
            'reward.points.save.after',
            'reward.points.update.after',
            'reward.points.register.after',
        ], function ($payload) {
            $reward = is_array($payload) ? ($payload[0] ?? null) : $payload;

            $customerId = is_object($reward) ? ($reward->customer_id ?? null) : null;

            if (! $customerId) {
                return;
            }

            $service = app(MobilePassService::class);

            if (! $service->isEnabled()) {
                return;
            }

            if ($googlePass = $service->getCustomerGooglePass($customerId)) {
                $service->syncPassContent($googlePass, $customerId);
            }

            $service->syncApplePassContent($customerId);
        });

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

    protected function overrideAppleConfig(): void
    {
        try {
            $organizationName = core()->getConfigData('sales.mobile_pass.apple.organization_name');
            $typeIdentifier = core()->getConfigData('sales.mobile_pass.apple.type_identifier');
            $teamIdentifier = core()->getConfigData('sales.mobile_pass.apple.team_identifier');
            $certificate = core()->getConfigData('sales.mobile_pass.apple.certificate');
            $certificatePassword = core()->getConfigData('sales.mobile_pass.apple.certificate_password');

            if ($organizationName) {
                config(['mobile-pass.apple.organization_name' => $organizationName]);
            }

            if ($typeIdentifier) {
                config(['mobile-pass.apple.type_identifier' => $typeIdentifier]);
            }

            if ($teamIdentifier) {
                config(['mobile-pass.apple.team_identifier' => $teamIdentifier]);
            }

            if ($certificate) {
                // The certificate is stored as base64 of the .p12 binary; strip any
                // whitespace/newlines a textarea may insert. The package decodes this
                // and writes a temp .p12 (see ApplePassBuilder::getCertificatePath()).
                $certificate = preg_replace('/\s+/', '', (string) $certificate);

                config(['mobile-pass.apple.certificate' => $certificate]);
                config(['mobile-pass.apple.certificate_path' => null]);
            }

            if ($certificatePassword !== null && $certificatePassword !== '') {
                config(['mobile-pass.apple.certificate_password' => $certificatePassword]);
            }

            // The Apple PassKit web-service host is the storefront itself. Apple
            // requires that whenever a pass declares webServiceURL it ALSO carries
            // an authenticationToken (>= 16 chars); a pass with one but not the
            // other is rejected on-device ("Safari cannot download this file").
            // The package builds webServiceURL from webservice.host and sets
            // authenticationToken from webservice.secret, so we must supply both.
            // The secret must be STABLE across requests (it is embedded in the
            // pass and used to authenticate device update calls), so derive it
            // deterministically from the app key rather than randomising it.
            config(['mobile-pass.apple.webservice.host' => config('app.url')]);
            config(['mobile-pass.apple.webservice.secret' => hash('sha256', 'mobile-pass-apple-webservice|'.config('app.key'))]);
        } catch (\Exception) {
            // DB may not be available during installation
        }
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(dirname(__DIR__).'/Config/system.php', 'core');

    }
}
