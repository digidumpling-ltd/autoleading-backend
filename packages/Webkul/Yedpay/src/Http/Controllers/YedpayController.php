<?php

namespace Webkul\Yedpay\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Webkul\Checkout\Facades\Cart;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;
use Webkul\Sales\Repositories\OrderTransactionRepository;
use Webkul\Sales\Transformers\OrderResource;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Yedpay\Payment\Yedpay;
use Webkul\Yedpay\Services\YedpayService;

class YedpayController extends Controller
{
    public function __construct(
        protected CartRepository $cartRepository,
        protected OrderRepository $orderRepository,
        protected OrderTransactionRepository $orderTransactionRepository,
        protected InvoiceRepository $invoiceRepository,
        protected Yedpay $yedpay,
    ) {}

    public function redirect(): RedirectResponse
    {
        if (! $this->yedpay->hasValidCredentials()) {
            session()->flash('error', trans('yedpay::app.response.provide-credentials'));

            return redirect()->route('shop.checkout.cart.index');
        }

        $cart = Cart::getCart();

        if (! $cart) {
            session()->flash('error', trans('yedpay::app.response.cart-not-found'));

            return redirect()->route('shop.checkout.cart.index');
        }

        try {
            $customId = 'bagisto-' . $cart->id . '-' . time();

            session(['yedpay_custom_id' => $customId, 'yedpay_cart_id' => $cart->id]);

            $service = $this->makeService();

            $paymentUrl = $service->createPayment(
                amount: (float) $cart->base_grand_total,
                customId: $customId,
                returnUrl: route('yedpay.payment.success'),
                notifyUrl: route('yedpay.payment.notify'),
            );

            return redirect($paymentUrl);
        } catch (Exception $e) {
            session()->flash('error', trans('yedpay::app.response.payment-failed') . ': ' . $e->getMessage());

            return redirect()->route('shop.checkout.cart.index');
        }
    }

    public function success(): RedirectResponse
    {
        $data = request()->all();

        try {
            $service = $this->makeService();

            if (! $service->verifyCallback($data)) {
                session()->flash('error', trans('yedpay::app.response.verification-failed'));

                return redirect()->route('shop.checkout.cart.index');
            }

            $cartId = session('yedpay_cart_id');
            $customId = session('yedpay_custom_id');

            if (! $cartId) {
                session()->flash('error', trans('yedpay::app.response.cart-not-found'));

                return redirect()->route('shop.checkout.cart.index');
            }

            $cart = $this->cartRepository->find($cartId);

            if (! $cart || ! $cart->is_active) {
                session()->flash('error', trans('yedpay::app.response.cart-processed'));

                return redirect()->route('shop.checkout.cart.index');
            }

            Cart::setCart($cart);
            Cart::collectTotals();

            $orderData = (new OrderResource($cart))->jsonSerialize();

            $orderData['payment']['additional'] = [
                'yedpay_custom_id'      => $customId,
                'yedpay_transaction_id' => $data['transaction_id'] ?? null,
                'yedpay_status'         => $data['status'] ?? null,
            ];

            $order = $this->orderRepository->create($orderData);

            $this->orderRepository->update(['status' => 'processing'], $order->id);

            if ($order->canInvoice()) {
                $invoiceData = ['order_id' => $order->id];

                foreach ($order->items as $item) {
                    $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
                }

                $invoice = $this->invoiceRepository->create($invoiceData);

                $this->orderTransactionRepository->create([
                    'transaction_id' => $data['transaction_id'] ?? $customId,
                    'status'         => 'paid',
                    'type'           => 'capture',
                    'payment_method' => 'yedpay',
                    'order_id'       => $order->id,
                    'invoice_id'     => $invoice->id,
                    'amount'         => $order->base_grand_total,
                    'data'           => json_encode($data),
                ]);
            }

            Cart::deActivateCart();

            session()->forget(['yedpay_custom_id', 'yedpay_cart_id']);

            session()->flash('order_id', $order->id);
            session()->flash('success', trans('yedpay::app.response.payment-success'));

            return redirect()->route('shop.checkout.onepage.success');
        } catch (Exception $e) {
            session()->flash('error', trans('yedpay::app.response.verification-failed') . ': ' . $e->getMessage());

            return redirect()->route('shop.checkout.cart.index');
        }
    }

    public function cancel(): RedirectResponse
    {
        session()->forget(['yedpay_custom_id', 'yedpay_cart_id']);

        session()->flash('error', trans('yedpay::app.response.payment-cancelled'));

        return redirect()->route('shop.checkout.cart.index');
    }

    /**
     * Yedpay async notify endpoint — acknowledge without processing (order already handled in success()).
     */
    public function notify()
    {
        return response('OK', 200);
    }

    protected function makeService(): YedpayService
    {
        return app(YedpayService::class);
    }
}
