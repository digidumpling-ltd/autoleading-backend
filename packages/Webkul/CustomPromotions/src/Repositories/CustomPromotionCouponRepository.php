<?php

namespace Webkul\CustomPromotions\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\CustomPromotions\Contracts\CustomPromotionCoupon;

class CustomPromotionCouponRepository extends Repository
{
    public function model(): string
    {
        return CustomPromotionCoupon::class;
    }

    public function syncForRule(int $ruleId, string $promotionType, array $data): void
    {
        if (! ($data['coupon_type'] ?? false)) {
            $this->deleteWhere(['promotion_id' => $ruleId, 'promotion_type' => $promotionType, 'is_primary' => 1]);

            return;
        }

        $existing = $this->findOneWhere(['promotion_id' => $ruleId, 'promotion_type' => $promotionType, 'is_primary' => 1]);

        $payload = [
            'code'               => $data['coupon_code'] ?? null,
            'usage_limit'        => $data['uses_per_coupon'] ?? 0,
            'usage_per_customer' => $data['usage_per_customer'] ?? 0,
            'expired_at'         => $data['ends_till'] ?? null,
        ];

        if ($existing) {
            $this->update($payload, $existing->id);
        } else {
            $this->create(array_merge($payload, [
                'promotion_id'   => $ruleId,
                'promotion_type' => $promotionType,
                'times_used'     => 0,
                'is_primary'     => 1,
            ]));
        }
    }
}
