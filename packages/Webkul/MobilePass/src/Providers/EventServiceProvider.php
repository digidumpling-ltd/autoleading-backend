<?php

namespace Webkul\MobilePass\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen(
            'bagisto.admin.customers.customers.view.card.accordion.customer.before',
            function ($viewRenderEventManager) {
                if (! app(\Webkul\MobilePass\Services\MobilePassService::class)->isEnabled()) {
                    return;
                }

                $viewRenderEventManager->addTemplate(
                    'mobile-pass::admin.customers.pass-status-card',
                    ['customerId' => request()->route('id')]
                );
            }
        );

        Event::listen(
            'bagisto.shop.customers.account.profile.delete.before',
            function ($viewRenderEventManager) {
                if (! app(\Webkul\MobilePass\Services\MobilePassService::class)->isEnabled()) {
                    return;
                }

                $viewRenderEventManager->addTemplate(
                    'mobile-pass::shop.customers.profile.google-wallet-row'
                );
            }
        );
    }
}
