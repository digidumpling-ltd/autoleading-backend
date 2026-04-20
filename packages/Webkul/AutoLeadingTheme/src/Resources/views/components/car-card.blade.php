@props([
    'name',
    'price',
    'url',
    'badge' => null,
    'image' => null,
    'tag'   => null,
])

<article class="al-car-card">
    <a href="{{ $url }}" class="al-car-thumb" aria-label="{{ $name }}">
        @if ($badge)
            <span class="al-car-badge">{{ $badge }}</span>
        @endif

        @if ($image)
            <img src="{{ $image }}" alt="{{ $name }}" loading="lazy">
        @else
            <span class="al-car-mark">{{ mb_substr($name, 0, 1) }}</span>
        @endif
    </a>

    <div class="al-car-body">
        <h3>{{ $name }}</h3>

        <p class="al-car-price">
            <small>{{ __('auto-leading-theme::app.home.from') }}</small>
            {{ $price }}
        </p>

        @if ($tag)
            <p class="al-car-tag">{{ $tag }}</p>
        @endif

        <a href="{{ $url }}" class="al-car-cta">
            {{ __('auto-leading-theme::app.common.view_car') }}
        </a>
    </div>
</article>
