@props([
    'hasFeature' => false,
    'hasHeader'  => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">
    <head>
        <title>{{ $title ?? '' }}</title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ url()->to('/') }}">
        <meta name="currency-code" content="{{ core()->getCurrentCurrencyCode() }}">
        <meta name="symbol" content="{{ core()->getCurrentCurrency()->symbol }}">

        @stack('meta')

        <link rel="icon" sizes="16x16" href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico', 'shop') }}" />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'], 'auto-leading-theme')

        {{-- Alpine.js — loaded via CDN, independent of Vite/symlink --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

        @stack('styles')

        <style>
            {!! core()->getConfigData('general.design.admin_logo.custom_css') !!}
        </style>

        {!! view_render_event('bagisto.shop.layout.head.after') !!}
    </head>

    <body class="bg-[#ffffff] font-sans antialiased overflow-x-hidden">
        {!! view_render_event('bagisto.shop.layout.body.before') !!}

        <div id="app" class="al-site min-h-screen flex flex-col">
            <x-shop::flash-group />

            @if ($hasHeader)
                <x-auto-leading-theme::layouts.header />
            @endif

            {!! view_render_event('bagisto.shop.layout.content.before') !!}

            <main class="flex-grow">
                {{ $slot }}
            </main>

            {!! view_render_event('bagisto.shop.layout.content.after') !!}

            @if ($hasFooter)
                <x-auto-leading-theme::layouts.footer />
            @endif
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        @stack('scripts')

        {{-- Auth dialog --}}
        <div
            x-data="{ open: false }"
            x-on:open-auth-dialog.window="open = true"
            x-on:keydown.escape.window="open = false"
            x-show="open"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-[200] flex items-center justify-center p-4"
            style="display:none;"
            role="dialog"
            aria-modal="true"
            aria-labelledby="al-auth-dialog-title"
        >
            {{-- Backdrop --}}
            <div
                x-on:click="open = false"
                aria-hidden="true"
                class="absolute inset-0 bg-black/60 backdrop-blur-sm"
            ></div>

            {{-- Panel --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
            >
                {{-- Header --}}
                <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-amber-500/10">
                            <x-heroicon-o-lock-closed class="w-5 h-5 text-amber-500" />
                        </div>
                        <h2 id="al-auth-dialog-title" class="text-base font-bold text-gray-900">
                            {{ __('auto-leading-theme::app.auth_dialog.title') }}
                        </h2>
                    </div>
                    <button
                        type="button"
                        x-on:click="open = false"
                        class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors border-0 bg-transparent cursor-pointer"
                        aria-label="{{ __('auto-leading-theme::app.common.close') }}"
                    >
                        <x-heroicon-o-x-mark class="w-4 h-4" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-6 py-5 flex flex-col gap-3">
                    <p class="text-sm text-gray-500">
                        {{ __('auto-leading-theme::app.auth_dialog.message') }}
                    </p>

                    <a
                        href="{{ route('shop.customer.session.index') }}"
                        class="al-car-cta justify-center gap-2 rounded-xl px-6 py-3"
                    >
                        <x-heroicon-o-arrow-right-on-rectangle class="w-4 h-4 shrink-0" />
                        {{ __('auto-leading-theme::app.common.login') }}
                    </a>

                    <a
                        href="{{ route('shop.customers.register.index') }}"
                        class="flex items-center justify-center gap-2 rounded-xl px-6 py-3 border-2 border-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wide no-underline hover:border-amber-500 hover:text-amber-500 transition-colors"
                    >
                        <x-heroicon-o-user-plus class="w-4 h-4 shrink-0" />
                        {{ __('auto-leading-theme::app.common.register') }}
                    </a>
                </div>
            </div>
        </div>

        <script>
            // Legacy bridge — car-card Alpine components call these plain JS functions
            function alOpenAuthDialog() {
                window.dispatchEvent(new CustomEvent('open-auth-dialog'));
            }
            function alCloseAuthDialog() {
                // handled by Alpine x-on:keydown.escape / backdrop click
            }
        </script>
    </body>
</html>
