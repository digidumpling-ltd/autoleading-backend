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

                @if ($testMode)
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-medium text-zinc-700">
                            @lang('bagisto-wallet::app.customers.account.wallet.topup-select-method')
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="mb-3 rounded-lg border border-amber-200 bg-amber-50 px-4 py-2.5">
                            <p class="text-xs font-medium text-amber-700">
                                @lang('bagisto-wallet::app.customers.account.wallet.topup-test-mode-notice')
                            </p>
                        </div>

                        <div class="flex flex-wrap gap-7 max-md:gap-4 max-sm:gap-2.5">
                            <div class="relative cursor-pointer max-md:max-w-full max-md:flex-auto">
                                <input
                                    type="radio"
                                    name="payment_method"
                                    value="test"
                                    id="payment_method_test"
                                    checked
                                    class="peer hidden"
                                />

                                <label
                                    for="payment_method_test"
                                    class="icon-radio-unselect peer-checked:icon-radio-select absolute top-5 cursor-pointer text-2xl text-navyBlue ltr:right-5 rtl:left-5"
                                ></label>

                                <label
                                    for="payment_method_test"
                                    class="block w-[190px] cursor-pointer rounded-xl border border-zinc-200 p-5 max-md:flex max-md:w-full max-md:gap-5 max-md:rounded-lg max-sm:gap-4 max-sm:px-4 max-sm:py-2.5"
                                >
                                    <div>
                                        <p class="mt-1.5 text-sm font-semibold max-md:mt-1 max-sm:mt-0">
                                            @lang('bagisto-wallet::app.customers.account.wallet.topup-test-mode-title')
                                        </p>

                                        <p class="mt-2.5 text-xs font-medium text-zinc-500 max-md:mt-1 max-sm:mt-0">
                                            @lang('bagisto-wallet::app.customers.account.wallet.topup-test-mode-description')
                                        </p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        @error('payment_method')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @elseif ($methods->isNotEmpty())
                    <div class="mb-6">
                        <label class="mb-3 block text-sm font-medium text-zinc-700">
                            @lang('bagisto-wallet::app.customers.account.wallet.topup-select-method')
                            <span class="text-red-500">*</span>
                        </label>

                        <div class="flex flex-wrap gap-7 max-md:gap-4 max-sm:gap-2.5">
                            @foreach ($methods as $method)
                                @php $checked = old('payment_method', $loop->first ? $method['method'] : null) === $method['method']; @endphp

                                <div class="relative cursor-pointer max-md:max-w-full max-md:flex-auto">
                                    <input
                                        type="radio"
                                        name="payment_method"
                                        value="{{ $method['method'] }}"
                                        id="payment_method_{{ $method['method'] }}"
                                        {{ $checked ? 'checked' : '' }}
                                        class="peer hidden"
                                    />

                                    <label
                                        for="payment_method_{{ $method['method'] }}"
                                        class="icon-radio-unselect peer-checked:icon-radio-select absolute top-5 cursor-pointer text-2xl text-navyBlue ltr:right-5 rtl:left-5"
                                    ></label>

                                    <label
                                        for="payment_method_{{ $method['method'] }}"
                                        class="block w-[190px] cursor-pointer rounded-xl border border-zinc-200 p-5 max-md:flex max-md:w-full max-md:gap-5 max-md:rounded-lg max-sm:gap-4 max-sm:px-4 max-sm:py-2.5"
                                    >
                                        @if ($method['image'])
                                            <img
                                                class="max-h-11 max-w-14"
                                                src="{{ $method['image'] }}"
                                                width="55"
                                                height="55"
                                                alt="{{ $method['method_title'] }}"
                                                title="{{ $method['method_title'] }}"
                                            />
                                        @endif

                                        <div>
                                            <p class="mt-1.5 text-sm font-semibold max-md:mt-1 max-sm:mt-0">
                                                {{ $method['method_title'] }}
                                            </p>

                                            @if ($method['description'])
                                                <p class="mt-2.5 text-xs font-medium text-zinc-500 max-md:mt-1 max-sm:mt-0">
                                                    {{ $method['description'] }}
                                                </p>
                                            @endif
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>

                        @error('payment_method')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                @else
                    <p class="mb-4 text-sm text-zinc-500">
                        @lang('bagisto-wallet::app.customers.account.wallet.topup-no-methods')
                    </p>
                @endif

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
