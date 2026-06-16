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
        <p class="p-4 pb-2 text-base font-semibold text-gray-800 dark:text-white">
            {{ trans('customer-verification::app.common.uploaded_documents') }}
        </p>

        <div class="flex flex-wrap gap-1 p-4">
            @foreach(['id_document', 'driver_license', 'address_proof'] as $docType)
                @php
                    $doc = $documents->firstWhere('type', $docType);
                    $storageDisk = config('filesystems.default');
                    $ext = $doc ? strtolower(pathinfo($doc->path, PATHINFO_EXTENSION)) : null;
                    $isImage = $ext && in_array($ext, ['png', 'jpg', 'jpeg', 'webp']);
                    $canEdit = $customer->verification_status !== 'approved';
                @endphp

                @if ($doc)
                    {{-- Uploaded card --}}
                    <div class="group relative grid w-[140px] justify-items-center overflow-hidden rounded border border-gray-200 transition-all hover:border-gray-400 dark:border-gray-700" style="height:140px;">

                        {{-- Content --}}
                        <div class="flex w-full flex-col items-center justify-center gap-1 overflow-hidden p-2" style="height:140px;">
                            @if ($isImage)
                                <img src="{{ Storage::disk($storageDisk)->url($doc->path) }}"
                                     class="rounded object-cover" style="width:80px;height:80px;flex-shrink:0;" alt="">
                            @else
                                <span class="icon-clip text-gray-400 dark:text-gray-500" style="font-size:4rem;line-height:1;flex-shrink:0;"></span>
                            @endif

                            <p class="w-full truncate text-center text-xs font-semibold text-gray-600 dark:text-gray-300"
                               title="{{ $doc->original_name }}">
                                {{ $doc->original_name }}
                            </p>

                            <p class="w-full truncate text-center text-xs text-gray-400 dark:text-gray-500">
                                {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                            </p>
                        </div>

                        {{-- Hover overlay — matches product image item style --}}
                        <div class="invisible absolute bottom-0 top-0 flex w-full flex-col justify-between bg-white p-3 opacity-80 transition-all group-hover:visible dark:bg-gray-900">
                            <p class="break-all text-xs font-semibold text-gray-600 dark:text-gray-300">
                                {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                            </p>

                            <div class="flex justify-between">
                                {{-- View --}}
                                <a href="{{ Storage::disk($storageDisk)->url($doc->path) }}"
                                   target="_blank" rel="noopener noreferrer"
                                   class="icon-view cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800">
                                </a>

                                @if ($canEdit)
                                    {{-- Re-upload --}}
                                    <form method="POST"
                                          action="{{ route('admin.verification.document.upload', [$customer->id, $docType]) }}"
                                          enctype="multipart/form-data"
                                          style="display:contents;">
                                        @csrf
                                        <label class="icon-edit cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800">
                                            <input type="file" name="document" class="hidden"
                                                   accept="image/png,image/jpeg,image/webp,application/pdf"
                                                   onchange="this.closest('form').submit()">
                                        </label>
                                    </form>

                                    {{-- Delete --}}
                                    <form method="POST"
                                          action="{{ route('admin.verification.document.destroy', [$customer->id, $doc->id]) }}"
                                          style="display:contents;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl hover:bg-gray-200 dark:hover:bg-gray-800"
                                                onclick="return confirm('{{ trans('customer-verification::app.common.confirm_delete_document') }}')">
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>

                @else
                    {{-- Empty card — whole card is the upload trigger --}}
                    @if ($canEdit)
                        <form method="POST"
                              action="{{ route('admin.verification.document.upload', [$customer->id, $docType]) }}"
                              enctype="multipart/form-data">
                            @csrf
                            <label class="grid w-[140px] cursor-pointer items-center justify-items-center rounded border border-dashed border-gray-300 transition-all hover:border-gray-400 dark:border-gray-800 dark:mix-blend-exclusion dark:invert" style="height:140px;">
                                <div class="flex flex-col items-center gap-1 p-2">
                                    <span class="icon-clip" style="font-size:4rem;line-height:1;color:#d1d5db;"></span>
                                    <p class="text-center text-xs font-semibold text-gray-400">
                                        {{ trans('customer-verification::app.common.not_uploaded') }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                                    </p>
                                </div>
                                <input type="file" name="document" class="hidden"
                                       accept="image/png,image/jpeg,image/webp,application/pdf"
                                       onchange="this.closest('form').submit()">
                            </label>
                        </form>
                    @else
                        <div class="grid w-[140px] items-center justify-items-center rounded border border-dashed border-gray-200 dark:border-gray-700" style="height:140px;">
                            <div class="flex flex-col items-center gap-1 p-2">
                                <span class="icon-clip" style="font-size:4rem;line-height:1;color:#d1d5db;"></span>
                                <p class="text-center text-xs font-semibold text-gray-400">
                                    {{ trans('customer-verification::app.common.not_uploaded') }}
                                </p>
                                <p class="text-xs text-gray-400">
                                    {{ trans('customer-verification::app.common.document_type_' . $docType) }}
                                </p>
                            </div>
                        </div>
                    @endif
                @endif
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
