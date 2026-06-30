<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

try {
    $walletRepo = app(Webkul\CustomPromotions\Repositories\WalletPromotionRuleRepository::class);
    echo 'WalletPromotionRuleRepository: OK' . PHP_EOL;
} catch (Exception $e) {
    echo 'WalletPromotionRuleRepository ERROR: ' . $e->getMessage() . PHP_EOL;
}

try {
    $rentalRepo = app(Webkul\CustomPromotions\Repositories\RentalPromotionRuleRepository::class);
    echo 'RentalPromotionRuleRepository: OK' . PHP_EOL;
} catch (Exception $e) {
    echo 'RentalPromotionRuleRepository ERROR: ' . $e->getMessage() . PHP_EOL;
}

try {
    $couponRepo = app(Webkul\CustomPromotions\Repositories\CustomPromotionCouponRepository::class);
    echo 'CustomPromotionCouponRepository: OK' . PHP_EOL;
} catch (Exception $e) {
    echo 'CustomPromotionCouponRepository ERROR: ' . $e->getMessage() . PHP_EOL;
}

try {
    $couponClass = Webkul\CustomPromotions\Models\CustomPromotionCouponProxy::modelClass();
    echo 'CustomPromotionCoupon proxy resolves to: ' . $couponClass . PHP_EOL;
} catch (Exception $e) {
    echo 'Proxy ERROR: ' . $e->getMessage() . PHP_EOL;
}

unlink(__FILE__);
