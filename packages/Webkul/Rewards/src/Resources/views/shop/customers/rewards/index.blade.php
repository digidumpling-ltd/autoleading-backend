
<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('rewards::app.shop.customer.account.rewards.index.your-reward-points')
    </x-slot>

    <div class="flex items-center justify-between">
        <div class="">
            <h2 class="text-lg font-medium">
                @lang('rewards::app.shop.customer.account.rewards.index.your-reward-points') {{ $totalRewardPoints }}
            </h2>
        </div>
    </div>

    <x-shop::datagrid :src="route('customer.rewards.index')"></x-shop::datagrid>

</x-shop::layouts.account>