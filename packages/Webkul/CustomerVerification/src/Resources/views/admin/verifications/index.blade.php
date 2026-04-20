<x-admin::layouts>
    <x-slot:title>
        {{ trans('customer-verification::app.common.manage_verifications') }}
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <p class="text-xl font-bold leading-normal text-gray-800 dark:text-white">
                {{ trans('customer-verification::app.common.manage_verifications') }}
            </p>
        </div>
    </div>

    <div class="flex gap-4 mb-4">
        <form method="GET" action="{{ route('admin.verification.index') }}" class="flex gap-3 w-full flex-wrap">
            <input 
                type="text" 
                name="search" 
                value="{{ $search ?? '' }}"
                placeholder="{{ trans('customer-verification::app.common.search_by_reference_email_name_phone') }}"
                class="rounded-md border border-gray-200 px-3 py-2 text-sm flex-1 min-w-xs dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
            />

            <select name="status" class="rounded-md border border-gray-200 px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300">
                <option value="all">{{ trans('customer-verification::app.common.all_status') }}</option>
                <option value="incomplete" @selected($filter === 'incomplete')>{{ trans('customer-verification::app.common.verification_status_incomplete') }}</option>
                <option value="pending" @selected($filter === 'pending')>{{ trans('customer-verification::app.common.verification_status_pending') }}</option>
                <option value="approved" @selected($filter === 'approved')>{{ trans('customer-verification::app.common.verification_status_approved') }}</option>
                <option value="rejected" @selected($filter === 'rejected')>{{ trans('customer-verification::app.common.verification_status_rejected') }}</option>
            </select>

            <button type="submit" class="primary-button">
                {{ trans('customer-verification::app.common.filter') }}
            </button>

            @if($search || $filter !== 'all')
                <a href="{{ route('admin.verification.index') }}" class="secondary-button">
                    {{ trans('customer-verification::app.common.clear') }}
                </a>
            @endif
        </form>
    </div>

    <div class="box-shadow rounded-xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900">
        <table class="w-full table-auto">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.reference_number') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.customer_name') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.email') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.verification_status_label') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.documents_uploaded') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.submitted_date') }}</th>
                    <th class="px-4 py-3 text-left text-sm font-semibold text-gray-600 dark:text-gray-300">{{ trans('customer-verification::app.common.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                    <tr class="border-b border-gray-100 dark:border-gray-800">
                        <td class="px-4 py-3 text-sm font-mono text-gray-800 dark:text-gray-300">
                            <span class="bg-blue-50 dark:bg-blue-900 px-2 py-1 rounded text-blue-700 dark:text-blue-300">
                                {{ $customer->reference_number ?? 'N/A' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">{{ $customer->first_name }} {{ $customer->last_name }}</td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">{{ $customer->email }}</td>
                        <td class="px-4 py-3 text-sm">
                            <span class="rounded px-2 py-1 text-xs font-medium
                                {{ $customer->verification_status === 'approved' ? 'bg-green-100 text-green-700' : ($customer->verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ trans('customer-verification::app.common.verification_status_' . ($customer->verification_status ?? 'incomplete')) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">{{ $customer->verificationDocuments->count() }}/3</td>
                        <td class="px-4 py-3 text-sm text-gray-800 dark:text-gray-300">{{ $customer->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 text-sm">
                            <a href="{{ route('admin.verification.show', $customer->id) }}" class="primary-button py-1 px-3 text-xs">
                                {{ trans('customer-verification::app.common.view') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-6 text-center text-sm text-gray-500 dark:text-gray-400">
                            {{ trans('customer-verification::app.common.no_customers_found') }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4">
            {{ $customers->links() }}
        </div>
    </div>
</x-admin::layouts>
