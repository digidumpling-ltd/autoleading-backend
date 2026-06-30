<?php

namespace Webkul\CustomPromotions\Repositories;

use Illuminate\Support\Collection;
use Webkul\Customer\Models\Customer;
use Webkul\CustomPromotions\Contracts\WalletPromotionRule;

class WalletPromotionRuleRepository extends PromotionRuleRepository
{
    public function model(): string
    {
        return WalletPromotionRule::class;
    }

    protected function promotionType(): string
    {
        return 'wallet';
    }

    public function getActiveRulesForCustomer(Customer $customer): Collection
    {
        return $this->getActiveRules($customer);
    }
}
