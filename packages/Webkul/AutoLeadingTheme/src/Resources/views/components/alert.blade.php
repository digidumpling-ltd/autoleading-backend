@props([
    'type'    => 'success',
    'message' => '',
])

<div
    class="al-alert al-alert--{{ $type }}"
    role="alert"
>
    @if ($type === 'success')
        <x-heroicon-o-check-circle class="al-alert-icon" />
    @else
        <x-heroicon-o-x-circle class="al-alert-icon" />
    @endif

    <p>{{ $message }}</p>
</div>
