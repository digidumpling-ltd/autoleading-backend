<?php

namespace Webkul\Wallet\DataGrids\Admin;

use Illuminate\Support\Facades\DB;
use Webkul\DataGrid\DataGrid;

class WalletTransactionDataGrid extends DataGrid
{
    public function prepareQueryBuilder()
    {
        $customerId = (int) request()->route('id');

        $p = DB::getTablePrefix();

        $queryBuilder = DB::table('transactions')
            ->join('wallets', 'wallets.id', '=', 'transactions.wallet_id')
            ->leftJoin('admins', function ($join) use ($p) {
                $join->on('admins.id', '=', DB::raw("CAST(JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.creator_id')) AS UNSIGNED)"))
                    ->where(DB::raw("JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.creator_type'))"), '=', 'admin');
            })
            ->select([
                'transactions.id',
                'transactions.type',
                'transactions.amount',
                'transactions.created_at',
                'wallets.decimal_places',
                DB::raw("COALESCE(
                    JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.reason')),
                    JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.description')),
                    ''
                ) as remarks"),
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.creator_type')) as creator_type"),
                DB::raw("{$p}admins.name as creator_admin_name"),
            ])
            ->where('wallets.holder_id', $customerId)
            ->where('wallets.holder_type', 'Webkul\\Wallet\\Models\\Customer')
            ->whereNull('transactions.deleted_at');

        $this->addFilter('created_at', 'transactions.created_at');
        $this->addFilter('type', 'transactions.type');
        $this->addFilter('remarks', DB::raw("COALESCE(JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.reason')), JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.description')), '') COLLATE utf8mb4_general_ci"));
        $this->addFilter('creator_type', DB::raw("JSON_UNQUOTE(JSON_EXTRACT({$p}transactions.meta, '$.creator_type'))"));

        return $queryBuilder;
    }

    public function prepareColumns()
    {
        $this->addColumn([
            'index'              => 'type',
            'label'              => trans('bagisto-wallet::app.admin.customers.wallet.col-type'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => true,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => trans('bagisto-wallet::app.customers.account.wallet.type-deposit'), 'value' => 'deposit'],
                ['label' => trans('bagisto-wallet::app.customers.account.wallet.type-withdraw'), 'value' => 'withdraw'],
            ],
            'closure' => function ($row) {
                $label = trans('bagisto-wallet::app.customers.account.wallet.type-' . $row->type);

                $color = $row->type === 'deposit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700';

                return '<span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium ' . $color . '">' . $label . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'amount',
            'label'      => trans('bagisto-wallet::app.admin.customers.wallet.col-amount'),
            'type'       => 'string',
            'searchable' => false,
            'sortable'   => true,
            'filterable' => false,
            'closure'    => function ($row) {
                $color = $row->type === 'deposit' ? 'text-green-600' : 'text-red-600';

                return '<span class="font-medium ' . $color . '">' . core()->formatPrice($row->amount / pow(10, $row->decimal_places)) . '</span>';
            },
        ]);

        $this->addColumn([
            'index'      => 'remarks',
            'label'      => trans('bagisto-wallet::app.admin.customers.wallet.col-meta'),
            'type'       => 'string',
            'searchable' => true,
            'sortable'   => false,
            'filterable' => false,
        ]);

        $this->addColumn([
            'index'              => 'creator_type',
            'label'              => trans('bagisto-wallet::app.admin.customers.wallet.col-created-by'),
            'type'               => 'string',
            'searchable'         => false,
            'sortable'           => false,
            'filterable'         => true,
            'filterable_type'    => 'dropdown',
            'filterable_options' => [
                ['label' => trans('bagisto-wallet::app.admin.customers.wallet.creator-system'),   'value' => 'system'],
                ['label' => trans('bagisto-wallet::app.admin.customers.wallet.creator-admin'),    'value' => 'admin'],
                ['label' => trans('bagisto-wallet::app.admin.customers.wallet.creator-customer'), 'value' => 'customer'],
            ],
            'closure'    => function ($row) {
                return match ($row->creator_type) {
                    'admin'    => $row->creator_admin_name ?? trans('bagisto-wallet::app.admin.customers.wallet.creator-admin'),
                    'customer' => trans('bagisto-wallet::app.admin.customers.wallet.creator-customer'),
                    'system'   => trans('bagisto-wallet::app.admin.customers.wallet.creator-system'),
                    default    => '—',
                };
            },
        ]);

        $this->addColumn([
            'index'           => 'created_at',
            'label'           => trans('bagisto-wallet::app.admin.customers.wallet.col-date'),
            'type'            => 'date',
            'searchable'      => false,
            'sortable'        => true,
            'filterable'      => true,
            'filterable_type' => 'date_range',
        ]);
    }
}
