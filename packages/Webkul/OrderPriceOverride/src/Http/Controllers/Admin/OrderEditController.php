<?php

namespace Webkul\OrderPriceOverride\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Sales\Repositories\OrderItemRepository;
use Webkul\Sales\Repositories\OrderRepository;

class OrderEditController extends Controller
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected OrderItemRepository $orderItemRepository,
    ) {}

    public function store(int $id): RedirectResponse
    {
        if (! bouncer()->hasPermission('sales.orders.edit')) {
            abort(403);
        }

        $order = $this->orderRepository->findOrFail($id);

        if ($order->status !== 'pending' || $order->invoices->count() > 0) {
            session()->flash('error', trans('order-price-override::app.admin.sales.orders.edit.not-pending'));

            return redirect()->back();
        }

        $overrides = request()->input('override_total', []);

        if (empty($overrides)) {
            return redirect()->back();
        }

        $adminId = Auth::guard('admin')->id();
        $now = now()->toISOString();

        foreach ($overrides as $itemId => $overrideTotal) {
            $overrideTotal = (float) $overrideTotal;

            if ($overrideTotal <= 0) {
                continue;
            }

            $item = $this->orderItemRepository->find($itemId);

            if (! $item || $item->order_id !== $order->id) {
                continue;
            }

            $qtyOrdered = max($item->qty_ordered, 1);
            $unitPrice = $overrideTotal / $qtyOrdered;

            $additional = $item->additional ?? [];
            $additional['price_override'] = [
                'original_price'   => $item->price,
                'original_total'   => $item->total,
                'override_total'   => $overrideTotal,
                'admin_id'         => $adminId,
                'edited_at'        => $now,
            ];

            $this->orderItemRepository->update([
                'price'            => $unitPrice,
                'base_price'       => $unitPrice,
                'total'            => $overrideTotal,
                'base_total'       => $overrideTotal,
                'additional'       => $additional,
            ], $itemId);
        }

        $order->refresh();
        $items = $order->items;

        $subTotal     = $items->sum('total');
        $baseSubTotal = $items->sum('base_total');
        $grandTotal   = $subTotal
            + (float) $order->shipping_amount
            + (float) $order->tax_amount
            - (float) $order->discount_amount;
        $baseGrandTotal = $baseSubTotal
            + (float) $order->base_shipping_amount
            + (float) $order->base_tax_amount
            - (float) $order->base_discount_amount;

        $this->orderRepository->update([
            'sub_total'         => $subTotal,
            'base_sub_total'    => $baseSubTotal,
            'grand_total'       => $grandTotal,
            'base_grand_total'  => $baseGrandTotal,
        ], $order->id);

        session()->flash('success', trans('order-price-override::app.admin.sales.orders.edit.success'));

        return redirect()->back();
    }
}
