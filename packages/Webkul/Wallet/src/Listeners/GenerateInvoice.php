<?php

namespace Webkul\Wallet\Listeners;

use Webkul\Sales\Repositories\InvoiceRepository;
use Webkul\Sales\Repositories\OrderRepository;

class GenerateInvoice
{
    public function __construct(
        protected OrderRepository $orderRepository,
        protected InvoiceRepository $invoiceRepository
    ) {}

    public function handle($order): void
    {
        if ($order->payment->method !== 'wallet') {
            return;
        }

        if (! core()->getConfigData('sales.payment_methods.wallet.generate_invoice')) {
            return;
        }

        $this->invoiceRepository->create(
            $this->prepareInvoiceData($order),
            core()->getConfigData('sales.payment_methods.wallet.invoice_status'),
            core()->getConfigData('sales.payment_methods.wallet.order_status')
        );
    }

    protected function prepareInvoiceData($order): array
    {
        $invoiceData = ['order_id' => $order->id];

        foreach ($order->items as $item) {
            $invoiceData['invoice']['items'][$item->id] = $item->qty_to_invoice;
        }

        return $invoiceData;
    }
}
