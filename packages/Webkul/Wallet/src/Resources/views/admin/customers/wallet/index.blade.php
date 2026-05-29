<x-admin::layouts>
    <x-slot:title>
        @lang('bagisto-wallet::app.admin.customers.wallet.title')
        </x-slot>

        <!-- Page Header -->
        <div class="flex items-center gap-4 max-sm:flex-wrap">
            <a href="{{ route('admin.customers.customers.view', $customer->id) }}" class="flex items-center">
                <span class="icon-arrow-right rtl:icon-arrow-left text-2xl text-gray-800 dark:text-white"></span>
            </a>

            <h1 class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('bagisto-wallet::app.admin.customers.wallet.title') - {{ $customer->first_name }} {{
                $customer->last_name }}
            </h1>
        </div>

        <!-- Balance + Adjustment -->
        <div class="box-shadow mt-8 rounded bg-white p-4 dark:bg-gray-900">
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- Balance -->
                <div class="rounded border border-gray-200 p-5 dark:border-gray-700">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        @lang('bagisto-wallet::app.admin.customers.wallet.balance')
                    </p>

                    <p class="mt-1 text-3xl font-bold text-gray-800 dark:text-white">
                        {{ core()->formatPrice($customer->balanceFloatNum) }}
                    </p>

                </div>

                <!-- Adjustment Form -->
                <div class="lg:col-span-2">
                    <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                        @lang('bagisto-wallet::app.admin.customers.wallet.adjust-title')
                    </p>

                    <x-admin::form :action="route('admin.customers.wallet.adjust', $customer->id)">
                        <div class="mb-4 flex gap-4">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="type" value="add" {{ old('type', 'add' )==='add' ? 'checked'
                                    : '' }} class="accent-blue-600" />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    @lang('bagisto-wallet::app.admin.customers.wallet.type-add')
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" name="type" value="deduct" {{ old('type')==='deduct' ? 'checked'
                                    : '' }} class="accent-blue-600" />
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                    @lang('bagisto-wallet::app.admin.customers.wallet.type-deduct')
                                </span>
                            </label>
                        </div>

                        <div class="grid gap-4 sm:grid-cols-2">
                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('bagisto-wallet::app.admin.customers.wallet.amount')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control type="price" name="amount"
                                    value="{{ old('amount') }}" rules="required|decimal:2|min_value:0.01"
                                    :label="trans('bagisto-wallet::app.admin.customers.wallet.amount')"
                                    placeholder="0.00" />

                                <x-admin::form.control-group.error control-name="amount" />
                            </x-admin::form.control-group>

                            <x-admin::form.control-group>
                                <x-admin::form.control-group.label class="required">
                                    @lang('bagisto-wallet::app.admin.customers.wallet.reason')
                                </x-admin::form.control-group.label>

                                <x-admin::form.control-group.control type="text" name="reason"
                                    value="{{ old('reason') }}" rules="required|min:5"
                                    :label="trans('bagisto-wallet::app.admin.customers.wallet.reason')" />

                                <x-admin::form.control-group.error control-name="reason" />
                            </x-admin::form.control-group>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit" class="primary-button">
                                @lang('bagisto-wallet::app.admin.customers.wallet.adjust-submit')
                            </button>
                        </div>
                    </x-admin::form>
                </div>
            </div>
        </div>

        <!-- Transaction History -->
        <div class="box-shadow mt-4 rounded bg-white dark:bg-gray-900">
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
                        <tr
                            class="bg-gray-50 text-left text-xs font-medium uppercase text-gray-500 dark:bg-gray-800 dark:text-gray-400">
                            <th class="px-6 py-3">@lang('bagisto-wallet::app.admin.customers.wallet.col-type')</th>
                            <th class="px-6 py-3 text-right">
                                @lang('bagisto-wallet::app.admin.customers.wallet.col-amount')</th>
                            <th class="px-6 py-3">@lang('bagisto-wallet::app.admin.customers.wallet.col-meta')</th>
                            <th class="px-6 py-3 text-right">
                                @lang('bagisto-wallet::app.admin.customers.wallet.col-date')</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                        @foreach ($transactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-3">
                                @php $type = $transaction->meta['type'] ?? $transaction->type; @endphp
                                <span
                                    class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
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