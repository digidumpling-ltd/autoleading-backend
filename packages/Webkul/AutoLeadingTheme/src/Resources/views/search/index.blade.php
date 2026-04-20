<x-shop::layouts :has-feature="false">
    <!-- Page Title -->
    <x-slot:title>
        {{ __('auto-leading-theme::app.product_list.title') }}
    </x-slot>

    <div class="al-container">
        <!-- Page Heading -->
        <div class="al-page-header">
            <h1 class="al-page-title">{{ __('auto-leading-theme::app.product_list.heading') }}</h1>
        </div>

        <!-- Search Results Container -->
        <div class="al-products-section">
            <div class="al-products-layout">
                <!-- Sidebar Filters -->
                <aside class="al-filters-sidebar">
                    <div class="al-filter-panel">
                        <h2 class="al-filter-title">{{ __('auto-leading-theme::app.product_list.filters') }}</h2>

                        <!-- Brand Filter -->
                        <div class="al-filter-group">
                            <h3 class="al-filter-heading">{{ __('auto-leading-theme::app.product_list.brand') }}</h3>
                            <div class="al-filter-options">
                                @foreach(['Audi', 'Mercedes Benz', 'BMW', 'Maserati'] as $brand)
                                <label class="al-checkbox-label">
                                    <input type="checkbox" name="brand" value="{{ strtolower($brand) }}" class="al-filter-checkbox">
                                    {{ $brand }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Type Filter -->
                        <div class="al-filter-group">
                            <h3 class="al-filter-heading">{{ __('auto-leading-theme::app.product_list.type') }}</h3>
                            <div class="al-filter-options">
                                @foreach(['Sedan' => 'sedan', 'Sports' => 'sports', 'SUV' => 'suv', 'Convertible' => 'convertible'] as $label => $value)
                                <label class="al-checkbox-label">
                                    <input type="checkbox" name="type" value="{{ $value }}" class="al-filter-checkbox">
                                    {{ $label }}
                                </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Price Range Filter -->
                        <div class="al-filter-group">
                            <h3 class="al-filter-heading">{{ __('auto-leading-theme::app.product_list.price_range') }}</h3>
                            <div class="al-price-range">
                                <input type="number" name="min_price" placeholder="0" class="al-price-input" min="0">
                                <input type="number" name="max_price" placeholder="10000" class="al-price-input" min="0">
                            </div>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="al-products-main">
                    <!-- Toolbar -->
                    <div class="al-toolbar">
                        <div class="al-results-count">
                            <span id="results-count">{{ __('auto-leading-theme::app.product_list.results', ['count' => 0]) }}</span>
                        </div>

                        <div class="al-sort-dropdown">
                            <label for="sort">{{ __('auto-leading-theme::app.product_list.sort_by') }}:</label>
                            <select id="sort" name="sort" class="al-select">
                                <option value="newest">{{ __('auto-leading-theme::app.product_list.sort_newest') }}</option>
                                <option value="price_low">{{ __('auto-leading-theme::app.product_list.sort_price_low') }}</option>
                                <option value="price_high">{{ __('auto-leading-theme::app.product_list.sort_price_high') }}</option>
                                <option value="popular">{{ __('auto-leading-theme::app.product_list.sort_popular') }}</option>
                            </select>
                        </div>
                    </div>

                    <!-- Products Grid -->
                    <div id="products-grid" class="al-products-grid">
                        @forelse(app(\Webkul\Product\Repositories\ProductRepository::class)->scopeQuery(fn ($q) => $q->join('product_flat as pf', 'products.id', '=', 'pf.product_id')->where('pf.status', 1)->where('pf.visible_individually', 1)->where('pf.locale', app()->getLocale())->select('products.*')->orderBy('products.created_at', 'desc'))->all()->take(12) as $product)
                            <x-auto-leading-theme::car-card
                                :name="$product->name"
                                :price="core()->currency($product->product_flat->price ?? 0)"
                                :url="route('shop.product_or_category.index', $product->url_key)"
                                :image="$product->base_image?->url"
                            />
                        @empty
                            <div class="al-empty-results">
                                <p>{{ __('auto-leading-theme::app.product_list.no_products') }}</p>
                            </div>
                        @endforelse
                    </div>

                    <!-- Pagination -->
                    <div class="al-pagination">
                        <a href="#" class="al-pagination-link">{{ __('auto-leading-theme::app.common.previous') }}</a>
                        <a href="#" class="al-pagination-link al-active">1</a>
                        <a href="#" class="al-pagination-link">2</a>
                        <a href="#" class="al-pagination-link">3</a>
                        <a href="#" class="al-pagination-link">{{ __('auto-leading-theme::app.common.next') }}</a>
                    </div>
                </main>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const filterCheckboxes = document.querySelectorAll('.al-filter-checkbox');
            const sortDropdown = document.getElementById('sort');

            // Handle filter changes
            filterCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', updateFilters);
            });

            // Handle sort changes
            sortDropdown.addEventListener('change', updateFilters);

            function updateFilters() {
                // Collect selected filters
                const brands = Array.from(document.querySelectorAll('input[name="brand"]:checked')).map(cb => cb.value);
                const types = Array.from(document.querySelectorAll('input[name="type"]:checked')).map(cb => cb.value);
                const minPrice = document.querySelector('input[name="min_price"]').value;
                const maxPrice = document.querySelector('input[name="max_price"]').value;
                const sort = sortDropdown.value;

                // Build URL with query parameters
                const params = new URLSearchParams();
                if (brands.length) params.append('brand', brands.join(','));
                if (types.length) params.append('type', types.join(','));
                if (minPrice) params.append('min_price', minPrice);
                if (maxPrice) params.append('max_price', maxPrice);
                if (sort) params.append('sort', sort);

                // Update page with new filters (in real impl, would fetch via AJAX)
                console.log('Filters applied:', params.toString());
            }
        });
    </script>
    @endpush
</x-shop::layouts>
