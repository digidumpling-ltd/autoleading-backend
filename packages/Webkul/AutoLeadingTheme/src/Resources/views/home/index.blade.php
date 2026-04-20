<x-auto-leading-theme::layouts>
    {{-- Hero Section --}}
    <div
        class="al-hero-section relative h-screen"
        x-data="{
            scrolled: false,
            init() {
                window.addEventListener('scroll', () => {
                    this.scrolled = window.scrollY > 50;
                });
            }
        }"
    >
        <div class="absolute inset-0 bg-black/40 z-10"></div>

        <img src="{{ bagisto_asset('images/hero-image.webp', 'auto-leading-theme') }}" class="absolute inset-0 w-full h-full object-cover" alt="Hero">

        <div class="al-hero-content relative z-20 al-shell h-full flex flex-col justify-center items-center text-center text-white">
            <h1 class="text-5xl md:text-7xl font-black italic tracking-tighter mb-4 uppercase">
                {{ __('auto-leading-theme::app.home.hero_title') }}
            </h1>

            {{-- Search Form --}}
            <form
                action="{{ route('shop.search.index') }}"
                method="GET"
                class="w-full max-w-4xl"
            >
                <div class="grid grid-cols-1 md:grid-cols-4 items-center bg-white/10 backdrop-blur-md p-4 rounded-full gap-4 shadow-lg border border-white/20">
                    {{-- Brand --}}
                    <div class="relative col-span-1 md:col-span-1">
                        <select
                            name="brand"
                            class="w-full bg-transparent text-white py-3 px-4 pr-8 rounded-full appearance-none focus:outline-none"
                        >
                            <option value="" class="text-black">{{ __('auto-leading-theme::app.brands.all') }}</option>
                            @foreach (app('Webkul\Attribute\Repositories\AttributeRepository')->findOneByField('code', 'brand')?->options ?? [] as $brand)
                                <option value="{{ $brand->id }}" class="text-black">{{ $brand->admin_name }}</option>
                            @endforeach
                        </select>
                        <x-heroicon-o-chevron-down class="w-5 h-5 text-white absolute top-1/2 right-4 -translate-y-1/2 pointer-events-none" />
                    </div>

                    {{-- Type --}}
                    <div class="relative col-span-1 md:col-span-1">
                        <select
                            name="type"
                            class="w-full bg-transparent text-white py-3 px-4 pr-8 rounded-full appearance-none focus:outline-none"
                        >
                            <option value="" class="text-black">{{ __('auto-leading-theme::app.types.all') }}</option>
                            @foreach (app('Webkul\Attribute\Repositories\AttributeRepository')->findOneByField('code', 'type')?->options ?? [] as $type)
                                <option value="{{ $type->id }}" class="text-black">{{ $type->admin_name }}</option>
                            @endforeach
                        </select>
                        <x-heroicon-o-chevron-down class="w-5 h-5 text-white absolute top-1/2 right-4 -translate-y-1/2 pointer-events-none" />
                    </div>

                    {{-- Name --}}
                    <div class="relative col-span-1 md:col-span-1">
                        <input
                            type="text"
                            name="name"
                            class="w-full bg-transparent text-white py-3 px-4 rounded-full focus:outline-none placeholder-white/70"
                            placeholder="{{ __('auto-leading-theme::app.common.search_model') }}"
                        />
                    </div>

                    {{-- Submit --}}
                    <div class="col-span-1 md:col-span-1">
                        <button
                            type="submit"
                            class="w-full bg-[#F0A500] hover:bg-[#C88600] text-black px-8 py-3 rounded-full font-bold transition-all transform hover:scale-105 uppercase tracking-wider flex items-center justify-center gap-2"
                            aria-label="{{ __('auto-leading-theme::app.common.search') }}"
                        >
                            <x-heroicon-o-magnifying-glass class="w-5 h-5" />
                            <span>{{ __('auto-leading-theme::app.common.search') }}</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Featured Products Section -->
    <section class="al-featured-section py-24 bg-white">
        <div class="al-shell">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div>
                    <span class="text-[#F0A500] font-bold uppercase tracking-widest text-sm mb-2 block">{{ __('auto-leading-theme::app.home.featured_label') }}</span>
                    <h2 class="text-4xl font-black italic uppercase tracking-tighter">{{ __('auto-leading-theme::app.home.featured_title') }}</h2>
                </div>
                <a href="{{ route('shop.search.index') }}" class="text-[#1a1a1a] font-bold hover:text-[#F0A500] transition-colors uppercase tracking-widest text-sm flex items-center gap-2">
                    {{ __('auto-leading-theme::app.home.view_all') }}
                    <x-heroicon-o-arrow-right class="w-4 h-4" />
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($featuredProducts as $product)
                    @php
                        $flatProduct = $product->product_flat ?? $product;
                    @endphp
                    <x-auto-leading-theme::car-card
                        :name="$product->name"
                        :price="core()->currency($flatProduct->price ?? 0)"
                        :url="route('shop.product_or_category.index', $product->url_key)"
                        :image="$product->base_image?->url"
                        :badge="__('auto-leading-theme::app.home.featured_label')"
                        :tag="__('auto-leading-theme::app.home.hot_deal_label')"
                        :images-count="$product->images->count()"
                        :product-id="$product->id"
                        :is-wishlisted="in_array($product->id, $wishlistedProductIds ?? [])"
                    />
                @endforeach
            </div>
        </div>
    </section>

    <!-- Brands Section -->
    <section class="al-brands-section py-20 bg-[#f9f9f9] border-t border-b border-gray-100">
        <div class="al-shell grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-12 items-center opacity-30 grayscale hover:grayscale-0 transition-all duration-700">
            <div class="flex justify-center"><h3 class="font-bold text-2xl">TESLA</h3></div>
            <div class="flex justify-center"><h3 class="font-bold text-2xl">MERCEDES</h3></div>
            <div class="flex justify-center"><h3 class="font-bold text-2xl">BMW</h3></div>
            <div class="flex justify-center"><h3 class="font-bold text-2xl">AUDI</h3></div>
            <div class="flex justify-center"><h3 class="font-bold text-2xl">PORSCHE</h3></div>
            <div class="flex justify-center"><h3 class="font-bold text-2xl">HONDA</h3></div>
        </div>
    </section>

    <!-- Why Choose Us -->
    <section class="al-why-us py-24 bg-[#0d0d0d] text-white">
        <div class="al-shell grid grid-cols-1 lg:grid-cols-2 gap-20 items-center">
            <div class="relative">
                <div class="aspect-square bg-[#1a1a1a] rounded-3xl overflow-hidden shadow-2xl">
                    <img src="{{ bagisto_asset('images/hero-image.webp', 'auto-leading-theme') }}" class="w-full h-full object-cover opacity-50" alt="About">
                </div>
                <div class="absolute -bottom-10 -right-10 bg-[#F0A500] p-10 rounded-3xl hidden md:block shadow-2xl">
                    <span class="text-5xl font-black block text-black">15+</span>
                    <span class="text-black font-bold uppercase tracking-widest text-sm">{{ __('auto-leading-theme::app.home.years_experience') }}</span>
                </div>
            </div>
            <div>
                <span class="text-[#F0A500] font-bold uppercase tracking-widest text-sm mb-2 block">{{ __('auto-leading-theme::app.home.about_label') }}</span>
                <h2 class="text-5xl font-black italic uppercase tracking-tighter mb-8 leading-tight">{{ __('auto-leading-theme::app.home.about_title') }}</h2>
                <div class="space-y-8">
                    <div class="flex gap-6">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center shrink-0">
                            <x-heroicon-o-shield-check class="w-8 h-8 text-[#F0A500]" />
                        </div>
                        <div>
                            <h4 class="text-xl font-bold mb-2 uppercase">{{ __('auto-leading-theme::app.home.feature_1_title') }}</h4>
                            <p class="text-white/50 text-sm leading-relaxed">{{ __('auto-leading-theme::app.home.feature_1_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex gap-6">
                        <div class="w-14 h-14 bg-white/5 rounded-2xl flex items-center justify-center shrink-0">
                            <x-heroicon-o-banknotes class="w-8 h-8 text-[#F0A500]" />
                        </div>
                        <div>
                            <h4 class="text-xl font-bold mb-2 uppercase">{{ __('auto-leading-theme::app.home.feature_2_title') }}</h4>
                            <p class="text-white/50 text-sm leading-relaxed">{{ __('auto-leading-theme::app.home.feature_2_desc') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-auto-leading-theme::layouts>
