<x-admin::layouts>
    <x-slot:title>
        {{ $customer->first_name }} {{ $customer->last_name }}
    </x-slot>

    <div class="flex items-center justify-between gap-4 mb-5 max-sm:flex-wrap">
        <div class="grid gap-1.5">
            <p class="text-xl font-bold leading-normal text-gray-800 dark:text-white">
                {{ $customer->first_name }} {{ $customer->last_name }}
            </p>
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ $customer->email }}</p>
            @if($customer->reference_number)
                <p class="text-xs font-mono text-gray-400 dark:text-gray-500">{{ trans('customer-verification::app.common.reference') }}: <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $customer->reference_number }}</span></p>
            @endif
        </div>

        <a href="{{ route('admin.verification.index') }}" class="secondary-button">
            {{ trans('customer-verification::app.common.back') }}
        </a>
    </div>

    {{-- Customer Info --}}
    <div class="box-shadow rounded-xl border border-gray-200 bg-white p-6 mb-5 dark:border-gray-800 dark:bg-gray-900">
        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">{{ trans('customer-verification::app.common.customer_information') }}</p>

        <div class="grid grid-cols-2 gap-4 text-sm">
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">{{ trans('customer-verification::app.common.verification_status_label') }}:</span>
                <span class="ml-2 rounded px-2 py-1 text-xs font-medium
                    {{ $customer->verification_status === 'approved' ? 'bg-green-100 text-green-700' : ($customer->verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ trans('customer-verification::app.common.verification_status_' . ($customer->verification_status ?? 'incomplete')) }}
                </span>
            </div>
            <div>
                <span class="font-medium text-gray-600 dark:text-gray-400">{{ trans('customer-verification::app.common.submitted_date') }}:</span>
                <span class="ml-2 text-gray-800 dark:text-gray-300">{{ $customer->created_at->format('Y-m-d H:i') }}</span>
            </div>
            @if($customer->rejection_reason)
                <div class="col-span-2">
                    <span class="font-medium text-gray-600 dark:text-gray-400">{{ trans('customer-verification::app.common.rejection_reason') }}:</span>
                    <span class="ml-2 text-gray-800 dark:text-gray-300">{{ $customer->rejection_reason }}</span>
                </div>
            @endif
        </div>
    </div>

    {{-- Documents --}}
    <div class="box-shadow rounded-xl border border-gray-200 bg-white p-6 mb-5 dark:border-gray-800 dark:bg-gray-900">
        <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">{{ trans('customer-verification::app.common.uploaded_documents') }}</p>

        <div class="grid grid-cols-3 gap-4 max-md:grid-cols-1">
            @foreach(['id_document', 'driver_license', 'address_proof'] as $docType)
                @php $doc = $documents->firstWhere('type', $docType); @endphp
                <div class="rounded-lg border border-gray-200 p-4 dark:border-gray-700">
                    <p class="mb-3 text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                    </p>
                    @if($doc)
                        @php $storageDisk = config('filesystems.default'); @endphp
                        @if(in_array(pathinfo($doc->path, PATHINFO_EXTENSION), ['png', 'jpg', 'jpeg', 'webp']))
                            <img src="{{ Storage::disk($storageDisk)->url($doc->path) }}" alt="{{ $doc->original_name }}" class="mb-2 max-h-48 w-full rounded object-contain">
                        @else
                            <p class="mb-2 text-xs text-gray-500">{{ $doc->original_name }}</p>
                            <a href="{{ Storage::disk($storageDisk)->url($doc->path) }}" target="_blank" class="secondary-button py-1 px-3 text-xs">
                                {{ trans('customer-verification::app.common.download') }}
                            </a>
                        @endif
                        <p class="mt-2 text-xs text-gray-400">{{ trans('customer-verification::app.common.uploaded_at') }}: {{ $doc->created_at->format('Y-m-d H:i') }}</p>
                    @else
                        <p class="text-sm text-gray-400 italic">{{ trans('customer-verification::app.common.not_uploaded') }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    @if($customer->verification_status !== 'approved')
        <div class="box-shadow rounded-xl border border-gray-200 bg-white p-6 dark:border-gray-800 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">{{ trans('customer-verification::app.common.verification_actions') }}</p>

            <div class="flex gap-6 max-md:flex-col">
                <form action="{{ route('admin.verification.approve', $customer->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="primary-button">
                        {{ trans('customer-verification::app.common.approve_button') }}
                    </button>
                </form>

                <form action="{{ route('admin.verification.reject', $customer->id) }}" method="POST" class="flex-1">
                    @csrf
                    <div class="mb-3">
                        <label class="mb-1.5 block text-sm font-medium text-gray-600 dark:text-gray-400">
                            {{ trans('customer-verification::app.common.rejection_reason') }}
                        </label>
                        <textarea name="rejection_reason" rows="3" required
                            class="w-full rounded-md border border-gray-200 px-3 py-2 text-sm dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                            placeholder="{{ trans('customer-verification::app.common.rejection_reason_placeholder') }}"></textarea>
                        @error('rejection_reason')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="bg-red-600 hover:bg-red-700 rounded-md px-4 py-2 text-sm font-semibold text-white">
                        {{ trans('customer-verification::app.common.reject_button') }}
                    </button>
                </form>
            </div>
        </div>
    @endif
</x-admin::layouts>
