<x-admin::layouts>
    <x-slot:title>
        @lang('customform::app.menu')
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('customform::app.menu')
        </p>
    </div>

    <div class="box-shadow rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">
                        @lang('customform::app.fields.return-date')
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">
                        @lang('customform::app.fields.english-name')
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">
                        @lang('customform::app.fields.email')
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">
                        @lang('customform::app.fields.contact-number')
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">
                        @lang('customform::app.fields.refund-type')
                    </th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">
                        @lang('customform::app.actions')
                    </th>
                </tr>
            </thead>

            <tbody>
                @forelse ($submissions as $submission)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                            {{ $submission->created_at->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                            {{ $submission->english_name }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                            {{ $submission->email }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                            {{ $submission->contact_number }}
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">
                            {{ ucfirst($submission->refund_type) }}
                        </td>
                        <td class="px-4 py-3 text-sm">
                            <a
                                href="{{ route('admin.tvd-form.show', $submission->id) }}"
                                class="primary-button py-1 px-3 text-xs"
                            >
                                @lang('customform::app.view')
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            @lang('customform::app.no-submissions')
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $submissions->links() }}
        </div>
    </div>
</x-admin::layouts>
