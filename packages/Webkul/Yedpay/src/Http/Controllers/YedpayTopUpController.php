<?php

namespace Webkul\Yedpay\Http\Controllers;

use Exception;
use Illuminate\Http\RedirectResponse;
use Webkul\Shop\Http\Controllers\Controller;
use Webkul\Wallet\Models\Channel as WalletChannel;
use Webkul\Wallet\Models\Customer as WalletCustomer;
use Webkul\Wallet\Models\WalletTopUp;
use Webkul\Yedpay\Payment\Yedpay;
use Webkul\Yedpay\Services\YedpayService;

class YedpayTopUpController extends Controller
{
    public function __construct(
        protected Yedpay $yedpay,
    ) {}

    public function redirect(): RedirectResponse
    {
        if (! $this->yedpay->hasValidCredentials()) {
            session()->flash('error', trans('yedpay::app.response.provide-credentials'));

            return $this->failureRedirect();
        }

        $topUp = $this->resolveTopUp();

        if (! $topUp) {
            session()->flash('error', trans('yedpay::app.response.topup-not-found'));

            return $this->failureRedirect();
        }

        try {
            $reference = 'topup-' . $topUp->id . '-' . time();

            $topUp->update(['reference' => $reference]);

            session(['yedpay_topup_reference' => $reference]);

            $service = $this->makeService();

            $paymentUrl = $service->createPayment(
                amount: $topUp->amount,
                customId: $reference,
                returnUrl: route('yedpay.topup.success'),
                notifyUrl: route('yedpay.topup.notify'),
            );

            return redirect($paymentUrl);
        } catch (Exception $e) {
            $topUp->update(['status' => WalletTopUp::STATUS_FAILED]);

            session()->flash('error', trans('yedpay::app.response.payment-failed') . ': ' . $e->getMessage());

            return $this->failureRedirect();
        }
    }

    public function success(): RedirectResponse
    {
        if (! $this->yedpay->hasValidCredentials()) {
            session()->flash('error', trans('yedpay::app.response.provide-credentials'));

            return $this->failureRedirect();
        }

        $data = request()->all();

        try {
            $service = $this->makeService();

            if (! $service->verifyCallback($data)) {
                session()->flash('error', trans('yedpay::app.response.verification-failed'));

                return $this->failureRedirect();
            }

            if (! $service->isPaymentPaid($data)) {
                $topUp = $this->resolveTopUp();

                if ($topUp && $topUp->status === WalletTopUp::STATUS_PENDING) {
                    $topUp->update(['status' => WalletTopUp::STATUS_FAILED, 'metadata' => $data]);
                }

                session()->forget(['wallet_topup_id', 'yedpay_topup_reference']);
                session()->flash('error', trans('yedpay::app.response.payment-failed'));

                return $this->failureRedirect();
            }

            $topUp = $this->resolveTopUp();

            if (! $topUp) {
                session()->flash('error', trans('yedpay::app.response.topup-not-found'));

                return $this->failureRedirect();
            }

            if ($topUp->status === WalletTopUp::STATUS_COMPLETED) {
                session()->flash('info', trans('bagisto-wallet::app.customers.account.wallet.topup-already-completed'));

                return redirect()->route('shop.customers.account.wallet.index');
            }

            $topUp->update([
                'status'         => WalletTopUp::STATUS_COMPLETED,
                'transaction_id' => $data['transaction_id'] ?? null,
                'metadata'       => $data,
            ]);

            $customer = WalletCustomer::find($topUp->customer_id);
            $channel  = WalletChannel::find(core()->getCurrentChannel()->id);

            $oldBalance = $customer->balanceFloatNum;

            $channel->forceTransferFloat($customer, $topUp->amount, [
                'type'        => 'wallet_topup',
                'topup_id'    => $topUp->id,
                'description' => trans('bagisto-wallet::app.listeners.wallet-topup.description', ['order' => $topUp->id]),
            ]);

            // A gateway top-up credits the wallet but does not otherwise announce
            // it. Dispatch WalletBalanceUpdated (as the admin/account top-up paths
            // do) so downstream listeners run — notably the wallet-pass sync that
            // keeps the Apple/Google pass balance in step. Gated by the same
            // publish flag the other paths use.
            if (core()->getConfigData('sales.wallet.events.publish_balance_updated')) {
                \Illuminate\Support\Facades\Event::dispatch(new \Webkul\Wallet\Events\WalletBalanceUpdated(
                    customerId: $customer->id,
                    oldBalance: $oldBalance,
                    newBalance: $customer->fresh()->balanceFloatNum,
                    reason: 'wallet_topup',
                    customerGroupId: $customer->customer_group_id,
                ));
            }

            session()->forget(['wallet_topup_id', 'yedpay_topup_reference']);

            session()->flash('success', trans('bagisto-wallet::app.customers.account.wallet.topup-success'));

            return redirect()->route('shop.customers.account.wallet.index');
        } catch (Exception $e) {
            session()->flash('error', trans('yedpay::app.response.verification-failed') . ': ' . $e->getMessage());

            return $this->failureRedirect();
        }
    }

    public function cancel(): RedirectResponse
    {
        $topUp = $this->resolveTopUp();

        if ($topUp && $topUp->status === WalletTopUp::STATUS_PENDING) {
            $topUp->update(['status' => WalletTopUp::STATUS_CANCELLED]);
        }

        session()->forget(['wallet_topup_id', 'yedpay_topup_reference']);

        session()->flash('error', trans('yedpay::app.response.payment-cancelled'));

        return $this->failureRedirect();
    }

    public function notify()
    {
        return response('OK', 200);
    }

    protected function makeService(): YedpayService
    {
        return app(YedpayService::class);
    }

    protected function resolveTopUp(): ?WalletTopUp
    {
        $id = session('wallet_topup_id');

        return $id ? WalletTopUp::find($id) : null;
    }

    protected function failureRedirect(): RedirectResponse
    {
        return redirect()->route('shop.customers.account.wallet.topup');
    }
}
