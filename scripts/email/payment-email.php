<?php
// Tests the invoiced email override (shop::emails.orders.invoiced → CustomTheme)
// TEST_LOCALE=zh_CN ddev exec php artisan tinker test-payment-email.php
$invoice = \Webkul\Sales\Models\Invoice::latest()->first();

if ($testLocale = getenv('TEST_LOCALE')) {
    $invoice->order->items->first()->additional = array_merge(
        $invoice->order->items->first()->additional ?? [],
        ['locale' => $testLocale]
    );
}

$locale = $invoice->order->items->first()->additional['locale'] ?? 'en';
echo 'Invoice: ' . $invoice->increment_id . PHP_EOL;
echo 'To: ' . $invoice->order->customer_email . PHP_EOL;
echo 'Locale: ' . $locale . PHP_EOL;

app()->setLocale($locale);

$mailable = new \Webkul\Shop\Mail\Order\InvoicedNotification($invoice);
\Illuminate\Support\Facades\Mail::to($invoice->order->customer_email)->sendNow($mailable);

echo 'Done — check Mailpit at https://bagisto.ddev.site:8026' . PHP_EOL;
