<x-shop::layouts.account>
    <x-slot:title>
        @lang('customer_promotions::app.customers.account.promotions.title')
    </x-slot>

    @if (core()->getConfigData('general.general.breadcrumbs.shop'))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="promotions" />
        @endSection
    @endif

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center max-sm:mb-5">
            <a
                class="grid md:hidden"
                href="{{ route('shop.customers.account.index') }}"
            >
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium ltr:ml-2.5 rtl:mr-2.5 max-sm:text-base md:ltr:ml-0 md:rtl:mr-0">
                @lang('customer_promotions::app.customers.account.promotions.title')
            </h2>
        </div>

        @if ($promotions->isEmpty())
            <div class="flex flex-col items-center justify-center gap-5 py-20">
                <span class="icon-email text-6xl text-gray-300"></span>

                <div class="text-center">
                    <p class="text-xl font-semibold">
                        @lang('customer_promotions::app.customers.account.promotions.empty-title')
                    </p>

                    <p class="mt-2 text-gray-600">
                        @lang('customer_promotions::app.customers.account.promotions.empty-description')
                    </p>
                </div>
            </div>
        @else
            <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                @foreach ($promotions as $promotion)
                    <div class="flex flex-col gap-3 rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900">
                                {{ $promotion->name }}
                            </h3>

                            @if ($promotion->description)
                                <p class="mt-1 text-sm text-gray-600">
                                    {{ $promotion->description }}
                                </p>
                            @endif
                        </div>

                        @if ($promotion->ends_till)
                            <p class="text-xs text-gray-500">
                                @lang('customer_promotions::app.customers.account.promotions.valid-until', [
                                    'date' => \Carbon\Carbon::parse($promotion->ends_till)->format('M d, Y'),
                                ])
                            </p>
                        @endif

                        <div class="flex items-center gap-2">
                            <div class="flex flex-1 items-center rounded-lg border border-dashed border-navyBlue bg-blue-50 px-4 py-2">
                                <span class="font-mono text-sm font-semibold tracking-widest text-navyBlue">
                                    {{ $promotion->cart_rule_coupon->code }}
                                </span>
                            </div>

                            <button
                                type="button"
                                class="secondary-button px-4 py-2 font-normal"
                                data-copy-label="@lang('customer_promotions::app.customers.account.promotions.copy')"
                                data-copied-label="@lang('customer_promotions::app.customers.account.promotions.copied')"
                                onclick="copyPromoCode(this, '{{ addslashes($promotion->cart_rule_coupon->code) }}')"
                            >
                                @lang('customer_promotions::app.customers.account.promotions.copy')
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @pushOnce('scripts')
        <script>
            function copyPromoCode(btn, code) {
                var copy = function () {
                    var copied = btn.dataset.copiedLabel;
                    var original = btn.dataset.copyLabel;
                    btn.textContent = copied;
                    setTimeout(function () { btn.textContent = original; }, 2000);
                };

                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(code).then(copy);
                } else {
                    var ta = document.createElement('textarea');
                    ta.value = code;
                    ta.style.cssText = 'position:fixed;opacity:0';
                    document.body.appendChild(ta);
                    ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    copy();
                }
            }
        </script>
    @endPushOnce
</x-shop::layouts.account>
