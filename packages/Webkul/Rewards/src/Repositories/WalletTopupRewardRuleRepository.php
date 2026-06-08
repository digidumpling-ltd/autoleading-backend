<?php

namespace Webkul\Rewards\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Rewards\Contracts\WalletTopupRewardRule;

class WalletTopupRewardRuleRepository extends Repository
{
    public function model(): string
    {
        return WalletTopupRewardRule::class;
    }

    /**
     * Find the best matching rule for a customer group, amount, and trigger.
     *
     * Priority: group-specific > global, higher priority value wins,
     * then narrowest range (smallest max_amount - min_amount).
     */
    public function findBestRule(?int $customerGroupId, float $amount, string $trigger): ?object
    {
        $query = $this->model->where('status', true)
            ->where('trigger', $trigger)
            ->where(function ($q) use ($amount) {
                $q->whereNull('min_amount')
                    ->orWhere('min_amount', '<=', $amount);
            })
            ->where(function ($q) use ($amount) {
                $q->whereNull('max_amount')
                    ->orWhere('max_amount', '>=', $amount);
            })
            ->where(function ($q) use ($customerGroupId) {
                $q->whereNull('customer_group_id');
                if ($customerGroupId) {
                    $q->orWhere('customer_group_id', $customerGroupId);
                }
            })
            ->get();

        if ($query->isEmpty()) {
            return null;
        }

        return $query->sort(function ($a, $b) use ($customerGroupId) {
            $aIsGroup = $customerGroupId && $a->customer_group_id == $customerGroupId;
            $bIsGroup = $customerGroupId && $b->customer_group_id == $customerGroupId;

            if ($aIsGroup !== $bIsGroup) {
                return $aIsGroup ? -1 : 1;
            }

            if ($a->priority !== $b->priority) {
                return $b->priority <=> $a->priority;
            }

            $aRange = ($a->max_amount !== null && $a->min_amount !== null)
                ? $a->max_amount - $a->min_amount
                : PHP_INT_MAX;

            $bRange = ($b->max_amount !== null && $b->min_amount !== null)
                ? $b->max_amount - $b->min_amount
                : PHP_INT_MAX;

            return $aRange <=> $bRange;
        })->first();
    }
}
