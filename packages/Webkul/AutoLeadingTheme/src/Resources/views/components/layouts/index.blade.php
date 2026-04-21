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
        <meta name="currency" content="{{ core()->getCurrentCurrency()->toJson() }}">
        <meta name="currency-code" content="{{ core()->getCurrentCurrencyCode() }}">
        <meta name="symbol" content="{{ core()->getCurrentCurrency()->symbol }}">

        @stack('meta')

        <link rel="icon" sizes="16x16" href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico', 'shop') }}" />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'], 'auto-leading-theme')

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
            <x-shop::modal.confirm />

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

            <v-al-auth-dialog></v-al-auth-dialog>
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        @stack('scripts')

        <script type="text/x-template" id="v-al-auth-dialog-template">
            <div
                v-show="open"
                class="fixed inset-0 z-[200] flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="al-auth-dialog-title"
            >
                <div @click="close" aria-hidden="true" class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

                <div class="relative z-10 bg-white rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden">
                    <div class="flex items-center justify-between px-6 pt-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 bg-amber-500/10">
                                <span class="text-amber-500 text-xl icon-lock"></span>
                            </div>
                            <h2 id="al-auth-dialog-title" class="text-base font-bold text-gray-900">
                                @{{ authDialogTitle }}
                            </h2>
                        </div>
                        <button
                            type="button"
                            @click="close"
                            class="w-8 h-8 rounded-full flex items-center justify-center text-gray-400 hover:text-gray-600 hover:bg-gray-100 transition-colors border-0 bg-transparent cursor-pointer"
                        >
                            <span class="icon-cancel text-lg"></span>
                        </button>
                    </div>

                    <div class="px-6 py-5 flex flex-col gap-3">
                        <p class="text-sm text-gray-500">@{{ authDialogMessage }}</p>

                        <a
                            :href="loginUrl"
                            class="al-car-cta justify-center gap-2 rounded-xl px-6 py-3"
                        >
                            @{{ loginLabel }}
                        </a>

                        <a
                            :href="registerUrl"
                            class="flex items-center justify-center gap-2 rounded-xl px-6 py-3 border-2 border-gray-200 text-gray-700 font-bold text-xs uppercase tracking-wide no-underline hover:border-amber-500 hover:text-amber-500 transition-colors"
                        >
                            @{{ registerLabel }}
                        </a>
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-al-auth-dialog', {
                template: '#v-al-auth-dialog-template',

                data() {
                    return {
                        open: false,
                        authDialogTitle:   "{{ __('auto-leading-theme::app.auth_dialog.title') }}",
                        authDialogMessage: "{{ __('auto-leading-theme::app.auth_dialog.message') }}",
                        loginLabel:        "{{ __('auto-leading-theme::app.common.login') }}",
                        registerLabel:     "{{ __('auto-leading-theme::app.common.register') }}",
                        loginUrl:          "{{ route('shop.customer.session.index') }}",
                        registerUrl:       "{{ route('shop.customers.register.index') }}",
                    };
                },

                mounted() {
                    window.addEventListener('open-auth-dialog', () => { this.open = true; });

                    window.addEventListener('keydown', (e) => {
                        if (e.key === 'Escape') this.close();
                    });
                },

                methods: {
                    close() { this.open = false; },
                },
            });
        </script>

        <script>
            function alOpenAuthDialog() {
                window.dispatchEvent(new CustomEvent('open-auth-dialog'));
            }

            window.addEventListener("load", function () {
                app.mount("#app");

                if (typeof alInitNavbar === 'function') alInitNavbar();
                if (typeof alUpdateCartCount === 'function') {
                    alUpdateCartCount();
                    emitter.on('update-mini-cart', alUpdateCartCount);
                }
            });
        </script>
    </body>
</html>
