<?php

namespace Webkul\Wallet\Services;

use Illuminate\Support\Facades\Mail;
use Webkul\Wallet\Mail\WalletTopUpSuccess;
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

    public function notifyTopUp(WalletCustomer $customer, float $amount, float $newBalance): void
    {
        Mail::queue(new WalletTopUpSuccess([
            'email'            => $customer->email,
            'name'             => $customer->name,
            'customer_id'      => $customer->id,
            'transaction_time' => now()->format('Y-m-d H:i:s'),
            'amount'           => core()->formatPrice($amount),
            'new_balance'      => core()->formatPrice($newBalance),
        ]));
    }
}
