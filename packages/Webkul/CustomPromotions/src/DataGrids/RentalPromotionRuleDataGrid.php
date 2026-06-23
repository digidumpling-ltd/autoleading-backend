<?php

namespace Webkul\CustomPromotions\DataGrids;

use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class RentalPromotionRuleDataGrid extends DataGrid
{
    public function prepareQueryBuilder(): Builder
    {
        return DB::table('custom_rental_promotion_rules')
            ->select('id', 'name', 'status', 'starts_from', 'ends_till', 'sort_order');
    }

    public function prepareColumns(): void
    {
        $this->addColumn([
            'index' => 'id',
            'label' => trans('custom_promotions::app.admin.rental-rules.index.datagrid.id'),
            'type' => 'integer',
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'name',
            'label' => trans('custom_promotions::app.admin.rental-rules.index.datagrid.name'),
            'type' => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'status',
            'label' => trans('custom_promotions::app.admin.rental-rules.index.datagrid.status'),
            'type' => 'boolean',
            'filterable' => true,
            'filterable_options' => [
                ['label' => trans('custom_promotions::app.common.active'),   'value' => 1],
                ['label' => trans('custom_promotions::app.common.inactive'), 'value' => 0],
            ],
            'sortable' => true,
            'closure' => fn ($row) => $row->status
                ? trans('custom_promotions::app.common.active')
                : trans('custom_promotions::app.common.inactive'),
        ]);

        $this->addColumn([
            'index' => 'starts_from',
            'label' => trans('custom_promotions::app.admin.rental-rules.index.datagrid.starts-from'),
            'type' => 'date',
            'sortable' => true,
            'closure' => fn ($row) => $row->starts_from ?? '-',
        ]);

        $this->addColumn([
            'index' => 'ends_till',
            'label' => trans('custom_promotions::app.admin.rental-rules.index.datagrid.ends-till'),
            'type' => 'date',
            'sortable' => true,
            'closure' => fn ($row) => $row->ends_till ?? '-',
        ]);

        $this->addColumn([
            'index' => 'sort_order',
            'label' => trans('custom_promotions::app.admin.rental-rules.index.datagrid.sort-order'),
            'type' => 'integer',
            'sortable' => true,
        ]);
    }

    public function prepareActions(): void
    {
        if (bouncer()->hasPermission('marketing.promotions.rental_rules.edit')) {
            $this->addAction([
                'icon' => 'icon-edit',
                'title' => trans('custom_promotions::app.common.edit'),
                'method' => 'GET',
                'url' => fn ($row) => route('admin.custom_promotions.rental_rules.edit', $row->id),
            ]);
        }

        if (bouncer()->hasPermission('marketing.promotions.rental_rules.delete')) {
            $this->addAction([
                'icon' => 'icon-delete',
                'title' => trans('custom_promotions::app.common.delete'),
                'method' => 'DELETE',
                'url' => fn ($row) => route('admin.custom_promotions.rental_rules.destroy', $row->id),
            ]);
        }
    }
}
