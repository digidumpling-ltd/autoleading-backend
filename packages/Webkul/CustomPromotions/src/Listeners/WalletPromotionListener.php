<?php

namespace Webkul\CustomPromotions\Listeners;

use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Repositories\WalletPromotionRuleRepository;
use Webkul\CustomPromotions\Services\PromotionActionHandler;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class WalletPromotionListener
{
    public function __construct(
        protected WalletPromotionRuleRepository $ruleRepository,
        protected PromotionActionHandler $actionHandler,
        protected CustomerRepository $customerRepository,
    ) {}

    public function handle(WalletBalanceUpdated $event): void
    {
        if (! in_array($event->reason, ['wallet_topup', 'wallet_spend'], true)) {
            return;
        }

        $customer = $this->customerRepository->find($event->customerId);

        if (! $customer) {
            return;
        }

        $eventAmount = abs($event->newBalance - $event->oldBalance);
        $amountKey = $event->reason === 'wallet_topup' ? 'topup_amount' : 'spend_amount';

        $eventData = [
            $amountKey => $eventAmount,
            'transaction_id' => $event->transactionId,
        ];

        $eventContext = ['eventAmount' => $eventAmount];

        $rules = $this->ruleRepository->getActiveRulesForCustomer($customer);

        $this->actionHandler->processRules($rules, $customer, $eventData, $eventContext);
    }
}
