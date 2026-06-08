<?php

namespace Webkul\Rewards\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Webkul\Admin\Http\Requests\MassDestroyRequest;
use Webkul\Admin\Http\Requests\MassUpdateRequest;
use Webkul\Customer\Repositories\CustomerGroupRepository;
use Webkul\Rewards\DataGrids\WalletTopupRewardRuleDataGrid;
use Webkul\Rewards\Repositories\WalletTopupRewardRuleRepository;

class WalletTopupRewardRuleController extends Controller
{
    public function __construct(
        protected WalletTopupRewardRuleRepository $ruleRepository,
        protected CustomerGroupRepository $customerGroupRepository,
    ) {
    }

    public function index()
    {
        if (request()->ajax()) {
            return app(WalletTopupRewardRuleDataGrid::class)->toJson();
        }

        return view('rewards::admin.rewards.wallet-topup.index');
    }

    public function create()
    {
        $customerGroups = $this->customerGroupRepository->all();

        return view('rewards::admin.rewards.wallet-topup.create', compact('customerGroups'));
    }

    public function store()
    {
        request()->validate([
            'trigger'    => 'required|in:wallet_topup,wallet_spend',
            'mode'       => 'required|in:fixed,percent',
            'value'      => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'priority'   => 'nullable|integer|min:0',
            'status'     => 'required|boolean',
        ]);

        $data = request()->only([
            'customer_group_id',
            'trigger',
            'mode',
            'value',
            'min_amount',
            'max_amount',
            'priority',
            'status',
        ]);

        $data['customer_group_id'] = $data['customer_group_id'] ?: null;

        $this->ruleRepository->create($data);

        session()->flash('success', trans('rewards::app.admin.rewards.wallet-topup.index.create-success'));

        return redirect()->route('admin.reward.wallet-topup.index');
    }

    public function edit(int $id)
    {
        $rule = $this->ruleRepository->findOrFail($id);

        $customerGroups = $this->customerGroupRepository->all();

        return view('rewards::admin.rewards.wallet-topup.edit', compact('rule', 'customerGroups'));
    }

    public function update(int $id)
    {
        request()->validate([
            'trigger'    => 'required|in:wallet_topup,wallet_spend',
            'mode'       => 'required|in:fixed,percent',
            'value'      => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0|gte:min_amount',
            'priority'   => 'nullable|integer|min:0',
            'status'     => 'required|boolean',
        ]);

        $data = request()->only([
            'customer_group_id',
            'trigger',
            'mode',
            'value',
            'min_amount',
            'max_amount',
            'priority',
            'status',
        ]);

        $data['customer_group_id'] = $data['customer_group_id'] ?: null;

        $this->ruleRepository->update($data, $id);

        session()->flash('success', trans('rewards::app.admin.rewards.wallet-topup.index.update-success'));

        return redirect()->route('admin.reward.wallet-topup.index');
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->ruleRepository->delete($id);

            return new JsonResponse(['message' => trans('rewards::app.admin.rewards.wallet-topup.index.delete-success')]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => trans('rewards::app.admin.rewards.wallet-topup.index.delete-failed')], 500);
        }
    }

    public function massDestroy(MassDestroyRequest $massDestroyRequest): JsonResponse
    {
        $ids = $massDestroyRequest->input('indices');

        try {
            $this->ruleRepository->whereIn('id', $ids)->delete();

            return new JsonResponse([
                'message' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.mass-delete-success'),
            ]);
        } catch (\Exception $e) {
            return new JsonResponse(['message' => $e->getMessage()], 500);
        }
    }

    public function massUpdate(MassUpdateRequest $massUpdateRequest): JsonResponse
    {
        $ids = $massUpdateRequest->input('indices');

        foreach ($ids as $id) {
            $this->ruleRepository->update(['status' => $massUpdateRequest->input('value')], $id);
        }

        return new JsonResponse([
            'message' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.mass-update-success'),
        ]);
    }
}
