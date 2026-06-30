<?php

namespace Webkul\CustomPromotions\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\CustomPromotions\DataGrids\RentalPromotionRuleDataGrid;
use Webkul\CustomPromotions\Http\Requests\RentalPromotionRuleRequest;
use Webkul\CustomPromotions\Repositories\RentalPromotionRuleRepository;

class RentalPromotionRuleController extends Controller
{
    public function __construct(protected RentalPromotionRuleRepository $ruleRepository) {}

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(RentalPromotionRuleDataGrid::class)->process();
        }

        return view('custom_promotions::admin.rental-rules.index');
    }

    public function create(): View
    {
        return view('custom_promotions::admin.rental-rules.create');
    }

    public function store(RentalPromotionRuleRequest $request): RedirectResponse
    {
        $this->ruleRepository->create($request->all());

        session()->flash('success', trans('custom_promotions::app.admin.rental-rules.create.create-success'));

        return redirect()->route('admin.custom_promotions.rental_rules.index');
    }

    public function edit(int $id): View
    {
        $rule = $this->ruleRepository->findOrFail($id);
        $coupon = $rule->coupon;

        return view('custom_promotions::admin.rental-rules.edit', compact('rule', 'coupon'));
    }

    public function update(RentalPromotionRuleRequest $request, int $id): RedirectResponse
    {
        $this->ruleRepository->update($request->all(), $id);

        session()->flash('success', trans('custom_promotions::app.admin.rental-rules.edit.update-success'));

        return redirect()->route('admin.custom_promotions.rental_rules.index');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->ruleRepository->findOrFail($id);

        $this->ruleRepository->delete($id);

        return new JsonResponse(['message' => trans('custom_promotions::app.admin.rental-rules.delete-success')]);
    }
}
