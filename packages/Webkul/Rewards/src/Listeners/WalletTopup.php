<?php

namespace Webkul\Rewards\Listeners;

use Webkul\Rewards\Repositories\RewardPointRepository;
use Webkul\Rewards\Repositories\WalletTopupRewardRuleRepository;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class WalletTopup
{
    public function __construct(
        protected WalletTopupRewardRuleRepository $ruleRepository,
        protected RewardPointRepository $rewardPointRepository,
    ) {
    }

    public function handle(WalletBalanceUpdated $event): void
    {
        if (! in_array($event->reason, ['wallet_topup', 'wallet_spend'])) {
            return;
        }

        if (! core()->getConfigData('reward.general.general.module-status')) {
            return;
        }

        $amount = $event->reason === 'wallet_topup'
            ? $event->newBalance - $event->oldBalance
            : $event->oldBalance - $event->newBalance;

        if ($amount <= 0) {
            return;
        }

        $rule = $this->ruleRepository->findBestRule($event->customerGroupId, $amount, $event->reason);

        if (! $rule) {
            return;
        }

        $points = $rule->mode === 'fixed'
            ? (int) $rule->value
            : (int) floor($amount * $rule->value / 100);

        if ($points < 1) {
            return;
        }

        $label = $event->reason === 'wallet_topup' ? 'Wallet topup reward' : 'Wallet spend reward';

        $this->rewardPointRepository->awardPoints($event->customerId, $points, $label);
    }
}
