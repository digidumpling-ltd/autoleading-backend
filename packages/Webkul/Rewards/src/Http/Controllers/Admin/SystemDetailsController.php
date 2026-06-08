<?php

namespace Webkul\Rewards\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Mail;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Rewards\DataGrids\SystemDetailByCustomerDataGrid;
use Webkul\Rewards\DataGrids\SystemDetailRewardPoints;
use Webkul\Rewards\Mail\RewardApproved;
use Webkul\Rewards\Models\RewardPoint;
use Webkul\Rewards\Repositories\RewardPointRepository;

class SystemDetailsController extends Controller
{
    /**
     * Create a new datagrid instance.
     * 
     * @return void
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected RewardPointRepository $rewardPointRepository,
    ) {
    }

    /* Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        if (request()->ajax()) {
            return app(SystemDetailRewardPoints::class)->toJson();
        }
    
        return view('rewards::admin.rewards.system.index');
    }

    /**
     * Show the form for creating a new resource.
     * 
     * @param int $id
     * @return \Illuminate\View\View
     */
    public function view($id)
    {
        if (request()->ajax()) {
            return app(SystemDetailByCustomerDataGrid::class)->toJson();
        }

        $customer = $this->customerRepository->find(request('id'));

        return view('rewards::admin.rewards.system.view', compact('customer'));
    }

    /**
     * Allocate reward points to a customer.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function allocate($id): JsonResponse
    {
        $customer = $this->customerRepository->findOrFail($id);

        request()->validate([
            'points' => 'required|integer|min:1',
            'reason' => 'required|string|max:500',
        ]);

        $this->rewardPointRepository->allocateByAdmin(
            $customer->id,
            (int) request('points'),
            request('reason')
        );

        if (request('notify_customer')) {
            Mail::queue(new RewardApproved([
                'email'               => $customer->email,
                'name'                => $customer->first_name . ' ' . $customer->last_name,
                'order_id'            => null,
                'points'              => (int) request('points'),
                'note'                => request('reason'),
                'used_reward_points'  => RewardPoint::where('customer_id', $customer->id)->where('status', 'used')->sum('reward_points'),
                'total_reward_points' => $this->rewardPointRepository->totalRewardPoints($customer->id),
            ]));
        }

        return new JsonResponse(['message' => trans('rewards::app.admin.rewards.system.view.allocate-success')]);
    }

    /**
     * Return the calculated reward point balance for a customer.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function balance($id): JsonResponse
    {
        $customer = $this->customerRepository->findOrFail($id);

        return new JsonResponse([
            'balance' => $this->rewardPointRepository->totalRewardPoints($customer->id),
        ]);
    }

    /**
     * Show the customer birthday reward.
     *
     * @return void
     */
    public function setRewardsOnCustomerBirthday()
    {
       return $this->rewardPointRepository->setRewardsOnCustomerBirthday();
    }
}