<?php

namespace Webkul\Wallet\Events;

class WalletBalanceUpdated
{
    public function __construct(
        public readonly int $customerId,
        public readonly float $oldBalance,
        public readonly float $newBalance,
        public readonly string $reason,
        public readonly ?int $customerGroupId = null,
    ) {}
}
