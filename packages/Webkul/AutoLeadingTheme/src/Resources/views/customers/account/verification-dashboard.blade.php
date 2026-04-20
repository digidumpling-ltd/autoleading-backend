@php
    $status = $customer->verification_status ?? 'incomplete';

    $statusClassMap = [
        'incomplete' => 'bg-gray-100 text-gray-800',
        'pending'    => 'bg-yellow-100 text-yellow-800',
        'approved'   => 'bg-green-100 text-green-800',
        'rejected'   => 'bg-red-100 text-red-800',
    ];

    $statusTranslationMap = [
        'incomplete' => 'customer-verification::app.common.verification_status_incomplete',
        'pending'    => 'customer-verification::app.common.verification_status_pending',
        'approved'   => 'customer-verification::app.common.verification_status_approved',
        'rejected'   => 'customer-verification::app.common.verification_status_rejected',
    ];

    $statusClass = $statusClassMap[$status] ?? $statusClassMap['incomplete'];

    $statusLabel = __($statusTranslationMap[$status] ?? $statusTranslationMap['incomplete']);
@endphp

<x-shop::layouts.account>
    <x-slot:title>
        @lang('customer-verification::app.common.verification_dashboard_title')
    </x-slot>

    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="profile" />
        @endSection
    @endif

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="mx-4 flex-auto max-md:mx-6 max-sm:mx-4">
        <div class="mb-8 flex items-center max-md:mb-5">
            <a
                class="grid md:hidden"
                href="{{ route('shop.customers.account.index') }}"
            >
                <span class="icon-arrow-left rtl:icon-arrow-right text-2xl"></span>
            </a>

            <h2 class="text-2xl font-medium max-sm:text-base ltr:ml-2.5 md:ltr:ml-0 rtl:mr-2.5 md:rtl:mr-0">
                @lang('customer-verification::app.common.verification_dashboard_title')
            </h2>
        </div>

        @if (session('success'))
            <div class="mb-5 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-5 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                <ul class="list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="rounded-xl border border-zinc-200 p-6 max-sm:p-4">
            <div class="flex items-center justify-between gap-3 max-sm:flex-col max-sm:items-start">
                <p class="text-base font-medium">
                    @lang('customer-verification::app.common.verification_status_label')
                </p>

                <span class="inline-flex rounded-full px-3 py-1 text-sm font-semibold {{ $statusClass }}">
                    {{ $statusLabel }}
                </span>
            </div>

            @if ($status === 'rejected' && $rejectionReason)
                <div class="mt-4 rounded-md border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800">
                    <p class="font-medium">
                        @lang('customer-verification::app.common.verification_rejected_reason')
                    </p>

                    <p class="mt-1">{{ $rejectionReason }}</p>
                </div>
            @endif
        </div>

        <div class="mt-6 rounded-xl border border-zinc-200 p-6 max-sm:p-4">
            <h3 class="text-lg font-medium">
                @lang('customer-verification::app.common.verification_dashboard_documents_title')
            </h3>

            <div class="mt-4 grid gap-4">
                @foreach ($documentMeta as $documentType => $meta)
                    @php
                        $document = $documents->get($documentType);
                        $uploadedAt = $document?->updated_at ?? $document?->created_at;
                    @endphp

                    <div class="rounded-lg border border-zinc-200 p-4">
                        <div class="flex items-center justify-between gap-4 max-sm:flex-col max-sm:items-start">
                            <div>
                                <p class="font-medium">{{ __($meta['label']) }}</p>

                                @if ($document)
                                    <p class="mt-1 text-sm text-zinc-600">
                                        @lang('customer-verification::app.common.verification_uploaded_on'):
                                        {{ \Illuminate\Support\Carbon::parse($uploadedAt)->format('M d, Y H:i') }}
                                    </p>
                                @else
                                    <p class="mt-1 text-sm text-zinc-600">
                                        @lang('customer-verification::app.common.verification_document_missing_hint')
                                    </p>
                                @endif
                            </div>

                            @if ($document)
                                <span class="inline-flex rounded-full bg-green-100 px-2.5 py-1 text-xs font-semibold text-green-700">
                                    @lang('customer-verification::app.common.verification_document_uploaded')
                                </span>
                            @else
                                <span class="inline-flex rounded-full bg-orange-100 px-2.5 py-1 text-xs font-semibold text-orange-700">
                                    @lang('customer-verification::app.common.verification_document_missing')
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="mt-6 rounded-xl border border-zinc-200 p-6 max-sm:p-4">
            <h3 class="text-lg font-medium">
                @lang('customer-verification::app.common.verification_upload_label')
            </h3>

            <p class="mt-1 text-sm text-zinc-600">
                @lang('customer-verification::app.common.verification_upload_hint')
            </p>

            @if (! empty($missingDocumentTypes))
                <x-shop::form
                    class="mt-4"
                    :action="route('shop.customer.verification.upload')"
                    enctype="multipart/form-data"
                >
                    <input
                        type="hidden"
                        name="customer_id"
                        value="{{ $customer->id }}"
                    >

                    <div class="grid gap-4">
                        @foreach ($missingDocumentTypes as $documentType)
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label>
                                    {{ __($documentMeta[$documentType]['label']) }}
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="file"
                                    name="{{ $documentType }}"
                                    accept="{{ $documentMeta[$documentType]['accept'] }}"
                                />

                                <p class="mt-1 text-xs text-zinc-500">
                                    {{ __($documentMeta[$documentType]['hint']) }}
                                </p>

                                <x-shop::form.control-group.error control-name="{{ $documentType }}" />
                            </x-shop::form.control-group>
                        @endforeach

                        <x-shop::form.control-group.error control-name="documents" />
                    </div>

                    <button
                        type="submit"
                        class="primary-button mt-4 rounded-lg px-5 py-2.5"
                    >
                        @lang('customer-verification::app.common.verification_upload_button')
                    </button>
                </x-shop::form>
            @else
                <p class="mt-4 rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
                    @lang('customer-verification::app.common.verification_no_documents_to_upload')
                </p>
            @endif
        </div>
    </div>
</x-shop::layouts.account>
