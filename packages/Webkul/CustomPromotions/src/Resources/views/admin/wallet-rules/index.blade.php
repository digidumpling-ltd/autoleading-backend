<x-admin::layouts>
    <x-slot:title>
        @lang('custom_promotions::app.admin.wallet-rules.index.title')
    </x-slot>

    <div class="mt-3 flex items-center justify-between gap-4 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('custom_promotions::app.admin.wallet-rules.index.title')
        </p>

        <div class="flex items-center gap-x-2.5">
            @if (bouncer()->hasPermission('marketing.promotions.wallet_rules.create'))
                <a
                    href="{{ route('admin.custom_promotions.wallet_rules.create') }}"
                    class="primary-button"
                >
                    @lang('custom_promotions::app.admin.wallet-rules.index.create-btn')
                </a>
            @endif
        </div>
    </div>

    <x-admin::datagrid :src="route('admin.custom_promotions.wallet_rules.index')" />
</x-admin::layouts>
