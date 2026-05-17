<header class="al-header">
    <div class="al-header-shell">

        {{-- Logo --}}
        <a href="{{ route('shop.home.index') }}" class="al-logo-link flex-col items-center">
            @php $channelLogo = core()->getCurrentChannel()->logo_url; @endphp
            @if ($channelLogo)
                <img src="{{ $channelLogo }}" alt="{{ core()->getCurrentChannel()->name }}" class="h-10 w-auto" />
            @else
                <img src="{{ bagisto_asset('images/logo.svg', 'auto-leading-theme') }}" alt="AutoLeading Logo" class="h-10 w-auto" />
            @endif
            <span class="al-logo-wordmark">AUTO LEADING</span>
        </a>

        {{-- Desktop Navigation --}}
        <nav class="al-nav hidden lg:flex" aria-label="{{ __('auto-leading-theme::app.nav.main_navigation') }}">
            <a href="{{ route('shop.home.index') }}" class="{{ request()->routeIs('shop.home.index') ? 'is-active' : '' }}">
                {{ __('auto-leading-theme::app.nav.home') }}
            </a>
            <a href="#">{{ __('auto-leading-theme::app.nav.cross_border') }}</a>
            <a href="#">{{ __('auto-leading-theme::app.nav.about') }}</a>
            <a href="{{ route('shop.search.index') }}" class="{{ request()->routeIs('shop.search.index') ? 'is-active' : '' }}">
                {{ __('auto-leading-theme::app.nav.models') }}
            </a>
            <a href="#">{{ __('auto-leading-theme::app.nav.notice') }}</a>
            <a href="#">{{ __('auto-leading-theme::app.nav.membership') }}</a>
            <a href="#">{{ __('auto-leading-theme::app.nav.tvd') }}</a>
        </nav>

        {{-- Header Actions --}}
        <div class="al-header-actions">
            <div class="al-auth-links hidden lg:flex items-center">

                @guest('customer')
                    <a href="{{ route('shop.customer.session.index') }}" class="al-auth-link flex items-center gap-1">
                        <x-heroicon-o-user class="w-6 h-6 al-text-accent" />
                        {{ __('auto-leading-theme::app.common.login') }}
                    </a>

                    <a href="{{ route('shop.customers.register.index') }}" class="al-auth-link">
                        {{ __('auto-leading-theme::app.common.register') }}
                    </a>
                @endguest

                @auth('customer')
                    <div class="al-dropdown">
                        <button
                            type="button"
                            class="al-auth-link flex items-center gap-1 al-dropdown-toggle"
                            aria-expanded="false"
                        >
                            <x-heroicon-o-user-circle class="w-6 h-6 al-text-accent" />
                            <span class="max-w-[100px] truncate">{{ auth()->guard('customer')->user()->first_name }}</span>
                        </button>
                        <div class="al-dropdown-menu" style="display:none">
                            <a href="{{ route('shop.customers.account.profile.index') }}" class="al-dropdown-item">
                                {{ __('auto-leading-theme::app.common.profile') }}
                            </a>
                            <a href="{{ route('shop.customers.account.orders.index') }}" class="al-dropdown-item">
                                {{ __('auto-leading-theme::app.common.orders') }}
                            </a>
                            <div class="border-t border-white/10 my-1"></div>
                            <form action="{{ route('shop.customer.session.destroy') }}" method="POST">
                                @method('DELETE')
                                @csrf
                                <button type="submit" class="al-dropdown-item w-full text-left text-red-400 cursor-pointer">
                                    {{ __('auto-leading-theme::app.common.logout') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @endauth

                {{-- Language Switcher (visible to all users when channel has >1 locale) --}}
                @php
                    $localeLabels   = ['en' => 'EN', 'zh_CN' => '中文', 'zh_TW' => '中文'];
                    $currentLabel   = $localeLabels[app()->getLocale()] ?? strtoupper(app()->getLocale());
                    $switcherLocales = core()->getCurrentChannel()->locales()
                        ->where('code', '!=', app()->getLocale())
                        ->get();
                @endphp
                @if($switcherLocales->count() > 0)
                    <div class="al-dropdown">
                        <button
                            type="button"
                            class="al-auth-link flex items-center gap-1 al-dropdown-toggle"
                            aria-expanded="false"
                            aria-label="{{ __('auto-leading-theme::app.nav.language') }}"
                        >
                            <x-heroicon-o-globe-alt class="w-6 h-6 al-text-accent" />
                            <span class="font-medium">{{ $currentLabel }}</span>
                        </button>
                        <div class="al-dropdown-menu" style="display:none">
                            @foreach($switcherLocales as $locale)
                                <a href="?locale={{ $locale->code }}" class="al-dropdown-item">
                                    {{ $localeLabels[$locale->code] ?? strtoupper($locale->code) }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Cart Button --}}
                <a
                    href="{{ route('shop.checkout.cart.index') }}"
                    class="al-auth-link relative flex items-center gap-1"
                    aria-label="{{ __('auto-leading-theme::app.common.cart') }}"
                >
                    <x-heroicon-o-shopping-cart class="w-6 h-6 al-text-accent" />
                    <span
                        id="al-cart-count"
                        class="absolute -top-2 -right-2 min-w-[18px] h-[18px] rounded-full bg-[#c9a84c] text-[10px] font-bold leading-[18px] text-center text-black px-1"
                        style="display:none"
                    ></span>
                </a>

            </div>

            {{-- Mobile Toggle --}}
            <button
                type="button"
                class="al-hamburger lg:hidden al-mobile-toggle"
                aria-expanded="false"
                aria-label="{{ __('auto-leading-theme::app.common.menu') }}"
            >
                <x-heroicon-o-bars-3 class="w-7 h-7 text-white al-icon-menu" />
                <x-heroicon-o-x-mark class="w-7 h-7 text-white al-icon-close" style="display:none" />
            </button>
        </div>

    </div>

    {{-- Mobile Drawer --}}
    <div class="al-mobile-drawer lg:hidden border-t border-white/10 bg-[#0d0d0d]" style="display:none">
        <div class="px-6 py-8 flex flex-col gap-6">
            <a href="{{ route('shop.home.index') }}" class="text-xl font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.home') }}</a>
            <a href="#" class="text-xl font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.cross_border') }}</a>
            <a href="#" class="text-xl font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.about') }}</a>
            <a href="{{ route('shop.search.index') }}" class="text-xl font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.models') }}</a>
            <a href="#" class="text-xl font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.notice') }}</a>
            <a href="#" class="text-xl font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.membership') }}</a>
            <a href="#" class="text-base font-medium text-white hover:al-text-accent transition-colors">{{ __('auto-leading-theme::app.nav.tvd') }}</a>

            <div class="mt-4 pt-6 border-t border-white/5 flex flex-col gap-4">
                @guest('customer')
                    <a href="{{ route('shop.customer.session.index') }}" class="flex items-center al-text-accent font-semibold">
                        {{ __('auto-leading-theme::app.common.login') }}
                    </a>
                    <a href="{{ route('shop.customers.register.index') }}" class="flex items-center text-white font-semibold">
                        {{ __('auto-leading-theme::app.common.register') }}
                    </a>
                @endguest
                @auth('customer')
                    <a href="{{ route('shop.customers.account.profile.index') }}" class="flex items-center text-white">
                        {{ auth()->guard('customer')->user()->first_name }}
                    </a>
                    <a href="{{ route('shop.customers.account.orders.index') }}" class="flex items-center text-white">
                        {{ __('auto-leading-theme::app.common.orders') }}
                    </a>
                    <form action="{{ route('shop.customer.session.destroy') }}" method="POST">
                        @method('DELETE')
                        @csrf
                        <button type="submit" class="text-left text-red-500 font-medium cursor-pointer">
                            {{ __('auto-leading-theme::app.common.logout') }}
                        </button>
                    </form>
                @endauth
                <a href="{{ route('shop.checkout.cart.index') }}" class="flex items-center gap-2 text-white">
                    <x-heroicon-o-shopping-cart class="w-6 h-6 al-text-accent" />
                    {{ __('auto-leading-theme::app.common.view_cart') }}
                    <span id="al-cart-count-mobile" class="hidden min-w-[18px] h-[18px] rounded-full bg-[#c9a84c] text-[10px] font-bold leading-[18px] text-center text-black px-1"></span>
                </a>
            </div>
        </div>
    </div>

</header>

@pushOnce('scripts')
<script>
    function alInitNavbar() {
        var header    = document.querySelector('.al-header');
        var toggle    = document.querySelector('.al-mobile-toggle');
        var drawer    = document.querySelector('.al-mobile-drawer');
        var iconMenu  = document.querySelector('.al-icon-menu');
        var iconClose = document.querySelector('.al-icon-close');

        function onScroll() {
            if (header) header.classList.toggle('is-scrolled', window.scrollY > 50);
        }
        window.addEventListener('scroll', onScroll, { passive: true });
        onScroll();

        if (toggle && drawer) {
            toggle.addEventListener('click', function () {
                var isOpen = drawer.style.display !== 'none';
                drawer.style.display    = isOpen ? 'none' : '';
                iconMenu.style.display  = isOpen ? '' : 'none';
                iconClose.style.display = isOpen ? 'none' : '';
                toggle.setAttribute('aria-expanded', String(!isOpen));
            });
        }

        document.querySelectorAll('.al-header .al-dropdown').forEach(function (dropdown) {
            var btn  = dropdown.querySelector('.al-dropdown-toggle');
            var menu = dropdown.querySelector('.al-dropdown-menu');
            if (!btn || !menu) return;

            btn.addEventListener('click', function (e) {
                e.stopPropagation();
                var opening = menu.style.display === 'none';
                document.querySelectorAll('.al-header .al-dropdown-menu').forEach(function (m) {
                    m.style.display = 'none';
                    m.closest('.al-dropdown')?.querySelector('.al-dropdown-toggle')?.setAttribute('aria-expanded', 'false');
                });
                if (opening) {
                    menu.style.display = '';
                    btn.setAttribute('aria-expanded', 'true');
                }
            });
        });

        document.addEventListener('click', function () {
            document.querySelectorAll('.al-header .al-dropdown-menu').forEach(function (menu) {
                menu.style.display = 'none';
                menu.closest('.al-dropdown')?.querySelector('.al-dropdown-toggle')?.setAttribute('aria-expanded', 'false');
            });
        });
    }

    function alUpdateCartCount() {
        fetch('{{ route('shop.api.checkout.cart.index') }}', {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function (r) { return r.json(); })
        .then(function (data) {
            var qty = data?.data?.items_qty || 0;
            ['al-cart-count', 'al-cart-count-mobile'].forEach(function (id) {
                var el = document.getElementById(id);
                if (!el) return;
                if (qty > 0) {
                    el.textContent = qty;
                    el.style.display = '';
                    el.classList.remove('hidden');
                } else {
                    el.style.display = 'none';
                    el.classList.add('hidden');
                }
            });
        })
        .catch(function () {});
    }
</script>
@endPushOnce
