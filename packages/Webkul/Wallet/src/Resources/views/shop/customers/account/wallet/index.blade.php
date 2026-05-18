<x-shop::layouts.account>
    <x-slot:title>
        @lang('bagisto-wallet::app.customers.account.wallet.title')
    </x-slot>

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <!-- Header -->
        <div class="mb-8 flex items-center justify-between max-sm:mb-5">
            <div class="flex items-center">
                <a class="grid md:hidden" href="{{ route('shop.customers.account.index') }}">
                    <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
                </a>

                <h2 class="text-2xl font-medium ltr:ml-2.5 rtl:mr-2.5 max-sm:text-base md:ltr:ml-0 md:rtl:mr-0">
                    @lang('bagisto-wallet::app.customers.account.wallet.title')
                </h2>
            </div>

            @php
            $walletGatingEnabled = core()->getConfigData('customer_verification.wallet.settings.require_verification');
            $isVerified = auth()->guard('customer')->user()->verification_status ?? 'incomplete';
            @endphp

                           <a
                    href="{{ route('shop.customers.account.wallet.topup') }}"
                    class="primary-button rounded-2xl px-6 py-3 max-sm:px-4 max-sm:py-2 max-sm:text-sm"
                >
                    @lang('bagisto-wallet::app.customers.account.wallet.topup')
                </a>

            @if (!($walletGatingEnabled && $isVerified !== \Webkul\CustomerVerification\Support\Verification::STATUS_APPROVED))
                <a
                    href="{{ route('shop.customers.account.wallet.topup') }}"
                    class="primary-button rounded-2xl px-6 py-3 max-sm:px-4 max-sm:py-2 max-sm:text-sm"
                >
                    @lang('bagisto-wallet::app.customers.account.wallet.topup')
                </a>
            @endif
        </div>

            @if ($walletGatingEnabled && $isVerified !== \Webkul\CustomerVerification\Support\Verification::STATUS_APPROVED)
                <div class="mb-6 rounded-md border border-yellow-200 bg-yellow-50 px-4 py-3 text-sm text-yellow-700 flex items-center gap-3">
                    <span class="icon-toast-exclamation-mark text-2xl"></span>

                    <div class="text-sm">
                        {!! trans('customer-verification::app.common.wallet_topup_requires_verification_link', ['dashboard_url' => route('shop.customer.verification.index')]) !!}
                    </div>
                </div>
            @endif

        <!-- Balance Card -->
        <div class="mb-8 rounded-xl border border-zinc-200 p-6">
            <p class="mb-1 text-sm opacity-80">
                @lang('bagisto-wallet::app.customers.account.wallet.balance')
            </p>

            <p class="text-4xl font-semibold">
                {{ core()->formatPrice($customer->balanceFloatNum) }}
            </p>
        </div>

        <!-- Transaction History -->
        <h3 class="mb-4 text-xl font-medium">
            @lang('bagisto-wallet::app.customers.account.wallet.transactions')
        </h3>

        @if ($transactions->isEmpty())
            <div class="flex h-36 items-center justify-center rounded-xl border border-dashed border-zinc-300 text-zinc-500">
                @lang('bagisto-wallet::app.customers.account.wallet.no-transactions')
            </div>
        @else
            <div class="overflow-x-auto rounded-xl border border-zinc-200">
                <table class="w-full">
                    <thead class="bg-zinc-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-500">
                                @lang('bagisto-wallet::app.customers.account.wallet.type')
                            </th>
                            <th class="px-6 py-3 text-right text-sm font-medium text-zinc-500">
                                @lang('bagisto-wallet::app.customers.account.wallet.amount')
                            </th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-zinc-500">
                                @lang('bagisto-wallet::app.customers.account.wallet.remarks')
                            </th>
                            <th class="px-6 py-3 text-right text-sm font-medium text-zinc-500">
                                @lang('bagisto-wallet::app.customers.account.wallet.date')
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-zinc-100">
                        @foreach ($transactions as $transaction)
                            <tr class="hover:bg-zinc-50">
                                <td class="px-6 py-4">
                                    <span class="inline-flex h-8 w-8 items-center justify-center rounded-full
                                        {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        @if ($transaction->type === 'deposit')
                                            <x-tabler-credit-card-pay class="h-5 w-5" />
                                        @else
                                            <x-tabler-credit-card-refund class="h-5 w-5" />
                                        @endif
                                    </span>
                                </td>

                                <td class="px-6 py-4 text-right font-medium
                                    {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ core()->formatPrice((float) $transaction->amountFloat) }}
                                </td>

                                <td class="px-6 py-4 text-sm text-zinc-600">
                                    {{ $transaction->meta['reason'] ?? $transaction->meta['description'] ?? '' }}
                                </td>

                                <td class="px-6 py-4 text-right text-sm text-zinc-500">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transactions->links() }}
            </div>
        @endif

        @if (session('success'))
            <div class="mt-4 rounded-lg bg-green-50 p-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif
    </div>
</x-shop::layouts.account>
