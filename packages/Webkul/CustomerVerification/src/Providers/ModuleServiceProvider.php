<?php

namespace Webkul\CustomerVerification\Providers;

use Webkul\Core\Providers\CoreModuleServiceProvider;
use Webkul\CustomerVerification\Models\CustomerVerificationDocument;

class ModuleServiceProvider extends CoreModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [
        CustomerVerificationDocument::class,
    ];
}