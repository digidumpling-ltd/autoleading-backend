<x-auto-leading-theme::layouts>
    <!-- Page Title -->
    @section('page_title')
        {{ __('auto-leading-theme::app.product_list.title') }}
    @endsection

    <div class="al-container mt-24">
        <!-- Search Header -->
        <div class="al-search-header bg-[#1a1a1a] p-6 rounded-2xl border border-white/5 mb-8">
            <div class="al-search-form flex flex-wrap gap-4 items-center">
                <!-- Brand Dropdown -->
                <select name="brand" class="al-search-select bg-[#111] text-white border-white/10 rounded-xl px-4 py-2 focus:border-[#F0A500] outline-none min-w-[150px]">
                    <option value="">{{ __('auto-leading-theme::app.search.all_brands') }}</option>
                    <option value="tesla">{{ __('auto-leading-theme::app.brands.tesla') }}</option>
                    <option value="mercedes">{{ __('auto-leading-theme::app.brands.mercedes') }}</option>
                    <option value="bmw">{{ __('auto-leading-theme::app.brands.bmw') }}</option>
                    <option value="audi">{{ __('auto-leading-theme::app.brands.audi') }}</option>
                    <option value="porsche">{{ __('auto-leading-theme::app.brands.porsche') }}</option>
                    <option value="honda">{{ __('auto-leading-theme::app.brands.honda') }}</option>
                    <option value="toyota">{{ __('auto-leading-theme::app.brands.toyota') }}</option>
                    <option value="volkswagen">{{ __('auto-leading-theme::app.brands.volkswagen') }}</option>
                </select>

                <!-- Type Dropdown -->
                <select name="type" class="al-search-select bg-[#111] text-white border-white/10 rounded-xl px-4 py-2 focus:border-[#F0A500] outline-none min-w-[150px]">
                    <option value="">{{ __('auto-leading-theme::app.search.all_types') }}</option>
                    <option value="sedan">{{ __('auto-leading-theme::app.search.sedan') }}</option>
                    <option value="suv">{{ __('auto-leading-theme::app.search.suv') }}</option>
                    <option value="sports">{{ __('auto-leading-theme::app.search.sports') }}</option>
                    <option value="convertible">{{ __('auto-leading-theme::app.search.convertible') }}</option>
                    <option value="hatchback">{{ __('auto-leading-theme::app.search.hatchback') }}</option>
                    <option value="hybrid">{{ __('auto-leading-theme::app.search.hybrid') }}</option>
                </select>

                <!-- Location Dropdown -->
                <select name="location" class="al-search-select bg-[#111] text-white border-white/10 rounded-xl px-4 py-2 focus:border-[#F0A500] outline-none min-w-[150px]">
                    <option value="">{{ __('auto-leading-theme::app.search.all_locations') }}</option>
                    <option value="hk">{{ __('auto-leading-theme::app.search.hong_kong') }}</option>
                    <option value="macau">{{ __('auto-leading-theme::app.search.macau') }}</option>
                    <option value="china">{{ __('auto-leading-theme::app.search.china') }}</option>
                </select>

                <!-- Price Range -->
                <div class="al-price-range flex items-center gap-2">
                    <input type="number" name="min_price" placeholder="{{ __('auto-leading-theme::app.search.min_price') }}" class="al-price-input bg-[#111] text-white border-white/10 rounded-xl px-4 py-2 focus:border-[#F0A500] outline-none w-24">
                    <span class="text-white/20">-</span>
                    <input type="number" name="max_price" placeholder="{{ __('auto-leading-theme::app.search.max_price') }}" class="al-price-input bg-[#111] text-white border-white/10 rounded-xl px-4 py-2 focus:border-[#F0A500] outline-none w-24">
                </div>

                <!-- Clear Filters -->
                <button type="button" class="al-clear-filters text-gray-400 hover:text-white transition-colors text-sm ml-auto">{{ __('auto-leading-theme::app.search.clear_all') }}</button>
            </div>

            <!-- Search Input -->
            <div class="al-keyword-search mt-4 relative">
                <input type="text" name="keyword" placeholder="{{ __('auto-leading-theme::app.search.keyword_placeholder') }}" class="al-keyword-input w-full bg-[#111] text-white border-white/10 rounded-xl px-4 py-3 focus:border-[#F0A500] outline-none pr-12">
                <button type="button" class="al-search-btn absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#F0A500] transition-colors">
                    <x-heroicon-o-magnifying-glass class="w-6 h-6" />
                </button>
            </div>
        </div>

        <!-- Results Header -->
        <div class="al-results-header flex justify-between items-center mb-8 px-2">
            <div class="al-results-count text-white/60 font-medium">
                {{ trans_choice('auto-leading-theme::app.product_list.results', $products->total(), ['count' => $products->total()]) }}
            </div>

            <div class="al-sort-controls flex items-center gap-3">
                <span class="text-white/40 text-sm italic">{{ __('auto-leading-theme::app.product_list.sort_by') }}:</span>
                <select name="sort" class="al-sort-select bg-transparent text-white border-none focus:ring-0 cursor-pointer font-semibold">
                    <option value="newest">{{ __('auto-leading-theme::app.product_list.sort_newest') }}</option>
                    <option value="price_low">{{ __('auto-leading-theme::app.product_list.sort_price_low') }}</option>
                    <option value="price_high">{{ __('auto-leading-theme::app.product_list.sort_price_high') }}</option>
                    <option value="popular">{{ __('auto-leading-theme::app.product_list.sort_popular') }}</option>
                </select>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="al-products-grid grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($products as $product)
                @php
                    $flatProduct = $product->product_flat ?? $product;
                @endphp
                <x-auto-leading-theme::car-card
                    :name="$product->name"
                    :price="core()->currency($flatProduct->price ?? 0)"
                    :url="route('shop.product_or_category.index', $product->url_key)"
                    :image="$product->base_image?->url"
                    :brand="$product->brand"
                    :product-id="$product->id"
                    :is-wishlisted="in_array($product->id, $wishlistedProductIds ?? [])"
                    :images-count="$product->images->count()"
                />
            @empty
                <div class="al-no-products col-span-full py-20 text-center bg-[#1a1a1a] rounded-2xl border border-dashed border-white/10">
                    <p class="text-white/40 text-lg">{{ __('auto-leading-theme::app.product_list.no_products') }}</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="al-pagination mt-12 flex justify-center">
            {{ $products->links() }}
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const clearFiltersBtn = document.querySelector('.al-clear-filters');
            const searchBtn = document.querySelector('.al-search-btn');
            const sortSelect = document.querySelector('.al-sort-select');

            // Clear all filters
            if (clearFiltersBtn) {
                clearFiltersBtn.addEventListener('click', function() {
                    document.querySelectorAll('.al-search-select').forEach(select => select.value = '');
                    document.querySelectorAll('.al-price-input').forEach(input => input.value = '');
                    document.querySelector('.al-keyword-input').value = '';
                    sortSelect.value = 'newest';
                });
            }

            // Search functionality (placeholder)
            if (searchBtn) {
                searchBtn.addEventListener('click', function() {
                    const keyword = document.querySelector('.al-keyword-input').value;
                    const url = new URL(window.location.href);
                    url.searchParams.set('keyword', keyword);
                    window.location.href = url.toString();
                });
            }

            // Sort functionality
            if (sortSelect) {
                sortSelect.addEventListener('change', function() {
                    const url = new URL(window.location.href);
                    url.searchParams.set('sort', this.value);
                    window.location.href = url.toString();
                });
            }
        });
    </script>
    @endpush
</x-auto-leading-theme::layouts>
