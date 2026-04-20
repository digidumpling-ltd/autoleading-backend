@props([
    'title' => '',
])

<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ core()->getCurrentLocale()->direction }}">
    <head>
        <title>{{ $title }}</title>

        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="base-url" content="{{ url()->to('/') }}">

        @stack('meta')

        <link rel="icon" sizes="16x16" href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico', 'shop') }}" />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'], 'auto-leading-theme')

        {{-- Alpine.js — same CDN as main layout --}}
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3/dist/cdn.min.js"></script>

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Jost:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

        @stack('styles')
    </head>

    <body class="bg-white font-sans antialiased">
        <div id="app">
            <x-shop::flash-group />

            <div class="al-auth-lang-bar">
                <x-auto-leading-theme::lang-switcher />
            </div>

            {{ $slot }}
        </div>

        @stack('scripts')

        <script>
            window.addEventListener('load', function () {
                app.mount('#app');
            });
        </script>
    </body>
</html>
