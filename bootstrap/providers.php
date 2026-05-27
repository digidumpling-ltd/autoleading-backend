<?php

use App\Providers\AppServiceProvider;
use Webkul\Admin\Providers\AdminServiceProvider;
use Webkul\AutoLeadingTheme\Providers\AutoLeadingThemeServiceProvider;
use Webkul\Attribute\Providers\AttributeServiceProvider;
use Webkul\BookingProduct\Providers\BookingProductServiceProvider;
use Webkul\CartRule\Providers\CartRuleServiceProvider;
use Webkul\CatalogRule\Providers\CatalogRuleServiceProvider;
use Webkul\Category\Providers\CategoryServiceProvider;
use Webkul\Checkout\Providers\CheckoutServiceProvider;
use Webkul\CMS\Providers\CMSServiceProvider;
use Webkul\Core\Providers\CoreServiceProvider;
use Webkul\Core\Providers\EnvValidatorServiceProvider;
use Webkul\Customer\Providers\CustomerServiceProvider;
use Webkul\DataGrid\Providers\DataGridServiceProvider;
use Webkul\DataTransfer\Providers\DataTransferServiceProvider;
use Webkul\DebugBar\Providers\DebugBarServiceProvider;
use Webkul\FPC\Providers\FPCServiceProvider;
use Webkul\CustomerPromotions\Providers\CustomerPromotionsServiceProvider;
use Webkul\GDPR\Providers\GDPRServiceProvider;
use Webkul\ImageCache\Providers\ImageCacheServiceProvider;
use Webkul\Installer\Providers\InstallerServiceProvider;
use Webkul\Inventory\Providers\InventoryServiceProvider;
use Webkul\MagicAI\Providers\MagicAIServiceProvider;
use Webkul\Marketing\Providers\MarketingServiceProvider;
use Webkul\Notification\Providers\NotificationServiceProvider;
use Webkul\Payment\Providers\PaymentServiceProvider;
use Webkul\Paypal\Providers\PaypalServiceProvider;
use Webkul\PayU\Providers\PayUServiceProvider;
use Webkul\Product\Providers\ProductServiceProvider;
use Webkul\Razorpay\Providers\RazorpayServiceProvider;
use Webkul\RMA\Providers\RMAServiceProvider;
use Webkul\Rule\Providers\RuleServiceProvider;
use Webkul\Sales\Providers\SalesServiceProvider;
use Webkul\Shipping\Providers\ShippingServiceProvider;
use Webkul\Shop\Providers\ShopServiceProvider;
use Webkul\CustomerVerification\Providers\CustomerVerificationServiceProvider;
use Webkul\Sitemap\Providers\SitemapServiceProvider;
use Webkul\SocialLogin\Providers\SocialLoginServiceProvider;
use Webkul\SocialShare\Providers\SocialShareServiceProvider;
use Webkul\Stripe\Providers\StripeServiceProvider;
use Webkul\Yedpay\Providers\YedpayServiceProvider;
use Webkul\Tax\Providers\TaxServiceProvider;
use Webkul\Theme\Providers\ThemeServiceProvider;
use Webkul\User\Providers\UserServiceProvider;
use Webkul\Wallet\Providers\WalletServiceProvider;
use Webkul\OrderPriceOverride\Providers\ServiceProvider as OrderPriceOverrideServiceProvider;
use Webkul\RentalPricing\Providers\ServiceProvider as RentalPricingServiceProvider;
use Themes\CustomTheme\Providers\CustomThemeServiceProvider;
use Webbycrown\BlogBagisto\Providers\BlogServiceProvider;

return [
    /**
     * Application service providers.
     */
    AppServiceProvider::class,

    /**
     * Webkul's service providers.
     */
    AdminServiceProvider::class,
    AutoLeadingThemeServiceProvider::class,
    AttributeServiceProvider::class,
    BookingProductServiceProvider::class,
    CMSServiceProvider::class,
    CartRuleServiceProvider::class,
    CatalogRuleServiceProvider::class,
    CategoryServiceProvider::class,
    CheckoutServiceProvider::class,
    CoreServiceProvider::class,
    EnvValidatorServiceProvider::class,
    CustomerPromotionsServiceProvider::class,
    CustomerServiceProvider::class,
    DataGridServiceProvider::class,
    DataTransferServiceProvider::class,
    DebugBarServiceProvider::class,
    FPCServiceProvider::class,
    GDPRServiceProvider::class,
    ImageCacheServiceProvider::class,
    InstallerServiceProvider::class,
    InventoryServiceProvider::class,
    MagicAIServiceProvider::class,
    MarketingServiceProvider::class,
    NotificationServiceProvider::class,
    PayUServiceProvider::class,
    PaymentServiceProvider::class,
    PaypalServiceProvider::class,
    ProductServiceProvider::class,
    RMAServiceProvider::class,
    RazorpayServiceProvider::class,
    RuleServiceProvider::class,
    SalesServiceProvider::class,
    ShippingServiceProvider::class,
    ShopServiceProvider::class,
    CustomerVerificationServiceProvider::class,
    SitemapServiceProvider::class,
    SocialLoginServiceProvider::class,
    SocialShareServiceProvider::class,
    StripeServiceProvider::class,
    YedpayServiceProvider::class,
    TaxServiceProvider::class,
    ThemeServiceProvider::class,
    UserServiceProvider::class,
    WalletServiceProvider::class,
    OrderPriceOverrideServiceProvider::class,
    RentalPricingServiceProvider::class,
    CustomThemeServiceProvider::class,
    BlogServiceProvider::class,
];
