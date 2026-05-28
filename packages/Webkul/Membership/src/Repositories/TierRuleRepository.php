<?php

namespace Webkul\Membership\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Membership\Models\TierRule;

class TierRuleRepository extends Repository
{
    public function model(): string
    {
        return TierRule::class;
    }

    /**
     * Replace all tier rules with the given set, ordered by sort_order.
     *
     * @param  array<int, array{min_balance: float, max_balance: float, customer_group_id: int, sort_order: int}>  $rules
     */
    public function syncRules(array $rules): void
    {
        $this->model->truncate();

        foreach ($rules as $rule) {
            $this->create([
                'min_balance'       => (float) $rule['min_balance'],
                'max_balance'       => isset($rule['max_balance']) && $rule['max_balance'] !== '' ? (float) $rule['max_balance'] : null,
                'customer_group_id' => (int) $rule['customer_group_id'],
                'sort_order'        => (int) ($rule['sort_order'] ?? 0),
            ]);
        }
    }

    /**
     * Return all rules sorted by sort_order ascending.
     */
    public function allSorted(): \Illuminate\Support\Collection
    {
        return $this->model->orderBy('sort_order')->get();
    }

    /**
     * Find the matching tier for a given balance.
     * Returns null if no tier matches (no-match: leave group unchanged).
     */
    public function findMatchingTier(float $balance): ?TierRule
    {
        return $this->model
            ->orderBy('sort_order')
            ->where('min_balance', '<=', $balance)
            ->where(function ($q) use ($balance) {
                $q->whereNull('max_balance')->orWhere('max_balance', '>=', $balance);
            })
            ->first();
    }
}
