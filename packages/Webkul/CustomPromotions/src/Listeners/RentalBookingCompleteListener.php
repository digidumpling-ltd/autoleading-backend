<?php

namespace Webkul\CustomPromotions\Listeners;

use Carbon\Carbon;
use Webkul\BookingProduct\Models\Booking;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\CustomPromotions\Repositories\RentalPromotionRuleRepository;
use Webkul\CustomPromotions\Services\PromotionActionHandler;

class RentalBookingCompleteListener
{
    public function __construct(
        protected RentalPromotionRuleRepository $ruleRepository,
        protected PromotionActionHandler $actionHandler,
        protected CustomerRepository $customerRepository,
    ) {}

    public function handle(Booking $booking): void
    {
        $order = $booking->order;

        if (! $order || ! $order->customer_id) {
            return;
        }

        $customer = $this->customerRepository->find($order->customer_id);

        if (! $customer) {
            return;
        }

        $from = $booking->from;
        $to = $booking->to;

        $rentalTotalDays = $from && $to ? (int) round(($to - $from) / 86400) : 0;
        $rentalTotal = (float) ($order->grand_total ?? 0);

        $eventData = [
            'rental_start_date' => $from ? Carbon::createFromTimestamp($from)->toDateString() : null,
            'rental_end_date'   => $to ? Carbon::createFromTimestamp($to)->toDateString() : null,
            'rental_total'      => $rentalTotal,
            'rental_total_days' => $rentalTotalDays,
            'booking_id'        => $booking->id,
        ];

        $eventContext = [
            'eventAmount' => $rentalTotal,
            'booking'     => $booking,
            'order'       => $order,
        ];

        $rules = $this->ruleRepository->getActiveRulesForBooking($customer);

        $this->actionHandler->processRules($rules, $customer, $eventData, $eventContext);
    }
}
