<?php
// Usage: TEST_LOCALE=zh_CN ddev exec php artisan tinker test-order-email.php
$locale = getenv('TEST_LOCALE') ?: 'en';

$order = \Webkul\Sales\Models\Order::latest()->first();
echo 'Order: ' . $order->increment_id . PHP_EOL;
echo 'To: ' . $order->customer_email . PHP_EOL;
echo 'Locale: ' . $locale . PHP_EOL;

app()->setLocale($locale);

$mailable = new \Themes\CustomTheme\Mail\Order\OrderConfirmedNotification($order);
echo 'Subject: ' . $mailable->envelope()->subject . PHP_EOL;

\Illuminate\Support\Facades\Mail::to($order->customer_email)->queue($mailable);

echo 'Done — check Mailpit at https://bagisto.ddev.site:8026' . PHP_EOL;
