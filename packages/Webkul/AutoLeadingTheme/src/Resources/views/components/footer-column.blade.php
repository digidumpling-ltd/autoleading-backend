@props([
    'heading',
    'links' => [],
])

<div class="al-footer-col">
    <h3 class="al-footer-heading">{{ $heading }}</h3>

    @if (count($links))
        <ul class="al-footer-links">
            @foreach ($links as $link)
                <li>
                    <a href="{{ $link['url'] }}">{{ $link['label'] }}</a>
                </li>
            @endforeach
        </ul>
    @endif

    {{ $slot }}
</div>
