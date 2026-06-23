<?php

namespace Webkul\CustomPromotions\Services;

use Illuminate\Support\Facades\DB;
use Webkul\Customer\Models\Customer;
use Webkul\Sales\Models\Order;

class ConditionEvaluator
{
    public function matches(array $conditions, int $conditionType, array $eventData, ?Customer $customer = null): bool
    {
        if (empty($conditions)) {
            return true;
        }

        $results = array_map(
            fn ($condition) => $this->evaluateCondition($condition, $eventData, $customer),
            $conditions
        );

        return $conditionType === 1
            ? ! in_array(false, $results, true)
            : in_array(true, $results, true);
    }

    private function evaluateCondition(array $condition, array $eventData, ?Customer $customer): bool
    {
        $attribute = $condition['attribute'] ?? null;
        $operator = $condition['operator'] ?? '==';
        $value = $condition['value'] ?? null;

        if ($attribute === null) {
            return true;
        }

        $actual = $this->resolveAttribute($attribute, $eventData, $customer);

        return $this->compare($actual, $operator, $value);
    }

    private function resolveAttribute(string $attribute, array $eventData, ?Customer $customer): mixed
    {
        if (array_key_exists($attribute, $eventData)) {
            return $eventData[$attribute];
        }

        return match ($attribute) {
            'is_first_topup' => $this->isFirstTopup($customer, $eventData['transaction_id'] ?? null),
            'is_first_booking' => $this->isFirstBooking($customer, $eventData['booking_id'] ?? null),
            default => null,
        };
    }

    private function compare(mixed $actual, string $operator, mixed $expected): bool
    {
        if (is_numeric($actual) && is_numeric($expected)) {
            $actual = (float) $actual;
            $expected = (float) $expected;
        }

        return match ($operator) {
            '==' => $actual == $expected,
            '!=' => $actual != $expected,
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            default => false,
        };
    }

    private function isFirstTopup(?Customer $customer, ?int $excludeTransactionId = null): bool
    {
        if (! $customer) {
            return false;
        }

        return ! DB::table('transactions')
            ->where('wallet_id', function ($q) use ($customer) {
                $q->select('id')->from('wallets')->where('holder_type', Customer::class)->where('holder_id', $customer->id);
            })
            ->where('type', 'deposit')
            ->where('meta->creator_type', 'customer')
            ->when($excludeTransactionId, fn ($q) => $q->where('id', '!=', $excludeTransactionId))
            ->exists();
    }

    private function isFirstBooking(?Customer $customer, ?int $excludeBookingId = null): bool
    {
        if (! $customer) {
            return false;
        }

        return ! DB::table('bookings')
            ->join('orders', 'bookings.order_id', '=', 'orders.id')
            ->where('orders.customer_id', $customer->id)
            ->whereNotIn('orders.status', [Order::STATUS_CANCELED])
            ->when($excludeBookingId, fn ($q) => $q->where('bookings.id', '!=', $excludeBookingId))
            ->exists();
    }

    public function getWalletConditionAttributes(): array
    {
        return [
            ['code' => 'topup_amount',  'type' => 'price',   'label' => trans('custom_promotions::app.admin.conditions.topup-amount')],
            ['code' => 'spend_amount',  'type' => 'price',   'label' => trans('custom_promotions::app.admin.conditions.spend-amount')],
            ['code' => 'is_first_topup', 'type' => 'boolean', 'label' => trans('custom_promotions::app.admin.conditions.is-first-topup')],
        ];
    }

    public function getRentalConditionAttributes(): array
    {
        return [
            ['code' => 'rental_start_date',  'type' => 'date',    'label' => trans('custom_promotions::app.admin.conditions.rental-start-date')],
            ['code' => 'rental_end_date',    'type' => 'date',    'label' => trans('custom_promotions::app.admin.conditions.rental-end-date')],
            ['code' => 'rental_total',       'type' => 'price',   'label' => trans('custom_promotions::app.admin.conditions.rental-total')],
            ['code' => 'rental_total_days',  'type' => 'integer', 'label' => trans('custom_promotions::app.admin.conditions.rental-total-days')],
            ['code' => 'is_first_booking',   'type' => 'boolean', 'label' => trans('custom_promotions::app.admin.conditions.is-first-booking')],
        ];
    }
}
