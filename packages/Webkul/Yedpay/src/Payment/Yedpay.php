<?php

namespace Webkul\Yedpay\Payment;

use Webkul\Payment\Payment\Payment;
use Webkul\Wallet\Contracts\SupportsWalletTopUp;

class Yedpay extends Payment implements SupportsWalletTopUp
{
    protected $code = 'yedpay';

    public function getRedirectUrl()
    {
        return route('yedpay.standard.redirect');
    }

    public function getTopUpRedirectUrl(): string
    {
        return route('yedpay.topup.redirect');
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
        $apiKey = $this->isSandbox()
            ? $this->getConfigData('sandbox_api_key')
            : $this->getConfigData('api_key');

        return ! empty($apiKey) && ! empty($this->getConfigData('signing_key'));
    }

    public function getApiKey(): ?string
    {
        if ($this->isSandbox()) {
            return $this->getConfigData('sandbox_api_key');
        }

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
