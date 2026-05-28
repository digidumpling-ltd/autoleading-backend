<?php

namespace Webkul\Membership\Listeners;

use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Membership\Repositories\TierRuleRepository;
use Webkul\Wallet\Events\WalletBalanceUpdated;

class AssignMembershipTier
{
    public function __construct(
        protected TierRuleRepository $tierRuleRepository,
        protected CustomerRepository $customerRepository,
    ) {}

    public function handle(WalletBalanceUpdated $event): void
    {
        $tier = $this->tierRuleRepository->findMatchingTier($event->newBalance);

        if (! $tier) {
            return;
        }

        $customer = $this->customerRepository->find($event->customerId);

        if (! $customer) {
            return;
        }

        if ($customer->customer_group_id === $tier->customer_group_id) {
            return;
        }

        $this->customerRepository->update(
            ['customer_group_id' => $tier->customer_group_id],
            $customer->id
        );
    }
}
