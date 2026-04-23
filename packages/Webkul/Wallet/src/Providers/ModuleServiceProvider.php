<?php

namespace Webkul\Wallet\Providers;

use Konekt\Concord\BaseModuleServiceProvider;

class ModuleServiceProvider extends BaseModuleServiceProvider
{
    /**
     * Models.
     *
     * @var array
     */
    protected $models = [];

    public function boot(): void
    {
        parent::boot();

        $this->app->concord->registerModel(
            \Webkul\Customer\Contracts\Customer::class,
            \Webkul\Wallet\Models\Customer::class
        );
    }
}
