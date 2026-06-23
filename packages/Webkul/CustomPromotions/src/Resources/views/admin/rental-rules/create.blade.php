<x-admin::layouts>
    <x-slot:title>
        @lang('custom_promotions::app.admin.rental-rules.create.title')
    </x-slot>

    <x-admin::form :action="route('admin.custom_promotions.rental_rules.store')">

        <div class="mt-3 flex items-center justify-between gap-4 max-sm:flex-wrap">
            <p class="text-xl font-bold text-gray-800 dark:text-white">
                @lang('custom_promotions::app.admin.rental-rules.create.title')
            </p>

            <div class="flex items-center gap-x-2.5">
                <a
                    href="{{ url()->previous(route('admin.custom_promotions.rental_rules.index')) }}"
                    class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                >
                    @lang('custom_promotions::app.common.back')
                </a>

                <button type="submit" class="primary-button">
                    @lang('custom_promotions::app.common.save')
                </button>
            </div>
        </div>

        @include('custom_promotions::admin.rental-rules._form', [
            'rule'             => null,
            'attributeOptions' => app(\Webkul\CustomPromotions\Services\ConditionEvaluator::class)->getRentalConditionAttributes(),
        ])

    </x-admin::form>

    @include('custom_promotions::admin._promotion-condition-script')
</x-admin::layouts>
