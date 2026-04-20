<?php

namespace Webkul\Admin\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class VerificationDataGrid extends DataGrid
{
    /**
     * Prepare query builder.
     *
     * @return void
     */
    public function prepareQueryBuilder()
    {
        $this->queryBuilder = DB::table('customers as c')
            ->select(
                'c.id',
                'c.first_name',
                'c.last_name',
                'c.email',
                'c.verification_status',
                'c.created_at',
                DB::raw('COUNT(cvd.id) as document_count')
            )
            ->leftJoin('customer_verification_documents as cvd', 'c.id', '=', 'cvd.customer_id')
            ->where('c.is_suspended', 0)
            ->groupBy('c.id', 'c.first_name', 'c.last_name', 'c.email', 'c.verification_status', 'c.created_at');
    }

    /**
     * Add columns.
     *
     * @return void
     */
    public function addColumns()
    {
        $this->addColumn([
            'index' => 'first_name',
            'label' => trans('admin::app.customers.customers.datagrid.first-name'),
            'type' => 'string',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'last_name',
            'label' => trans('admin::app.customers.customers.datagrid.last-name'),
            'type' => 'string',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'email',
            'label' => trans('admin::app.customers.customers.datagrid.email'),
            'type' => 'string',
            'searchable' => true,
        ]);

        $this->addColumn([
            'index' => 'verification_status',
            'label' => trans('admin::app.customers.verifications.datagrid.status'),
            'type' => 'string',
            'sortable' => true,
            'closure' => function ($row) {
                $statusClasses = [
                    'pending' => 'bg-yellow-100 text-yellow-800',
                    'approved' => 'bg-green-100 text-green-800',
                    'rejected' => 'bg-red-100 text-red-800',
                    'incomplete' => 'bg-gray-100 text-gray-800',
                ];

                $class = $statusClasses[$row->verification_status] ?? 'bg-gray-100 text-gray-800';

                return '<span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold ' . $class . '">'
                    . trans('admin::app.customers.verifications.status.' . $row->verification_status)
                    . '</span>';
            },
        ]);

        $this->addColumn([
            'index' => 'document_count',
            'label' => trans('admin::app.customers.verifications.datagrid.documents'),
            'type' => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index' => 'created_at',
            'label' => trans('admin::app.customers.customers.datagrid.date'),
            'type' => 'date_text',
            'sortable' => true,
        ]);
    }

    /**
     * Prepare filters.
     *
     * @return void
     */
    public function prepareFilters()
    {
        $this->addFilter('first_name', 'admin::app.customers.customers.datagrid.first-name');
        $this->addFilter('last_name', 'admin::app.customers.customers.datagrid.last-name');
        $this->addFilter('email', 'admin::app.customers.customers.datagrid.email');

        $this->addFilter('verification_status', 'admin::app.customers.verifications.datagrid.status', 'select', [
            ['id' => 'pending', 'label' => trans('admin::app.customers.verifications.status.pending')],
            ['id' => 'approved', 'label' => trans('admin::app.customers.verifications.status.approved')],
            ['id' => 'rejected', 'label' => trans('admin::app.customers.verifications.status.rejected')],
            ['id' => 'incomplete', 'label' => trans('admin::app.customers.verifications.status.incomplete')],
        ]);
    }

    /**
     * Add mass actions.
     *
     * @return void
     */
    public function prepareMassActions()
    {
        $this->addMassAction([
            'key' => 'approve',
            'label' => trans('admin::app.customers.verifications.mass-action.approve'),
            'action' => route('admin.customers.verifications.mass-update', ['status' => 'approved']),
            'method' => 'PUT',
        ]);

        $this->addMassAction([
            'key' => 'reject',
            'label' => trans('admin::app.customers.verifications.mass-action.reject'),
            'action' => route('admin.customers.verifications.mass-update', ['status' => 'rejected']),
            'method' => 'PUT',
        ]);
    }
}
