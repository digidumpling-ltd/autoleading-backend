@php
    $localeLabels  = ['en' => 'EN', 'zh_CN' => '中文', 'zh_TW' => '中文'];
    $currentLabel  = $localeLabels[app()->getLocale()] ?? strtoupper(app()->getLocale());
    $otherLocales  = core()->getAllLocales()->filter(fn ($l) => $l->code !== app()->getLocale());
@endphp

@if ($otherLocales->isNotEmpty())
    <div
        class="al-lang-switcher"
        x-data="{ open: false }"
        x-on:click.outside="open = false"
    >
        <button
            type="button"
            class="al-lang-switcher-btn"
            x-on:click="open = !open"
            :aria-expanded="open"
            aria-haspopup="listbox"
            aria-label="{{ __('auto-leading-theme::app.nav.language') }}"
        >
            <x-heroicon-o-globe-alt class="al-lang-switcher-icon" />
            <span>{{ $currentLabel }}</span>
            <span :class="open ? 'rotate-180' : ''" class="al-lang-switcher-chevron">
                <x-heroicon-o-chevron-down />
            </span>
        </button>

        <ul
            x-show="open"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="al-lang-switcher-menu"
            role="listbox"
            style="display:none;"
        >
            @foreach ($otherLocales as $locale)
                <li role="option">
                    <a
                        href="?locale={{ $locale->code }}"
                        class="al-lang-switcher-option"
                    >
                        {{ $localeLabels[$locale->code] ?? strtoupper($locale->code) }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
@endif
