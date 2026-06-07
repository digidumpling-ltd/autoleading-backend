<?php

namespace Themes\CustomTheme\Listeners;

use Webkul\Sales\Contracts\Order as OrderContract;
use Webkul\Sales\Models\Order as OrderModel;
use Webkul\Shop\Listeners\Base;
use Themes\CustomTheme\Mail\Order\OrderConfirmedNotification;

class Order extends Base
{
    public function afterOrderConfirmed(OrderContract $order): void
    {
        try {
            if (! in_array($order->status, [OrderModel::STATUS_COMPLETED, OrderModel::STATUS_PROCESSING])) {
                return;
            }

            $this->prepareMail($order, new OrderConfirmedNotification($order));
        } catch (\Exception $e) {
            report($e);
        }
    }
}
