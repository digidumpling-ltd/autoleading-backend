<?php

namespace Webkul\Wallet\Payment;

use Webkul\Payment\Payment\Payment;

class Wallet extends Payment
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $code  = 'wallet';

    /**
     * Get redirect url.
     */
    public function getRedirectUrl()
    {
        return null; // No redirect needed
    }

    /**
     * Check if payment method is available.
     */
    public function isAvailable(): bool
    {
        if (! $this->getConfigData('active')) {
            return false;
        }

        if (! auth('customer')->check()) {
            return false;
        }

        return auth('customer')->user()->verification_status === 'approved';
    }
}
