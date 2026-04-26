<?php

namespace Webkul\Yedpay\Payment;

use Webkul\Payment\Payment\Payment;

class Yedpay extends Payment
{
    protected $code = 'yedpay';

    public function getRedirectUrl()
    {
        return route('yedpay.standard.redirect');
    }

    public function isAvailable()
    {
        return parent::isAvailable() && $this->hasValidCredentials();
    }

    public function getTitle()
    {
        return $this->getConfigData('title') ?? trans('yedpay::app.title');
    }

    public function getDescription()
    {
        return $this->getConfigData('description') ?? trans('yedpay::app.description');
    }

    public function hasValidCredentials(): bool
    {
        return ! empty($this->getConfigData('api_key'))
            && ! empty($this->getConfigData('signing_key'));
    }

    public function getApiKey(): ?string
    {
        return $this->getConfigData('api_key');
    }

    public function getSigningKey(): ?string
    {
        return $this->getConfigData('signing_key');
    }

    public function isSandbox(): bool
    {
        return (bool) $this->getConfigData('sandbox');
    }
}
