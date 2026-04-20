<?php

namespace Webkul\Customer\Repositories;

use Webkul\Core\Eloquent\Repository;

class CustomerGroupRepository extends Repository
{
    /**
     * Specify model class name.
     */
    public function model(): string
    {
        return 'Webkul\Customer\Contracts\CustomerGroup';
    }

    public function getConfigOptions(): array
    {
        return $this->all()->map(fn ($group) => [
            'title' => $group->name,
            'value' => $group->code,
        ])->toArray();
    }
}
