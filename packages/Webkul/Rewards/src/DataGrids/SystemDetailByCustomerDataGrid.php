<?php

namespace Webkul\Rewards\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;
use Webkul\Rewards\Models\RewardPoint;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Sales\Repositories\OrderRepository;

class SystemDetailByCustomerDataGrid extends DataGrid
{
    /**
     * Create a new datagrid instance.
     *
     * @return \Illuminate\Database\Query\Builder
     */
    public function __construct(
        protected CustomerRepository $customerRepository,
        protected OrderRepository $orderRepository
    ) {
    }

    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $customer = $this->customerRepository->find(request('id'));

        $p = DB::getTablePrefix();

        $queryBuilder = DB::table('reward_points')
            ->addSelect(
                'orders.id',
                'reward_points.order_id',
                'reward_points.category_id',
                'reward_points.attribute_id',
                'reward_points.product_id',
                'reward_points.status',
                'reward_points.exp_date',
                'reward_points.created_at',
                'reward_points.reward_points',
                'reward_points.note',
                'reward_points.status',
                'reward_points.creator_type',
                DB::raw("{$p}admins.name as creator_admin_name"),
            )
            ->leftJoin('orders', 'reward_points.order_id', '=', 'orders.id')
            ->leftJoin('admins', function ($join) {
                $join->on('admins.id', '=', 'reward_points.creator_id')
                    ->where('reward_points.creator_type', '=', 'admin');
            })
            ->where('reward_points.customer_id', $customer->id);

        $this->addFilter('creator_type', 'reward_points.creator_type');

        return $queryBuilder;
    }

    /**
     * Prepare columns.
     *
     * @return void
     */
    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'reward_points',
            'label'      => trans('rewards::app.admin.rewards.system.view.datagrid.reward-points'),
            'type'       => 'integer',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'created_at',
            'label'      => trans('rewards::app.admin.rewards.system.view.datagrid.created-at'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                $data = [];

                if ($row->order_id) {
                    $data[] = '<a  href="'.route('admin.sales.orders.view', $this->orderRepository->find($row->order_id)->increment_id).'" class="text-blue-600">'.trans('rewards::app.admin.rewards.system.view.datagrid.order-id').'= #'.$this->orderRepository->find($row->order_id)->increment_id.'</a> <br><br>';
                }

                if ($row->product_id) {
                    $data[] = '<span>'.trans('rewards::app.admin.rewards.system.view.datagrid.product-id').'='.$row->product_id.'</span> <br><br>';
                }

                if ($row->category_id) {
                    $data[] = '<span>'.trans('rewards::app.admin.rewards.system.view.datagrid.category-id').'='.$row->category_id.'</span> <br><br>';
                }

                if ($row->attribute_id) {
                    $data[] = '<span>'.trans('rewards::app.admin.rewards.system.view.datagrid.attribute-id').'='.$row->attribute_id.'</span> <br><br>';
                }

                $data[] = $row->created_at;

                return implode(' ', $data);
            },
        ]);

        $this->addColumn([
            'index'      => 'exp_date',
            'label'      => trans('rewards::app.admin.rewards.system.view.datagrid.exp-date'),
            'type'       => 'integer',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'note',
            'label'      => trans('rewards::app.admin.rewards.system.view.datagrid.note'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('rewards::app.admin.rewards.system.view.datagrid.status'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'closure'    => function ($row) {
                switch ($row->status) {
                    case RewardPoint::STATUS_PROCESSING:
                        return '<span class="label-active">'.trans('rewards::app.admin.rewards.system.view.datagrid.processing').'</span>';
                    case RewardPoint::STATUS_APPROVED:
                        return '<span class="label-active">'.trans('rewards::app.admin.rewards.system.view.datagrid.approved').'</span>';
                    case RewardPoint::STATUS_CANCELED:
                        return '<span class="label-cancelled">'.trans('rewards::app.admin.rewards.system.view.datagrid.canceled').'</span>';
                    case RewardPoint::STATUS_CLOSED:
                        return '<span class="label-closed">'.trans('rewards::app.admin.rewards.system.view.datagrid.closed').'</span>';
                    case RewardPoint::STATUS_PENDING:
                        return '<span class="label-pending">'.trans('rewards::app.admin.rewards.system.view.datagrid.pending').'</span>';
                    case RewardPoint::STATUS_FRAUD:
                        return '<span class="label-cancelled">'.trans('rewards::app.admin.rewards.system.view.datagrid.fraud').'</span>';
                    case RewardPoint::STATUS_EXPIRE:
                        return '<span class="label-cancelled">'.trans('rewards::app.admin.rewards.system.view.datagrid.expire').'</span>';
                    case RewardPoint::STATUS_USED:
                        return '<span class="label-active">'.trans('rewards::app.admin.rewards.system.view.datagrid.used').'</span>';
                }
            },

            'filterable' => true,
        ]);

        $this->addColumn([
            'index'              => 'creator_type',
            'label'              => trans('rewards::app.admin.rewards.system.view.datagrid.created-by'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => false,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => trans('rewards::app.admin.rewards.system.view.datagrid.creator-system'),   'value' => 'system'],
                ['label' => trans('rewards::app.admin.rewards.system.view.datagrid.creator-admin'),    'value' => 'admin'],
                ['label' => trans('rewards::app.admin.rewards.system.view.datagrid.creator-customer'), 'value' => 'customer'],
            ],
            'closure'    => function ($row) {
                return match ($row->creator_type) {
                    'admin'    => $row->creator_admin_name ?? trans('rewards::app.admin.rewards.system.view.datagrid.creator-admin'),
                    'customer' => trans('rewards::app.admin.rewards.system.view.datagrid.creator-customer'),
                    'system'   => trans('rewards::app.admin.rewards.system.view.datagrid.creator-system'),
                    default    => '—',
                };
            },
        ]);
    }
}
