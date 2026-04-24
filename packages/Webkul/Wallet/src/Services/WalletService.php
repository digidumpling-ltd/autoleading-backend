<?php

namespace Webkul\Wallet\Services;

use Webkul\Wallet\Models\Customer as WalletCustomer;

class WalletService
{
    public function canAfford(WalletCustomer $customer, float $amount): bool
    {
        return $customer->canWithdrawFloat($amount);
    }

    public function shortfall(WalletCustomer $customer, float $amount): float
    {
        return max(0.0, $amount - $customer->balanceFloatNum);
    }
}
