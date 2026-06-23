<?php

namespace Webkul\CustomPromotions\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Webkul\Customer\Models\Customer;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Rewards\Repositories\RewardPointRepository;
use Webkul\Wallet\Events\WalletBalanceUpdated;
use Webkul\Wallet\Models\Customer as WalletCustomer;

class PromotionActionHandler
{
    public function __construct(
        protected RewardPointRepository $rewardPointRepository,
        protected ProductRepository $productRepository,
    ) {}

    public function execute(object $rule, Customer $customer, array $eventContext = []): void
    {
        $eventAmount = $eventContext['eventAmount'] ?? 0;

        match ($rule->action_type) {
            'reward_points' => $this->handleRewardPoints($rule, $customer, $eventAmount),
            'wallet_credit' => $this->handleWalletCredit($rule, $customer, $eventAmount),
            'reward_product' => $this->handleRewardProduct($rule, $eventContext),
            'free_extension' => $this->handleFreeExtension($rule, $eventContext),
            default => null,
        };
    }

    private function handleRewardPoints(object $rule, Customer $customer, float $eventAmount): void
    {
        $points = $rule->reward_mode === 'percentage'
            ? (int) floor($eventAmount * $rule->reward_value / 100)
            : (int) $rule->reward_value;

        if ($points > 0) {
            $this->rewardPointRepository->awardPoints(
                $customer->id,
                $points,
                trans('custom_promotions::app.reward.points-note', ['rule' => $rule->name])
            );
        }
    }

    private function handleWalletCredit(object $rule, Customer $customer, float $eventAmount): void
    {
        $amount = $rule->reward_mode === 'percentage'
            ? round($eventAmount * $rule->reward_value / 100, 2)
            : (float) $rule->reward_value;

        if ($amount > 0) {
            $walletCustomer = $this->resolveWalletCustomer($customer->id);

            if ($walletCustomer) {
                $oldBalance = $walletCustomer->balanceFloatNum;

                $walletCustomer->depositFloat($amount, [
                    'type' => 'promotion',
                    'description' => trans('custom_promotions::app.reward.wallet-credit-note', ['rule' => $rule->name]),
                ]);

                Event::dispatch(new WalletBalanceUpdated(
                    customerId: $walletCustomer->id,
                    oldBalance: $oldBalance,
                    newBalance: $walletCustomer->fresh()->balanceFloatNum,
                    reason: 'wallet_reward',
                    customerGroupId: $walletCustomer->customer_group_id,
                ));
            }
        }
    }

    protected function resolveWalletCustomer(int $customerId): ?WalletCustomer
    {
        return WalletCustomer::find($customerId);
    }

    private function handleRewardProduct(object $rule, array $eventContext): void
    {
        $order = $eventContext['order'] ?? null;

        if (! $order) {
            return;
        }

        if ($rule->reward_mode === 'note') {
            DB::table('order_comments')->insert([
                'order_id' => $order->id,
                'comment' => $rule->reward_value,
                'customer_notified' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return;
        }

        $productId = (int) $rule->reward_value;

        if (! $productId) {
            return;
        }

        $product = $this->productRepository->find($productId);

        if (! $product) {
            return;
        }

        DB::table('order_items')->insert([
            'order_id' => $order->id,
            'product_id' => $product->id,
            'product_type' => get_class($product),
            'sku' => $product->sku,
            'name' => $product->name,
            'type' => $product->type,
            'qty_ordered' => 1,
            'price' => 0,
            'base_price' => 0,
            'total' => 0,
            'base_total' => 0,
            'additional' => json_encode(['promotion_rule_id' => $rule->id]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    private function handleFreeExtension(object $rule, array $eventContext): void
    {
        $booking = $eventContext['booking'] ?? null;
        $extensionDays = (int) $rule->reward_value;

        if (! $booking || ! $extensionDays) {
            return;
        }

        $originalTo = Carbon::createFromTimestamp($booking->to)->format('d/m/Y');
        $newTo = Carbon::createFromTimestamp($booking->to)->addDays($extensionDays)->getTimestamp();
        $newToDate = Carbon::createFromTimestamp($newTo)->format('d/m/Y');

        DB::table('bookings')->where('id', $booking->id)->update(['to' => $newTo]);

        $order = $eventContext['order'] ?? null;

        if ($order) {
            DB::table('order_comments')->insert([
                'order_id' => $order->id,
                'comment' => "+{$extensionDays} complimentary day(s) — return date extended from {$originalTo} to {$newToDate}",
                'customer_notified' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
