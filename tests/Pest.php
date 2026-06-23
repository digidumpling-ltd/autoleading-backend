<?php

use Webkul\Admin\Tests\AdminTestCase;
use Webkul\RentalPricing\Tests\RentalPricingTestCase;
use Webkul\Core\Tests\CoreTestCase;
use Webkul\CustomerPromotions\Tests\CustomerPromotionsTestCase;
use Webkul\Customer\Tests\CustomerTestCase;
use Webkul\DataGrid\Tests\DataGridTestCase;
use Webkul\EUWithdrawal\Tests\EUWithdrawalTestCase;
use Webkul\Installer\Tests\InstallerTestCase;
use Webkul\Payment\Tests\PaymentTestCase;
use Webkul\PayU\Tests\PayUTestCase;
use Webkul\Razorpay\Tests\RazorpayTestCase;
use Webkul\Shop\Tests\ShopTestCase;
use Webkul\Stripe\Tests\StripeTestCase;
use Webkul\Membership\Tests\MembershipTestCase;
use Webkul\Rewards\Tests\RewardsTestCase;
use Webkul\Wallet\Tests\WalletTestCase;
use Webkul\CustomPromotions\Tests\CustomPromotionsTestCase;
use Webkul\Yedpay\Tests\YedpayTestCase;

ini_set('memory_limit', '1024M');

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

uses(AdminTestCase::class)->in('../packages/Webkul/Admin/tests');
uses(CustomerPromotionsTestCase::class)->in('../packages/Webkul/CustomerPromotions/tests');
uses(CoreTestCase::class)->in('../packages/Webkul/Core/tests');
uses(CustomerTestCase::class)->in('../packages/Webkul/Customer/tests');
uses(DataGridTestCase::class)->in('../packages/Webkul/DataGrid/tests');
uses(EUWithdrawalTestCase::class)->in('../packages/Webkul/EUWithdrawal/tests');
uses(InstallerTestCase::class)->in('../packages/Webkul/Installer/tests');
uses(PaymentTestCase::class)->in('../packages/Webkul/Payment/tests');
uses(PayUTestCase::class)->in('../packages/Webkul/PayU/tests');
uses(RazorpayTestCase::class)->in('../packages/Webkul/Razorpay/tests');
uses(ShopTestCase::class)->in('../packages/Webkul/Shop/tests');
uses(StripeTestCase::class)->in('../packages/Webkul/Stripe/tests');
uses(RewardsTestCase::class)->in('../packages/Webkul/Rewards/tests');
uses(WalletTestCase::class)->in('../packages/Webkul/Wallet/tests');
uses(Tests\TestCase::class)->in('../packages/Webkul/MobilePass/tests');
uses(MembershipTestCase::class)->in('../packages/Webkul/Membership/tests');
uses(YedpayTestCase::class)->in('../packages/Webkul/Yedpay/tests');
uses(RentalPricingTestCase::class)->in('../packages/Webkul/RentalPricing/tests');
uses(CustomPromotionsTestCase::class)->in('../packages/Webkul/CustomPromotions/tests');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

function something()
{
    // ..
}
