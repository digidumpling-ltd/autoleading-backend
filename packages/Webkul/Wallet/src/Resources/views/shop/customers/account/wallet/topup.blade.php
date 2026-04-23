<x-shop::layouts.account>
    <x-slot:title>
        @lang('bagisto-wallet::app.customers.account.wallet.topup')
    </x-slot>

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <!-- Header -->
        <div class="mb-8 flex items-center max-sm:mb-5">
            <a class="grid" href="{{ route('shop.customers.account.wallet.index') }}">
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium ltr:ml-2.5 rtl:mr-2.5 max-sm:text-base">
                @lang('bagisto-wallet::app.customers.account.wallet.topup')
            </h2>
        </div>

        <!-- Current Balance -->
        <div class="mb-6 rounded-xl border border-zinc-200 p-4">
            <p class="text-sm text-zinc-500">@lang('bagisto-wallet::app.customers.account.wallet.balance')</p>
            <p class="text-2xl font-semibold">{{ core()->formatPrice($customer->balanceFloatNum) }}</p>
        </div>

        <!-- Top-Up Form -->
        <div class="max-w-md rounded-xl border border-zinc-200 p-6">
            <x-shop::form
                method="POST"
                action="{{ route('shop.customers.account.wallet.topup.store') }}"
            >
                @csrf

                <div class="mb-4">
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('bagisto-wallet::app.customers.account.wallet.topup-amount')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="number"
                            name="amount"
                            min="1"
                            step="0.01"
                            :value="old('amount')"
                            :placeholder="trans('bagisto-wallet::app.customers.account.wallet.topup-amount-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="amount" />
                    </x-shop::form.control-group>
                </div>

                <button
                    type="submit"
                    class="primary-button w-full justify-center rounded-2xl px-6 py-3"
                >
                    @lang('bagisto-wallet::app.customers.account.wallet.topup-submit')
                </button>
            </x-shop::form>
        </div>
    </div>
</x-shop::layouts.account>
