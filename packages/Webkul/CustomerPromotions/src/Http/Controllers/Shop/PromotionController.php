<?php

namespace Webkul\CustomerPromotions\Http\Controllers\Shop;

use Carbon\Carbon;
use Illuminate\View\View;
use Webkul\CartRule\Repositories\CartRuleRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Shop\Http\Controllers\Controller;

class PromotionController extends Controller
{
    public function __construct(
        protected CartRuleRepository $cartRuleRepository,
        protected CustomerRepository $customerRepository
    ) {}

    public function index(): View
    {
        $customerGroup = $this->customerRepository->getCurrentGroup();

        $promotions = $this->cartRuleRepository
            ->leftJoin('cart_rule_customer_groups', 'cart_rules.id', '=', 'cart_rule_customer_groups.cart_rule_id')
            ->leftJoin('cart_rule_channels', 'cart_rules.id', '=', 'cart_rule_channels.cart_rule_id')
            ->where('cart_rule_customer_groups.customer_group_id', $customerGroup->id)
            ->where('cart_rule_channels.channel_id', core()->getCurrentChannel()->id)
            ->where(function ($query) {
                $query->where('cart_rules.starts_from', '<=', Carbon::now()->format('Y-m-d H:i:s'))
                    ->orWhereNull('cart_rules.starts_from');
            })
            ->where(function ($query) {
                $query->where('cart_rules.ends_till', '>=', Carbon::now()->format('Y-m-d H:i:s'))
                    ->orWhereNull('cart_rules.ends_till');
            })
            ->where('status', 1)
            ->where('coupon_type', 1)
            ->with(['cart_rule_coupon'])
            ->select('cart_rules.*')
            ->distinct()
            ->orderBy('sort_order', 'asc')
            ->get()
            ->filter(fn ($rule) => $rule->cart_rule_coupon !== null);

        return view('customer_promotions::shop.customers.account.promotions.index', compact('promotions'));
    }
}
