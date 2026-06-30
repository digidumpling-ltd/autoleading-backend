<?php

namespace Webkul\Membership\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Membership\Repositories\TierRuleRepository;

class TierRuleController extends Controller
{
    public function __construct(
        protected TierRuleRepository $tierRuleRepository,
        protected CustomerGroupRepository $customerGroupRepository,
    ) {}

    public function index(): View
    {
        abort_if(! bouncer()->hasPermission('customers.membership'), 401);

        $tierRules = $this->tierRuleRepository->allSorted();

        $customerGroups = $this->customerGroupRepository->all(['id', 'name', 'code']);

        return view('membership::admin.tiers.index', compact('tierRules', 'customerGroups'));
    }

    public function store(Request $request): JsonResponse
    {
        abort_if(! bouncer()->hasPermission('customers.membership'), 401);

        $rules = $request->input('rules', []);

        $errors = $this->validate($rules);

        if (! empty($errors)) {
            return response()->json(['errors' => $errors], 422);
        }

        $this->tierRuleRepository->syncRules($rules);

        return response()->json([
            'message' => trans('bagisto-membership::app.admin.tiers.saved'),
            'data'    => $this->tierRuleRepository->allSorted()
                ->map(fn ($r) => [
                    'id'                => $r->id,
                    'min_balance'       => $r->min_balance,
                    'max_balance'       => $r->max_balance,
                    'customer_group_id' => $r->customer_group_id,
                    'background_color'  => $r->background_color,
                    'text_color'        => $r->text_color,
                    'sort_order'        => $r->sort_order,
                ]),
        ]);
    }

    private function validate(array $rules): array
    {
        $errors = [];
        $ranges = [];

        foreach ($rules as $i => $rule) {
            $min     = isset($rule['min_balance']) && $rule['min_balance'] !== '' ? (float) $rule['min_balance'] : null;
            $max     = isset($rule['max_balance']) && $rule['max_balance'] !== '' ? (float) $rule['max_balance'] : null;
            $groupId = isset($rule['customer_group_id']) ? (int) $rule['customer_group_id'] : null;
            $n       = $i + 1;

            if ($min === null || $min < 0) {
                $errors[] = trans('bagisto-membership::app.admin.tiers.validation.min-balance-required', ['n' => $n]);
            }

            if ($max !== null && $max < 0) {
                $errors[] = trans('bagisto-membership::app.admin.tiers.validation.max-balance-required', ['n' => $n]);
            }

            if ($min !== null && $max !== null && $min > $max) {
                $errors[] = trans('bagisto-membership::app.admin.tiers.validation.min-exceeds-max', ['n' => $n]);
            }

            if (! $groupId) {
                $errors[] = trans('bagisto-membership::app.admin.tiers.validation.group-required', ['n' => $n]);
            }

            if ($min !== null) {
                foreach ($ranges as $j => [$eMin, $eMax]) {
                    $overlaps = $max === null
                        ? ($eMax === null || $eMax >= $min)
                        : ($min <= ($eMax ?? PHP_INT_MAX) && $max >= $eMin);

                    if ($overlaps) {
                        $errors[] = trans('bagisto-membership::app.admin.tiers.validation.overlap', [
                            'a' => $j + 1,
                            'b' => $n,
                        ]);
                    }
                }

                $ranges[] = [$min, $max];
            }
        }

        return $errors;
    }
}
