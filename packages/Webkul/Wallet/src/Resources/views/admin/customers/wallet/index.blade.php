<x-admin::layouts>
    <x-slot:title>
        @lang('bagisto-wallet::app.admin.customers.wallet.title')
    </x-slot>

    <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
        <div class="flex items-center gap-2.5">
            <a href="{{ route('admin.customers.customers.view', $customer->id) }}">
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h1 class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                @lang('bagisto-wallet::app.admin.customers.wallet.title')
                — {{ $customer->first_name }} {{ $customer->last_name }}
            </h1>
        </div>
    </div>

    <div class="mt-8 grid gap-6 lg:grid-cols-3">
        <!-- Balance Card -->
        <div class="box-shadow rounded bg-white p-6 dark:bg-gray-900 lg:col-span-1">
            <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                @lang('bagisto-wallet::app.admin.customers.wallet.balance')
            </p>

            <p class="text-3xl font-bold text-gray-800 dark:text-white">
                {{ core()->formatPrice($customer->balanceFloatNum) }}
            </p>

            <p class="mt-2 text-sm text-gray-500">{{ $customer->email }}</p>

            @if (session('success'))
                <div class="mt-4 rounded border border-green-200 bg-green-50 p-3 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif
        </div>

        <!-- Adjustment Form -->
        <div class="box-shadow rounded bg-white p-6 dark:bg-gray-900 lg:col-span-2">
            <h3 class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('bagisto-wallet::app.admin.customers.wallet.adjust-title')
            </h3>

            <form
                method="POST"
                action="{{ route('admin.customers.wallet.adjust', $customer->id) }}"
            >
                @csrf

                @if ($errors->any())
                    <div class="mb-4 rounded border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                        @foreach ($errors->all() as $error)
                            <p>{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="mb-4 flex gap-4">
                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            type="radio"
                            name="type"
                            value="add"
                            {{ old('type', 'add') === 'add' ? 'checked' : '' }}
                            class="accent-blue-600"
                        />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('bagisto-wallet::app.admin.customers.wallet.type-add')
                        </span>
                    </label>

                    <label class="flex cursor-pointer items-center gap-2">
                        <input
                            type="radio"
                            name="type"
                            value="deduct"
                            {{ old('type') === 'deduct' ? 'checked' : '' }}
                            class="accent-blue-600"
                        />
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                            @lang('bagisto-wallet::app.admin.customers.wallet.type-deduct')
                        </span>
                    </label>
                </div>

                <div class="mb-4">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('bagisto-wallet::app.admin.customers.wallet.amount')
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="number"
                        name="amount"
                        value="{{ old('amount') }}"
                        min="0.01"
                        step="0.01"
                        placeholder="0.00"
                        class="w-full rounded border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    />
                </div>

                <div class="mb-6">
                    <label class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-300">
                        @lang('bagisto-wallet::app.admin.customers.wallet.reason')
                        <span class="text-red-600">*</span>
                    </label>
                    <input
                        type="text"
                        name="reason"
                        value="{{ old('reason') }}"
                        minlength="5"
                        class="w-full rounded border border-gray-200 px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
                    />
                </div>

                <button
                    type="submit"
                    class="primary-button"
                >
                    @lang('bagisto-wallet::app.admin.customers.wallet.adjust-submit')
                </button>
            </form>
        </div>
    </div>

    <!-- Transaction History -->
    <div class="box-shadow mt-6 rounded bg-white dark:bg-gray-900">
        <div class="border-b border-gray-200 px-6 py-4 dark:border-gray-700">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white">
                @lang('bagisto-wallet::app.admin.customers.wallet.transactions')
            </h3>
        </div>

        @if ($transactions->isEmpty())
            <div class="flex h-24 items-center justify-center text-sm text-gray-500">
                @lang('bagisto-wallet::app.admin.customers.wallet.no-transactions')
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left text-xs font-medium uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <th class="px-6 py-3">@lang('bagisto-wallet::app.admin.customers.wallet.col-type')</th>
                            <th class="px-6 py-3 text-right">@lang('bagisto-wallet::app.admin.customers.wallet.col-amount')</th>
                            <th class="px-6 py-3">@lang('bagisto-wallet::app.admin.customers.wallet.col-meta')</th>
                            <th class="px-6 py-3 text-right">@lang('bagisto-wallet::app.admin.customers.wallet.col-date')</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($transactions as $transaction)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-3">
                                    @php $type = $transaction->meta['type'] ?? $transaction->type; @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                                        {{ $transaction->type === 'deposit' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                        {{ $type }}
                                    </span>
                                </td>

                                <td class="px-6 py-3 text-right font-medium
                                    {{ $transaction->type === 'deposit' ? 'text-green-600' : 'text-red-600' }}">
                                    {{ core()->formatPrice((float) $transaction->amountFloat) }}
                                </td>

                                <td class="px-6 py-3 text-xs text-gray-500">
                                    {{ $transaction->meta['reason'] ?? $transaction->meta['description'] ?? '' }}
                                </td>

                                <td class="px-6 py-3 text-right text-gray-500">
                                    {{ $transaction->created_at->format('d M Y, H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="border-t border-gray-100 px-6 py-3 dark:border-gray-700">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>
</x-admin::layouts>
