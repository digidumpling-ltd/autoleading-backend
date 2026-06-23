<?php

namespace Webkul\CustomPromotions\Repositories;

use Illuminate\Support\Collection;
use Webkul\Core\Eloquent\Repository;
use Webkul\Customer\Models\Customer;
use Webkul\CustomPromotions\Contracts\RentalPromotionRule;

class RentalPromotionRuleRepository extends Repository
{
    public function model(): string
    {
        return RentalPromotionRule::class;
    }

    public function create(array $data): mixed
    {
        $data['starts_from'] = $data['starts_from'] ?: null;
        $data['ends_till'] = $data['ends_till'] ?: null;
        $data['status'] = isset($data['status']);

        $rule = parent::create($data);

        $rule->channels()->sync($data['channels'] ?? []);
        $rule->customerGroups()->sync($data['customer_groups'] ?? []);

        return $rule;
    }

    public function update(array $data, $id): mixed
    {
        $data = array_merge($data, [
            'starts_from' => $data['starts_from'] ?: null,
            'ends_till' => $data['ends_till'] ?: null,
            'status' => isset($data['status']),
            'conditions' => $data['conditions'] ?? [],
        ]);

        $rule = parent::update($data, $id);

        $rule->channels()->sync($data['channels'] ?? []);
        $rule->customerGroups()->sync($data['customer_groups'] ?? []);

        return $rule;
    }

    public function getActiveRulesForBooking(Customer $customer): Collection
    {
        $today = now()->toDateString();
        $channelId = core()->getCurrentChannel()->id;

        return $this->model
            ->where('status', true)
            ->where(function ($q) use ($today) {
                $q->whereNull('starts_from')->orWhere('starts_from', '<=', $today);
            })
            ->where(function ($q) use ($today) {
                $q->whereNull('ends_till')->orWhere('ends_till', '>=', $today);
            })
            ->whereHas('channels', fn ($q) => $q->where('channels.id', $channelId))
            ->whereHas('customerGroups', fn ($q) => $q->where('customer_groups.id', $customer->customer_group_id))
            ->orderBy('sort_order')
            ->get();
    }
}
