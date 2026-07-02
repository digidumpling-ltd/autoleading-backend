<?php

namespace Webkul\CustomerVerification\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Webkul\CustomerVerification\Listeners\HandleCustomerRegistration;
use Webkul\CustomerVerification\Listeners\PreventUnverifiedAddToCartListener;
use Webkul\CustomerVerification\Listeners\SendDocumentsSubmittedAdminNotification;
use Webkul\CustomerVerification\Listeners\SendVerificationStatusCustomerNotification;
use Webkul\Theme\ViewRenderEventManager;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     */
    protected $listen = [
        'customer.registration.after' => [
            HandleCustomerRegistration::class,
        ],
        'checkout.cart.add.before' => [
            PreventUnverifiedAddToCartListener::class,
        ],
        'customer.verification.documents_complete' => [
            SendDocumentsSubmittedAdminNotification::class,
        ],
        'verification.admin.approved' => [
            SendVerificationStatusCustomerNotification::class . '@approved',
        ],
        'verification.admin.rejected' => [
            SendVerificationStatusCustomerNotification::class . '@rejected',
        ],
    ];

    public function boot(): void
    {
        parent::boot();

        Event::listen('bagisto.shop.customers.signup_form.newsletter_subscription.after', function (ViewRenderEventManager $viewRenderEventManager) {
            $viewRenderEventManager->addTemplate('customer-verification::shop.customers.signup.tnc-fields');
        });
    }
}