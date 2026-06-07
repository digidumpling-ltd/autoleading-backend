<x-admin::layouts>
    <x-slot:title>
        {{ $customer->first_name }} {{ $customer->last_name }}
    </x-slot>

    {{-- Page Header --}}
    <div class="grid">
        <div class="flex items-center justify-between gap-4 max-sm:flex-wrap">
            <div class="flex items-center gap-2.5">
                <p class="text-xl font-bold leading-6 text-gray-800 dark:text-white">
                    {{ $customer->first_name }} {{ $customer->last_name }}
                </p>

                <span class="rounded px-2 py-1 text-xs font-medium
                    {{ $customer->verification_status === 'approved' ? 'bg-green-100 text-green-700' : ($customer->verification_status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                    {{ trans('customer-verification::app.common.verification_status_' . ($customer->verification_status ?? 'incomplete')) }}
                </span>
            </div>

            <a href="{{ route('admin.verification.index') }}" class="transparent-button hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
                {{ trans('customer-verification::app.common.back') }}
            </a>
        </div>
    </div>

    {{-- Customer Info Card --}}
    <div class="box-shadow mt-5 rounded bg-white dark:bg-gray-900">
        <p class="p-4 pb-0 text-base font-semibold text-gray-800 dark:text-white">
            {{ trans('customer-verification::app.common.customer_information') }}
        </p>

        <div class="grid grid-cols-3 gap-4 p-4 max-sm:grid-cols-1">
            <div>
                <p class="font-medium text-gray-400">{{ trans('customer-verification::app.common.customer_name') }}</p>
                <p class="mt-0.5 text-gray-800 dark:text-gray-300">{{ $customer->first_name }} {{ $customer->last_name }}</p>
            </div>

            <div>
                <p class="font-medium text-gray-400">{{ trans('customer-verification::app.common.email') }}</p>
                <p class="mt-0.5 text-gray-800 dark:text-gray-300">{{ $customer->email }}</p>
            </div>

            <div>
                <p class="font-medium text-gray-400">{{ trans('customer-verification::app.common.submitted_date') }}</p>
                <p class="mt-0.5 text-gray-800 dark:text-gray-300">{{ $customer->created_at->format('Y-m-d H:i') }}</p>
            </div>

            @if ($customer->reference_number)
                <div>
                    <p class="font-medium text-gray-400">{{ trans('customer-verification::app.common.reference_number') }}</p>
                    <p class="mt-0.5 font-mono text-gray-800 dark:text-gray-300">{{ $customer->reference_number }}</p>
                </div>
            @endif

            @if ($customer->rejection_reason)
                <div style="grid-column:span 3">
                    <p class="font-medium text-gray-400">{{ trans('customer-verification::app.common.rejection_reason') }}</p>
                    <p class="mt-0.5 text-gray-800 dark:text-gray-300">{{ $customer->rejection_reason }}</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Documents --}}
    <div class="box-shadow mt-5 rounded bg-white dark:bg-gray-900">
        <p class="p-4 pb-0 text-base font-semibold text-gray-800 dark:text-white">
            {{ trans('customer-verification::app.common.uploaded_documents') }}
        </p>

        <div class="grid grid-cols-3 gap-4 p-4 max-xl:grid-cols-2 max-sm:grid-cols-1">
            @foreach(['id_document', 'driver_license', 'address_proof'] as $docType)
                @php $doc = $documents->firstWhere('type', $docType); @endphp

                <div class="group relative rounded border border-gray-200 p-4 transition-all hover:shadow dark:border-gray-700">
                    @if ($doc)
                        @php $storageDisk = config('filesystems.default'); $ext = strtolower(pathinfo($doc->path, PATHINFO_EXTENSION)); @endphp

                        {{-- Delete button — top right, fades in on hover --}}
                        @if ($customer->verification_status !== 'approved')
                            <form method="POST" action="{{ route('admin.verification.document.destroy', [$customer->id, $doc->id]) }}"
                                  class="absolute right-2 top-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="icon-delete opacity-0 group-hover:opacity-100 cursor-pointer rounded-md p-1 text-xl text-red-600 transition-all hover:bg-gray-100 dark:hover:bg-gray-800"
                                        onclick="return confirm('{{ trans('customer-verification::app.common.confirm_delete_document') }}')">
                                </button>
                            </form>
                        @endif

                        <div class="flex items-start gap-3">
                            {{-- Thumbnail --}}
                            <div class="shrink-0">
                                @if (in_array($ext, ['png', 'jpg', 'jpeg', 'webp']))
                                    <img src="{{ Storage::disk($storageDisk)->url($doc->path) }}" alt="{{ $doc->original_name }}" class="h-16 w-16 rounded object-cover">
                                @else
                                    <span class="icon-folder text-gray-400" style="font-size:3rem;line-height:1"></span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="min-w-0 flex-1">
                                <p class="mb-1 font-medium text-gray-400">
                                    {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                                </p>
                                <a href="{{ Storage::disk($storageDisk)->url($doc->path) }}" target="_blank" rel="noopener noreferrer"
                                   class="block truncate font-semibold text-blue-600 hover:underline dark:text-blue-400">
                                    {{ $doc->original_name }}
                                </a>
                                <p class="mt-0.5 text-gray-400">{{ $doc->created_at->format('Y-m-d H:i') }}</p>
                            </div>
                        </div>
                    @else
                        <p class="font-semibold text-gray-800 dark:text-white">
                            {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                        </p>
                        <p class="mt-1 italic text-gray-400">{{ trans('customer-verification::app.common.not_uploaded') }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    {{-- Actions --}}
    @if ($customer->verification_status !== 'approved')
        <div class="box-shadow mt-5 rounded bg-white dark:bg-gray-900">
            <p class="p-4 pb-0 text-base font-semibold text-gray-800 dark:text-white">
                {{ trans('customer-verification::app.common.verification_actions') }}
            </p>

            <div class="flex flex-col gap-4 p-4">
                <div>
                    <label class="mb-1.5 block font-medium text-gray-600 dark:text-gray-400">
                        {{ trans('customer-verification::app.common.rejection_reason') }}
                    </label>
                    <textarea name="rejection_reason" form="reject-form" rows="3" required
                        class="w-full rounded border border-gray-200 px-3 py-2 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300"
                        placeholder="{{ trans('customer-verification::app.common.rejection_reason_placeholder') }}"></textarea>
                    @error('rejection_reason')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3">
                    <form action="{{ route('admin.verification.approve', $customer->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="primary-button">
                            {{ trans('customer-verification::app.common.approve_button') }}
                        </button>
                    </form>

                    <form id="reject-form" action="{{ route('admin.verification.reject', $customer->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="primary-button" style="background-color:#dc2626;border-color:#dc2626;">
                            {{ trans('customer-verification::app.common.reject_button') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @endif
</x-admin::layouts>
