@php
    $navItems = [
        [
            'label' => __('auto-leading-theme::app.nav.home'),
            'url'   => route('shop.home.index'),
        ],
        [
            'label' => __('auto-leading-theme::app.nav.cross_border'),
            'url'   => url('/china-hk-travel'),
        ],
        [
            'label' => __('auto-leading-theme::app.nav.about'),
            'url'   => url('/page/about-us'),
        ],
        [
            'label' => __('auto-leading-theme::app.nav.models'),
            'url'   => route('shop.search.index'),
        ],
        [
            'label' => __('auto-leading-theme::app.nav.notice'),
            'url'   => url('/rental-notice'),
        ],
        [
            'label' => __('auto-leading-theme::app.nav.membership'),
            'url'   => url('/membership'),
        ],
        [
            'label' => __('auto-leading-theme::app.nav.blog'),
            'url'   => url('/blog'),
        ],
    ];

    $typeOptions = [
        [
            'label' => __('auto-leading-theme::app.types.sedan'),
            'query' => 'sedan',
            'icon'  => '🚘',
        ],
        [
            'label' => __('auto-leading-theme::app.types.sports'),
            'query' => 'sports',
            'icon'  => '🏎️',
        ],
        [
            'label' => __('auto-leading-theme::app.types.suv'),
            'query' => 'suv',
            'icon'  => '🚙',
        ],
        [
            'label' => __('auto-leading-theme::app.types.convertible'),
            'query' => 'convertible',
            'icon'  => '🚗',
        ],
    ];

    $staticFeaturedCars = [
        [
            'name'  => 'AUDI R8 4.2 FSI QUATTRO V8',
            'price' => '$2,299',
            'badge' => __('auto-leading-theme::app.badges.featured'),
            'tag'   => __('auto-leading-theme::app.badges.hot'),
        ],
        [
            'name'  => 'AUDI A6 2.0 TSFI LWB',
            'price' => '$599',
            'badge' => __('auto-leading-theme::app.badges.featured'),
            'tag'   => __('auto-leading-theme::app.badges.efficient'),
        ],
        [
            'name'  => 'BMW 428I GRAN COUPE',
            'price' => '$699',
            'badge' => __('auto-leading-theme::app.badges.popular'),
            'tag'   => __('auto-leading-theme::app.badges.hot'),
        ],
        [
            'name'  => 'MERCEDES BENZ C200',
            'price' => '$649',
            'badge' => __('auto-leading-theme::app.badges.featured'),
            'tag'   => __('auto-leading-theme::app.badges.efficient'),
        ],
    ];

    $brandOptions    = ['Audi', 'Mercedes Benz', 'BMW', 'Maserati'];
    $carTypeFilters  = ['Sedan', 'Sports', 'SUV', 'Convertible'];
    $categoryOptions = [
        __('auto-leading-theme::app.home.category_all'),
        __('auto-leading-theme::app.home.category_hot'),
        __('auto-leading-theme::app.home.category_latest'),
    ];

    $locales        = core()->getCurrentChannel()->locales()->orderBy('name')->get();
    $currentLocale  = app()->getLocale();
    $showSwitcher   = $locales->count() > 1;

    $serviceItems = [
        [
            'icon'  => '🚘',
            'title' => __('auto-leading-theme::app.services.fleet_title'),
            'desc'  => __('auto-leading-theme::app.services.fleet_desc'),
        ],
        [
            'icon'  => '📅',
            'title' => __('auto-leading-theme::app.services.flexible_title'),
            'desc'  => __('auto-leading-theme::app.services.flexible_desc'),
        ],
        [
            'icon'  => '💬',
            'title' => __('auto-leading-theme::app.services.support_title'),
            'desc'  => __('auto-leading-theme::app.services.support_desc'),
        ],
    ];

    $footerQuickLinks = [
        ['label' => __('auto-leading-theme::app.nav.home'),       'url' => route('shop.home.index')],
        ['label' => __('auto-leading-theme::app.nav.models'),     'url' => route('shop.search.index')],
        ['label' => __('auto-leading-theme::app.nav.about'),      'url' => url('/page/about-us')],
        ['label' => __('auto-leading-theme::app.nav.membership'), 'url' => url('/membership')],
        ['label' => __('auto-leading-theme::app.nav.blog'),       'url' => url('/blog')],
    ];

    $footerCarModelLinks = [
        ['label' => __('auto-leading-theme::app.types.sedan'),       'url' => route('shop.search.index', ['type' => 'sedan'])],
        ['label' => __('auto-leading-theme::app.types.sports'),      'url' => route('shop.search.index', ['type' => 'sports'])],
        ['label' => __('auto-leading-theme::app.types.suv'),         'url' => route('shop.search.index', ['type' => 'suv'])],
        ['label' => __('auto-leading-theme::app.types.convertible'), 'url' => route('shop.search.index', ['type' => 'convertible'])],
    ];
@endphp

<x-shop::layouts :has-header="false" :has-feature="false">
    <x-slot:title>
        {{ __('auto-leading-theme::app.home.title') }}
    </x-slot>

    <div class="al-site">
        {{-- ========== HEADER ========== --}}
        <header class="al-header">
            <div class="al-shell al-header-shell">
                <a
                    href="{{ route('shop.customer.session.create') }}"
                    class="al-account-link"
                    aria-label="{{ __('auto-leading-theme::app.common.login') }}"
                >
                    <span aria-hidden="true">👤</span>
                    <span>{{ __('auto-leading-theme::app.common.login') }}</span>
                </a>

                <nav class="al-nav al-nav-desktop" aria-label="{{ __('auto-leading-theme::app.nav.main_navigation') }}">
                    @foreach ($navItems as $item)
                        <a
                            href="{{ $item['url'] }}"
                            class="{{ request()->url() === $item['url'] ? 'is-active' : '' }}"
                        >
                            {{ $item['label'] }}
                        </a>
                    @endforeach
                </nav>

                <div class="al-header-actions">
                    @if ($showSwitcher)
                        <div class="al-lang-switcher" aria-label="{{ __('auto-leading-theme::app.nav.language') }}">
                            <span class="al-lang-current">🌐 {{ $locales->where('code', $currentLocale)->first()?->name ?? strtoupper($currentLocale) }}</span>

                            <ul class="al-lang-dropdown" role="listbox">
                                @foreach ($locales as $locale)
                                    <li role="option" aria-selected="{{ $locale->code === $currentLocale ? 'true' : 'false' }}">
                                        <a href="{{ request()->url() . '?locale=' . $locale->code }}">
                                            {{ $locale->name }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <a
                        href="{{ route('shop.customer.session.create') }}"
                        class="al-profile-link"
                        aria-label="{{ __('auto-leading-theme::app.common.account_center') }}"
                    >
                        👥
                    </a>
                </div>
            </div>

            <div class="al-logo-row">
                <a
                    href="{{ route('shop.home.index') }}"
                    class="al-logo-link"
                    aria-label="{{ __('auto-leading-theme::app.nav.home') }}"
                >
                    @if (core()->getCurrentChannel()->logo_url)
                        <img
                            src="{{ core()->getCurrentChannel()->logo_url }}"
                            alt="{{ config('app.name') }}"
                            width="220"
                            height="72"
                        >
                    @else
                        <span class="al-logo-text">{{ config('app.name') }}</span>
                    @endif
                </a>
            </div>

            <details class="al-mobile-menu">
                <summary>{{ __('auto-leading-theme::app.common.menu') }}</summary>

                <nav class="al-nav al-nav-mobile" aria-label="{{ __('auto-leading-theme::app.nav.main_navigation') }}">
                    @foreach ($navItems as $item)
                        <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
                    @endforeach

                    @if ($showSwitcher)
                        <div class="al-lang-mobile">
                            @foreach ($locales as $locale)
                                <a href="{{ request()->url() . '?locale=' . $locale->code }}">
                                    {{ $locale->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </nav>
            </details>
        </header>

        {{-- ========== HERO ========== --}}
        <section class="al-hero">
            <div class="al-hero-overlay"></div>

            <div class="al-shell al-hero-content">
                <h1>{{ __('auto-leading-theme::app.home.hero_title') }}</h1>

                <form action="{{ route('shop.search.index') }}" method="GET" class="al-search-form">
                    <label for="al-brand" class="sr-only">{{ __('auto-leading-theme::app.home.brand_placeholder') }}</label>

                    <select id="al-brand" name="brand">
                        <option value="">{{ __('auto-leading-theme::app.home.brand_placeholder') }}</option>

                        @foreach ($brandOptions as $brand)
                            <option value="{{ $brand }}">{{ $brand }}</option>
                        @endforeach
                    </select>

                    <label for="al-type" class="sr-only">{{ __('auto-leading-theme::app.home.type_placeholder') }}</label>

                    <select id="al-type" name="type">
                        <option value="">{{ __('auto-leading-theme::app.home.type_placeholder') }}</option>

                        @foreach ($carTypeFilters as $carType)
                            <option value="{{ $carType }}">{{ $carType }}</option>
                        @endforeach
                    </select>

                    <label for="al-category" class="sr-only">{{ __('auto-leading-theme::app.home.category_placeholder') }}</label>

                    <select id="al-category" name="category">
                        <option value="">{{ __('auto-leading-theme::app.home.category_placeholder') }}</option>

                        @foreach ($categoryOptions as $category)
                            <option value="{{ $category }}">{{ $category }}</option>
                        @endforeach
                    </select>

                    <button type="submit">{{ __('auto-leading-theme::app.common.search') }}</button>
                </form>

                <div class="al-type-grid">
                    @foreach ($typeOptions as $type)
                        <a href="{{ route('shop.search.index', ['type' => $type['query']]) }}" class="al-type-chip">
                            <span class="al-type-icon">{{ $type['icon'] }}</span>
                            <span>{{ $type['label'] }}</span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>

        {{-- ========== SERVICES ========== --}}
        <section class="al-services al-shell" aria-label="{{ __('auto-leading-theme::app.services.section_label') }}">
            @foreach ($serviceItems as $item)
                <div class="al-service-card">
                    <span class="al-service-icon" aria-hidden="true">{{ $item['icon'] }}</span>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['desc'] }}</p>
                </div>
            @endforeach
        </section>

        {{-- ========== FEATURED CARS ========== --}}
        <section class="al-shell al-featured">
            <div class="al-featured-head">
                <p>{{ __('auto-leading-theme::app.home.featured_prefix') }}</p>
                <h2>{{ __('auto-leading-theme::app.home.featured_title') }}</h2>
            </div>

            <div class="al-featured-grid">
                @php
                    $useDynamic = isset($featuredProducts) && $featuredProducts->isNotEmpty();
                @endphp

                @if ($useDynamic)
                    @foreach ($featuredProducts as $product)
                        <x-auto-leading-theme::car-card
                            :name="$product->name"
                            :price="core()->currency($product->product_flat->price ?? 0)"
                            :url="route('shop.product_or_category.index', $product->url_key)"
                            :badge="__('auto-leading-theme::app.badges.featured')"
                            :image="$product->base_image?->url"
                        />
                    @endforeach
                @else
                    @foreach ($staticFeaturedCars as $car)
                        <x-auto-leading-theme::car-card
                            :name="$car['name']"
                            :price="$car['price']"
                            :url="route('shop.search.index', ['query' => $car['name']])"
                            :badge="$car['badge']"
                            :tag="$car['tag']"
                        />
                    @endforeach
                @endif
            </div>

            <div class="al-featured-more">
                <a href="{{ route('shop.search.index') }}">{{ __('auto-leading-theme::app.common.view_more') }}</a>
            </div>
        </section>

        {{-- ========== FOOTER ========== --}}
        <footer class="al-footer">
            <div class="al-shell al-footer-grid">
                <x-auto-leading-theme::footer-column
                    :heading="__('auto-leading-theme::app.footer.quick_links')"
                    :links="$footerQuickLinks"
                />

                <x-auto-leading-theme::footer-column
                    :heading="__('auto-leading-theme::app.footer.car_models')"
                    :links="$footerCarModelLinks"
                />

                <x-auto-leading-theme::footer-column
                    :heading="__('auto-leading-theme::app.footer.contact')"
                >
                    <address class="al-footer-contact">
                        <p>{{ __('auto-leading-theme::app.footer.address') }}</p>
                        <p>
                            <a href="tel:{{ __('auto-leading-theme::app.footer.phone') }}">
                                {{ __('auto-leading-theme::app.footer.phone') }}
                            </a>
                        </p>
                        <p>
                            <a href="mailto:{{ __('auto-leading-theme::app.footer.email') }}">
                                {{ __('auto-leading-theme::app.footer.email') }}
                            </a>
                        </p>
                    </address>
                </x-auto-leading-theme::footer-column>
            </div>

            <div class="al-footer-bar al-shell">
                <p>© {{ date('Y') }} Auto Leading. {{ __('auto-leading-theme::app.footer.all_rights_reserved') }}</p>
            </div>
        </footer>
    </div>
</x-shop::layouts>
