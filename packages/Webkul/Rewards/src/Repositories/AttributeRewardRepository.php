<?php

namespace Webkul\Rewards\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Rewards\Contracts\AttributeReward;

class AttributeRewardRepository extends Repository
{   
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return AttributeReward::class;
    }
}