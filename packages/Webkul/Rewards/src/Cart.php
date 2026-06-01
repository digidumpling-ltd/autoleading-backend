<?php

namespace Webkul\Rewards;

use Webkul\Checkout\Cart as BaseCart;
use Webkul\Checkout\Repositories\CartAddressRepository;
use Webkul\Checkout\Repositories\CartItemRepository;
use Webkul\Checkout\Repositories\CartRepository;
use Webkul\Customer\Repositories\CustomerAddressRepository;
use Webkul\Customer\Repositories\WishlistRepository;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Rewards\Helpers\CartHelper as RewardCartHelper;
use Webkul\Rewards\Models\Cart as CartModel;
use Webkul\Tax\Repositories\TaxCategoryRepository;

class Cart extends BaseCart
{
    /**
     * Create a new class instance.
     *
     * @return void
     */
    public function __construct(
        protected CartAddressRepository $cartAddressRepository,
        protected CartItemRepository $cartItemRepository,
        protected CartRepository $cartRepository,
        protected CustomerAddressRepository $customerAddressRepository,
        protected ProductRepository $productRepository,
        protected RewardCartHelper $rewardCartHelper,
        protected TaxCategoryRepository $taxCategoryRepository,
        protected WishlistRepository $wishlistRepository,
    ) {
        $this->initCart();
    }

    /**
     * Updates cart totals, delegating to base then applying points deduction.
     */
    public function collectTotals(): self
    {
        parent::collectTotals();

        /** @var CartModel|null $cart */
        $cart = $this->getCart();

        if (! $cart || ! core()->getConfigData('reward.general.general.module-status')) {
            return $this;
        }

        if (! empty($cart->points)) {
            $redemptions = $this->rewardCartHelper->redemption($cart->points);

            if ($redemptions && $cart->base_grand_total > 0) {
                $cart->grand_total      = max(0, round($cart->grand_total - $redemptions, 2));
                $cart->base_grand_total = max(0, round($cart->base_grand_total - $redemptions, 2));
                $cart->save();
            }
        }

        return $this;
    }

    /**
     * Set points to the cart.
     */
    public function setPoints(int|string $points): static
    {
        /** @var CartModel $cart */
        $cart = $this->getCart();

        $cart->points = $points;

        $cart->save();

        return $this;
    }

    /**
     * Remove points from cart.
     */
    public function removePoints(): static
    {
        /** @var CartModel $cart */
        $cart = $this->getCart();

        $cart->points = null;

        $cart->save();

        return $this;
    }
}