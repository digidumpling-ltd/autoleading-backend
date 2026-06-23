<?php

namespace Webkul\CustomPromotions\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Webkul\CustomPromotions\Models\RentalPromotionRule;
use Webkul\CustomPromotions\Models\WalletPromotionRule;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    protected $models = [
        WalletPromotionRule::class,
        RentalPromotionRule::class,
    ];
}
