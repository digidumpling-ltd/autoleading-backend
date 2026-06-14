<?php

namespace Webkul\CustomerVerification\DataGrids;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class VerificationDataGrid extends DataGrid
{
    protected $primaryColumn = 'customer_id';

    public function prepareQueryBuilder()
    {
        $tablePrefix = DB::getTablePrefix();

        $queryBuilder = DB::table('customers')
            ->leftJoin('customer_verification_documents', 'customers.id', '=', 'customer_verification_documents.customer_id')
            ->addSelect([
                'customers.id as customer_id',
                'customers.email',
                'customers.phone',
                'customers.reference_number',
                'customers.verification_status',
                'customers.created_at',
            ])
            ->selectRaw('CONCAT('.$tablePrefix.'customers.first_name, " ", '.$tablePrefix.'customers.last_name) as full_name')
            ->selectRaw('COUNT(DISTINCT '.$tablePrefix.'customer_verification_documents.id) as document_count')
            ->groupBy('customers.id');

        $this->addFilter('customer_id', 'customers.id');
        $this->addFilter('reference_number', 'customers.reference_number');
        $this->addFilter('full_name', DB::raw('CONCAT('.$tablePrefix.'customers.first_name, " ", '.$tablePrefix.'customers.last_name)'));
        $this->addFilter('email', 'customers.email');
        $this->addFilter('phone', 'customers.phone');
        $this->addFilter('verification_status', 'customers.verification_status');
        $this->addFilter('created_at', 'customers.created_at');

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'      => 'reference_number',
            'label'      => trans('customer-verification::app.common.reference_number'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
            'closure'    => function ($row) {
                $ref = $row->reference_number ?? 'N/A';

                return '<span class="label-info">'.$ref.'</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'full_name',
            'label'      => trans('customer-verification::app.common.customer_name'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'      => 'email',
            'label'      => trans('customer-verification::app.common.email'),
            'type'       => 'string',
            'searchable' => true,
            'filterable' => true,
            'sortable'   => true,
        ]);

        $this->addColumn([
            'index'              => 'verification_status',
            'label'              => trans('customer-verification::app.common.verification_status_label'),
            'type'               => 'string',
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => trans('customer-verification::app.common.verification_status_incomplete'), 'value' => 'incomplete'],
                ['label' => trans('customer-verification::app.common.verification_status_pending'),    'value' => 'pending'],
                ['label' => trans('customer-verification::app.common.verification_status_approved'),   'value' => 'approved'],
                ['label' => trans('customer-verification::app.common.verification_status_rejected'),   'value' => 'rejected'],
            ],
            'sortable' => true,
            'closure'  => function ($row) {
                $status = $row->verification_status ?? 'incomplete';

                $labelClass = match ($status) {
                    'approved'   => 'label-active',
                    'pending'    => 'label-pending',
                    'rejected'   => 'label-canceled',
                    default      => 'label-info',
                };

                $label = trans('customer-verification::app.common.verification_status_'.$status);

                return '<span class="'.$labelClass.'">'.$label.'</span>';
            },
        ]);

        $this->addColumn([
            'index'    => 'document_count',
            'label'    => trans('customer-verification::app.common.documents_uploaded'),
            'type'     => 'integer',
            'sortable' => true,
        ]);

        $this->addColumn([
            'index'    => 'created_at',
            'label'    => trans('customer-verification::app.common.submitted_date'),
            'type'     => 'datetime',
            'sortable' => true,
        ]);
    }

    public function prepareActions()
    {
        $this->addAction([
            'icon'   => 'icon-view',
            'title'  => trans('customer-verification::app.common.view'),
            'method' => 'GET',
            'url'    => fn ($row) => route('admin.verification.show', $row->customer_id),
        ]);
    }
}
