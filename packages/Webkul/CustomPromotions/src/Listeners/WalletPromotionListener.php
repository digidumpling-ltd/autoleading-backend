<?php

namespace Webkul\CustomPromotions\Listeners;

use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Repositories\WalletPromotionRuleRepository;
use Webkul\CustomPromotions\Services\ConditionEvaluator;
use Webkul\CustomPromotions\Services\PromotionActionHandler;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class WalletPromotionListener
{
    public function __construct(
        protected WalletPromotionRuleRepository $ruleRepository,
        protected ConditionEvaluator $conditionEvaluator,
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

        $rules = $this->ruleRepository->getActiveRulesForCustomer($customer);

        foreach ($rules as $rule) {
            if ($this->conditionEvaluator->matches(
                $rule->conditions ?? [],
                (int) $rule->condition_type,
                $eventData,
                $customer
            )) {
                $this->actionHandler->execute($rule, $customer, [
                    'eventAmount' => $eventAmount,
                ]);
            }
        }
    }
}
