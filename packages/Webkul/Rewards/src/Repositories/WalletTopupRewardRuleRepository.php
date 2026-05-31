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
     * Find the best matching rule for a customer group and topup amount.
     *
     * Priority: group-specific > global, higher priority value wins,
     * then narrowest range (smallest max_topup_amount - min_topup_amount).
     */
    public function findBestRule(?int $customerGroupId, float $topupAmount): ?object
    {
        $query = $this->model->where('status', true)
            ->where(function ($q) use ($topupAmount) {
                $q->whereNull('min_topup_amount')
                    ->orWhere('min_topup_amount', '<=', $topupAmount);
            })
            ->where(function ($q) use ($topupAmount) {
                $q->whereNull('max_topup_amount')
                    ->orWhere('max_topup_amount', '>=', $topupAmount);
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

        // Prefer group-specific over global, then higher priority, then narrowest range
        return $query->sort(function ($a, $b) use ($customerGroupId) {
            $aIsGroup = $customerGroupId && $a->customer_group_id == $customerGroupId;
            $bIsGroup = $customerGroupId && $b->customer_group_id == $customerGroupId;

            if ($aIsGroup !== $bIsGroup) {
                return $aIsGroup ? -1 : 1;
            }

            if ($a->priority !== $b->priority) {
                return $b->priority <=> $a->priority;
            }

            $aRange = ($a->max_topup_amount !== null && $a->min_topup_amount !== null)
                ? $a->max_topup_amount - $a->min_topup_amount
                : PHP_INT_MAX;

            $bRange = ($b->max_topup_amount !== null && $b->min_topup_amount !== null)
                ? $b->max_topup_amount - $b->min_topup_amount
                : PHP_INT_MAX;

            return $aRange <=> $bRange;
        })->first();
    }
}
