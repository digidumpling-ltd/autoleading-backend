<?php

namespace Webkul\CustomPromotions\Http\Controllers\Admin;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Webkul\Admin\Http\Controllers\Controller;
use Webkul\CustomPromotions\DataGrids\WalletPromotionRuleDataGrid;
use Webkul\CustomPromotions\Http\Requests\WalletPromotionRuleRequest;
use Webkul\CustomPromotions\Repositories\WalletPromotionRuleRepository;

class WalletPromotionRuleController extends Controller
{
    public function __construct(protected WalletPromotionRuleRepository $ruleRepository) {}

    public function index(): View|JsonResponse
    {
        if (request()->ajax()) {
            return datagrid(WalletPromotionRuleDataGrid::class)->process();
        }

        return view('custom_promotions::admin.wallet-rules.index');
    }

    public function create(): View
    {
        return view('custom_promotions::admin.wallet-rules.create');
    }

    public function store(WalletPromotionRuleRequest $request): RedirectResponse
    {
        $this->ruleRepository->create($request->all());

        session()->flash('success', trans('custom_promotions::app.admin.wallet-rules.create.create-success'));

        return redirect()->route('admin.custom_promotions.wallet_rules.index');
    }

    public function edit(int $id): View
    {
        $rule = $this->ruleRepository->findOrFail($id);
        $coupon = $rule->coupon;

        return view('custom_promotions::admin.wallet-rules.edit', compact('rule', 'coupon'));
    }

    public function update(WalletPromotionRuleRequest $request, int $id): RedirectResponse
    {
        $this->ruleRepository->update($request->all(), $id);

        session()->flash('success', trans('custom_promotions::app.admin.wallet-rules.edit.update-success'));

        return redirect()->route('admin.custom_promotions.wallet_rules.index');
    }

    public function destroy(int $id): JsonResponse
    {
        $this->ruleRepository->findOrFail($id);

        $this->ruleRepository->delete($id);

        return new JsonResponse(['message' => trans('custom_promotions::app.admin.wallet-rules.delete-success')]);
    }
}
