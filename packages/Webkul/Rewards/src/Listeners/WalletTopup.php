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
        if ($event->reason !== 'topup') {
            return;
        }

        if ($event->newBalance <= $event->oldBalance) {
            return;
        }

        if (! core()->getConfigData('reward.general.general.module-status')) {
            return;
        }

        $topupAmount = $event->newBalance - $event->oldBalance;

        $rule = $this->ruleRepository->findBestRule($event->customerGroupId, $topupAmount);

        if (! $rule) {
            return;
        }

        $points = $rule->mode === 'fixed'
            ? (int) $rule->value
            : (int) floor($topupAmount * $rule->value / 100);

        if ($points < 1) {
            return;
        }

        $this->rewardPointRepository->awardPoints($event->customerId, $points, 'Wallet topup reward');
    }
}
