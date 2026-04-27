<?php

namespace Webkul\Wallet\Contracts;

interface SupportsWalletTopUp
{
    public function getTopUpRedirectUrl(): string;
}
