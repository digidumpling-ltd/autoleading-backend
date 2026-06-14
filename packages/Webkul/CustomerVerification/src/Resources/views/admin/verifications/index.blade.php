<x-admin::layouts>
    <x-slot:title>
        {{ trans('customer-verification::app.common.manage_verifications') }}
    </x-slot>

    <div class="flex items-center justify-between">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            {{ trans('customer-verification::app.common.manage_verifications') }}
        </p>
    </div>

    <x-admin::datagrid :src="route('admin.verification.index')" />
</x-admin::layouts>
