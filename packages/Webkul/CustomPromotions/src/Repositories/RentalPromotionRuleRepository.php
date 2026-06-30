<?php

namespace Webkul\CustomPromotions\Repositories;

use Illuminate\Support\Collection;
use Webkul\Customer\Models\Customer;
use Webkul\CustomPromotions\Contracts\RentalPromotionRule;

class RentalPromotionRuleRepository extends PromotionRuleRepository
{
    public function model(): string
    {
        return RentalPromotionRule::class;
    }

    protected function promotionType(): string
    {
        return 'rental';
    }

    public function getActiveRulesForBooking(Customer $customer): Collection
    {
        return $this->getActiveRules($customer);
    }
}
