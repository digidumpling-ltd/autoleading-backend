<x-admin::layouts>

    <x-slot:title>
        @lang('rewards::app.admin.rewards.wallet-topup.index.title')
    </x-slot:title>

    <div class="flex justify-between items-center">
        <p class="py-3 text-xl text-gray-800 dark:text-white font-bold">
            @lang('rewards::app.admin.rewards.wallet-topup.index.title')
        </p>

        <div class="flex gap-x-2.5 items-center">
            <x-admin::datagrid.export src="{{ route('admin.reward.wallet-topup.index') }}" />

            <button type="button" class="primary-button">
                <a href="{{ route('admin.reward.wallet-topup.create') }}" class="btn-primary">
                    @lang('rewards::app.admin.rewards.wallet-topup.index.add-btn')
                </a>
            </button>
        </div>
    </div>

    <x-admin::datagrid src="{{ route('admin.reward.wallet-topup.index') }}" />

</x-admin::layouts>
