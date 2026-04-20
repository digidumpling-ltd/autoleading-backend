@extends('admin::layouts.master')

@section('page_title')
    @lang('admin::app.customers.verifications.title')
@endsection

@section('content')
    <div class="page-content">
        <!-- Page Header -->
        <div class="page-header">
            <div class="flex items-center justify-between">
                <h1 class="flex items-center text-2xl font-bold text-gray-800 dark:text-white">
                    @lang('admin::app.customers.verifications.title')
                </h1>
            </div>
        </div>

        <!-- DataGrid -->
        <x-admin::datagrid
            :src="route('admin.customers.verifications.get')"
            ref="datagrid"
        />
    </div>
@endsection
