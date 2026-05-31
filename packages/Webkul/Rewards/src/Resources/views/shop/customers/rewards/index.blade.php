
<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('rewards::app.shop.customer.account.rewards.index.your-reward-points')
    </x-slot>

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center max-sm:mb-5">
            <!-- Back Button -->
            <a
                class="grid md:hidden"
                href="{{ route('shop.customers.account.index') }}"
            >
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium ltr:ml-2.5 rtl:mr-2.5 max-sm:text-base md:ltr:ml-0 md:rtl:mr-0">
                @lang('rewards::app.shop.customer.account.rewards.index.your-reward-points')
            </h2>
        </div>

        <div class="mb-6 flex items-center justify-between">
            <p class="text-lg font-medium">
                @lang('rewards::app.shop.customer.account.rewards.index.your-reward-points')
                <span class="font-semibold">{{ $totalRewardPoints }}</span>
            </p>
        </div>

        <x-shop::datagrid :src="route('customer.rewards.index')"></x-shop::datagrid>
    </div>

</x-shop::layouts.account>