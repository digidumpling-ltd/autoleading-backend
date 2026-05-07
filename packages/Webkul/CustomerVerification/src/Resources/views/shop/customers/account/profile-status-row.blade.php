@php
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

<div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
    <p class="text-sm font-medium">
        @lang('customer-verification::app.common.verification_status_label')
    </p>

    <p class="text-sm font-medium text-zinc-500">
        <span class="inline-block rounded px-2 py-0.5 text-xs font-semibold {{ $statusClass }}">
            {{ $statusLabel }}
        </span>
    </p>
</div>
