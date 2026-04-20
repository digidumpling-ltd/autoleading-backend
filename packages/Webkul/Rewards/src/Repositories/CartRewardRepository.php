<?php

namespace Webkul\Rewards\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Rewards\Contracts\CartReward;

class CartRewardRepository extends Repository
{
    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return CartReward::class;
    }

    /**
     * Find cart by amount
     * 
     * @param integer $amount
     * @return \Webkul\Rewards\Contracts\CartReward
     */
    public function findCartByAmount($amount)
    {
        return $this->model->where('amount_from', '<=', $amount)
            ->where('amount_to', '>=', $amount)
            ->where('status', '1')
            ->first();  
    }
}