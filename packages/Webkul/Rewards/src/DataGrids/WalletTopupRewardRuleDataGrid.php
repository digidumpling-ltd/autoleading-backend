<?php

namespace Webkul\Rewards\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class WalletTopupRewardRuleDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $queryBuilder = DB::table('wallet_topup_reward_rules')
            ->leftJoin('customer_groups', 'wallet_topup_reward_rules.customer_group_id', '=', 'customer_groups.id')
            ->select(
                'wallet_topup_reward_rules.id',
                'customer_groups.name as customer_group_name',
                'wallet_topup_reward_rules.trigger',
                'wallet_topup_reward_rules.mode',
                'wallet_topup_reward_rules.value',
                'wallet_topup_reward_rules.min_amount',
                'wallet_topup_reward_rules.max_amount',
                'wallet_topup_reward_rules.priority',
                'wallet_topup_reward_rules.status',
            );

        $this->addFilter('id', 'wallet_topup_reward_rules.id');
        $this->addFilter('status', 'wallet_topup_reward_rules.status');
        $this->addFilter('mode', 'wallet_topup_reward_rules.mode');
        $this->addFilter('trigger', 'wallet_topup_reward_rules.trigger');

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'id',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.id'),
            'type'       => 'integer',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
        ]);

        $this->addColumn([
            'index'      => 'customer_group_name',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.customer-group'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'trigger',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.trigger'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return match ($row->trigger) {
                    'wallet_topup' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.trigger-wallet-topup'),
                    'wallet_spend' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.trigger-wallet-spend'),
                    default        => $row->trigger,
                };
            },
        ]);

        $this->addColumn([
            'index'      => 'min_amount',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.min-amount'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'max_amount',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.max-amount'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'mode',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.mode'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => true,
            'closure'    => function ($row) {
                return ucfirst($row->mode);
            },
        ]);

        $this->addColumn([
            'index'      => 'value',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.value'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'priority',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.priority'),
            'type'       => 'integer',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'      => 'status',
            'label'      => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.status'),
            'type'       => 'boolean',
            'options'    => [
                'type'   => 'basic',
                'params' => [
                    'options' => [
                        ['label' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.options.active'),   'value' => '1'],
                        ['label' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.options.inactive'), 'value' => '0'],
                    ],
                ],
            ],
            'sortable'   => true,
            'searchable' => false,
            'filterable' => true,
            'closure'    => function ($row) {
                if ($row->status) {
                    return '<span class="label-active">'.trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.options.active').'</span>';
                }

                return '<span class="label-info">'.trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.options.inactive').'</span>';
            },
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-edit',
            'title'  => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.edit'),
            'method' => 'GET',
            'url'    => function ($row) {
                return route('admin.reward.wallet-topup.edit', $row->id);
            },
        ]);

        $this->addAction([
            'icon'   => 'icon-delete',
            'title'  => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.delete'),
            'method' => 'POST',
            'url'    => function ($row) {
                return route('admin.reward.wallet-topup.delete', $row->id);
            },
        ]);
    }

    public function prepareMassActions()
    {
        $this->addMassAction([
            'title'  => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.delete'),
            'url'    => route('admin.reward.wallet-topup.mass_delete'),
            'method' => 'POST',
        ]);

        $this->addMassAction([
            'title'   => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.update-status'),
            'method'  => 'POST',
            'url'     => route('admin.reward.wallet-topup.mass_update'),
            'options' => [
                ['label' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.options.active'),   'value' => 1],
                ['label' => trans('rewards::app.admin.rewards.wallet-topup.index.datagrid.options.inactive'), 'value' => 0],
            ],
        ]);
    }
}
