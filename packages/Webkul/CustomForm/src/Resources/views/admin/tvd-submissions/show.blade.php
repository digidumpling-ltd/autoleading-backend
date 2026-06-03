<x-admin::layouts>
    <x-slot:title>
        @lang('customform::app.menu')
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('customform::app.menu') — #{{ $submission->id }}
        </p>

        <a
            href="{{ route('admin.tvd-form.index') }}"
            class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
        >
            @lang('customform::app.back')
        </a>
    </div>

    <div class="box-shadow rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900" style="padding: 1.5rem;">
        <div class="grid grid-cols-2 gap-6 max-md:grid-cols-1">
            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.chinese-name')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->chinese_name }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.english-name')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->english_name }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.rental-model')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->rental_model }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.return-date')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->return_date }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.contact-number')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->contact_number }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.email')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->email }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.refund-type')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ ucfirst($submission->refund_type) }}</p>
            </div>

            <div>
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Submitted At</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->created_at->format('Y-m-d H:i:s') }}</p>
            </div>

            <div class="col-span-2 max-md:col-span-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.local-bank-info')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->local_bank_info ?: '—' }}</p>
            </div>

            <div class="col-span-2 max-md:col-span-1">
                <p class="text-xs font-medium text-gray-500 dark:text-gray-400">@lang('customform::app.fields.overseas-bank-info')</p>
                <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $submission->overseas_bank_info ?: '—' }}</p>
            </div>
        </div>
    </div>
</x-admin::layouts>
