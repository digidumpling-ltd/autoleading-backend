<x-shop::layouts.account>
    <x-slot:title>
        @lang('bagisto-wallet::app.customers.account.wallet.title')
    </x-slot>

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <!-- Header -->
        <div class="mb-8 flex items-center max-sm:mb-5">
            <a class="grid md:hidden" href="{{ route('shop.customers.account.index') }}">
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium ltr:ml-2.5 rtl:mr-2.5 max-sm:text-base md:ltr:ml-0 md:rtl:mr-0">
                @lang('bagisto-wallet::app.customers.account.wallet.title')
            </h2>
        </div>

        @php
            $walletGatingEnabled = core()->getConfigData('sales.wallet.gating.require_topup_verification');
            $isVerified = auth()->guard('customer')->user()->verification_status ?? 'incomplete';
        @endphp

        @if ($walletGatingEnabled && $isVerified !== \Webkul\CustomerVerification\Support\Verification::STATUS_APPROVED)
            <div class="mb-6 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700 flex items-center gap-3">
                <span class="icon-toast-exclamation-mark text-2xl"></span>

                <div class="text-sm">
                    {!! trans('bagisto-wallet::app.common.topup-requires-verification-link', ['dashboard_url' => route('shop.customer.verification.index')]) !!}
                </div>
            </div>
        @endif

        <!-- Balance Card -->
        <div class="mb-8 flex items-center justify-between rounded-xl border border-zinc-200 p-6">
            <div>
                <p class="mb-1 text-sm opacity-80">
                    @lang('bagisto-wallet::app.customers.account.wallet.balance')
                </p>

                <p class="text-4xl font-semibold">
                    {{ core()->formatPrice($customer->balanceFloatNum) }}
                </p>
            </div>

            @if (!($walletGatingEnabled && $isVerified !== \Webkul\CustomerVerification\Support\Verification::STATUS_APPROVED))
                <a
                    href="{{ route('shop.customers.account.wallet.topup') }}"
                    class="primary-button rounded-2xl px-6 py-3 max-sm:px-4 max-sm:py-2 max-sm:text-sm"
                >
                    @lang('bagisto-wallet::app.customers.account.wallet.topup')
                </a>
            @endif
        </div>

        <!-- Transaction History -->
        <h3 class="mb-4 text-xl font-medium">
            @lang('bagisto-wallet::app.customers.account.wallet.transactions')
        </h3>

        <x-shop::datagrid :src="route('shop.customers.account.wallet.index')" />

        @if (session('success'))
            <div class="mt-4 rounded-lg bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-shop::layouts.account>
