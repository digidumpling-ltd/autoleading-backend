<x-shop::layouts :has-feature="false">
    <x-slot:title>
        {{ $product?->name ?? __('auto-leading-theme::app.product_detail.title') }}
    </x-slot>

    <div class="al-container">
        <!-- Breadcrumb Navigation -->
        <div class="al-breadcrumb">
            <a href="{{ route('shop.home.index') }}">{{ __('auto-leading-theme::app.common.home') }}</a>
            <span>/</span>
            <a href="{{ route('shop.search.index') }}">{{ __('auto-leading-theme::app.product_list.heading') }}</a>
            <span>/</span>
            <span>{{ $product?->name ?? 'Product' }}</span>
        </div>

        <!-- Product Detail Layout -->
        <div class="al-product-detail">
            <!-- Left: Image Gallery -->
            <div class="al-product-gallery">
                @if($product?->base_image)
                    <div class="al-gallery-main">
                        <img id="main-image" src="{{ $product->base_image->url }}" alt="{{ $product->name }}" class="al-main-image">
                    </div>

                    <div class="al-gallery-thumbnails">
                        <div class="al-thumbnail-scroll">
                            @foreach($product->images ?? collect([$product->base_image]) as $image)
                                <img src="{{ $image->url }}" alt="{{ $product->name }}" class="al-thumbnail" onclick="document.getElementById('main-image').src = this.src">
                            @endforeach
                        </div>
                    </div>
                @else
                    <div class="al-gallery-placeholder">
                        <p>{{ __('auto-leading-theme::app.product_detail.no_image') }}</p>
                    </div>
                @endif
            </div>

            <!-- Right: Product Details -->
            <div class="al-product-info">
                <h1 class="al-product-name">{{ $product?->name ?? 'Product Name' }}</h1>

                @if($product?->product_flat?->price)
                    <div class="al-product-price">
                        <span class="al-price-label">{{ __('auto-leading-theme::app.product_detail.daily_rate') }}:</span>
                        <span class="al-price-value">{{ core()->currency($product->product_flat->price) }}</span>
                    </div>
                @endif

                <!-- Rating (if available) -->
                <div class="al-product-rating">
                    <span class="al-stars">★★★★★</span>
                </div>

                <!-- Specifications Table -->
                <div class="al-specifications">
                    <h3 class="al-specs-title">{{ __('auto-leading-theme::app.product_detail.specifications') }}</h3>
                    <table class="al-specs-table">
                        <tr>
                            <td>{{ __('auto-leading-theme::app.product_detail.transmission') }}</td>
                            <td>{{ $product?->product_flat?->transmission ?? 'Automatic' }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('auto-leading-theme::app.product_detail.engine') }}</td>
                            <td>{{ $product?->product_flat?->engine ?? 'V6 3.0L' }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('auto-leading-theme::app.product_detail.seats') }}</td>
                            <td>{{ $product?->product_flat?->seats ?? 5 }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('auto-leading-theme::app.product_detail.year') }}</td>
                            <td>{{ $product?->product_flat?->year ?? '2024' }}</td>
                        </tr>
                    </table>
                </div>

                <!-- Features -->
                <div class="al-features">
                    <h3 class="al-features-title">{{ __('auto-leading-theme::app.product_detail.features') }}</h3>
                    <ul class="al-features-list">
                        <li>{{ __('auto-leading-theme::app.product_detail.feature_1') }}</li>
                        <li>{{ __('auto-leading-theme::app.product_detail.feature_2') }}</li>
                        <li>{{ __('auto-leading-theme::app.product_detail.feature_3') }}</li>
                    </ul>
                </div>

                <!-- Description -->
                <div class="al-description">
                    <h3 class="al-desc-title">{{ __('auto-leading-theme::app.product_detail.description') }}</h3>
                    <p>{{ $product?->short_description ?? $product?->description ?? __('auto-leading-theme::app.product_detail.no_description') }}</p>
                </div>

                <!-- Related Products -->
                <div class="al-related-products">
                    <h3 class="al-related-title">{{ __('auto-leading-theme::app.product_detail.related_products') }}</h3>
                    <div class="al-related-grid">
                        @forelse(app(\Webkul\Product\Repositories\ProductRepository::class)->scopeQuery(fn ($q) => $q->join('product_flat as pf', 'products.id', '=', 'pf.product_id')->where('pf.status', 1)->where('pf.visible_individually', 1)->where('pf.locale', app()->getLocale())->select('products.*')->orderBy('products.created_at', 'desc'))->all()->take(3) as $relatedProduct)
                            <x-auto-leading-theme::car-card
                                :name="$relatedProduct->name"
                                :price="core()->currency($relatedProduct->product_flat->price ?? 0)"
                                :url="route('shop.product_or_category.index', $relatedProduct->url_key)"
                                :image="$relatedProduct->base_image?->url"
                            />
                        @empty
                        @endforelse
                    </div>
                </div>

                <!-- Book Now Button (sticky on mobile) -->
                <div class="al-product-cta">
                    <button class="al-book-now-btn">
                        {{ __('auto-leading-theme::app.product_detail.book_now') }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        // Gallery keyboard navigation
        document.addEventListener('keydown', function(e) {
            const mainImage = document.getElementById('main-image');
            const thumbnails = document.querySelectorAll('.al-thumbnail');
            
            if (e.key === 'ArrowRight' || e.key === 'ArrowLeft') {
                let currentIndex = Array.from(thumbnails).findIndex(img => img.src === mainImage.src);
                if (e.key === 'ArrowRight') {
                    currentIndex = (currentIndex + 1) % thumbnails.length;
                } else {
                    currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
                }
                mainImage.src = thumbnails[currentIndex].src;
            }
        });

        // Touch swipe support for mobile
        let touchStartX = 0;
        let touchEndX = 0;
        const gallery = document.querySelector('.al-gallery-main');
        
        if (gallery) {
            gallery.addEventListener('touchstart', e => {
                touchStartX = e.changedTouches[0].screenX;
            });

            gallery.addEventListener('touchend', e => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            });

            function handleSwipe() {
                const mainImage = document.getElementById('main-image');
                const thumbnails = document.querySelectorAll('.al-thumbnail');
                
                if (touchEndX < touchStartX) { // Swipe left
                    let currentIndex = Array.from(thumbnails).findIndex(img => img.src === mainImage.src);
                    currentIndex = (currentIndex + 1) % thumbnails.length;
                    mainImage.src = thumbnails[currentIndex].src;
                }
                if (touchEndX > touchStartX) { // Swipe right
                    let currentIndex = Array.from(thumbnails).findIndex(img => img.src === mainImage.src);
                    currentIndex = (currentIndex - 1 + thumbnails.length) % thumbnails.length;
                    mainImage.src = thumbnails[currentIndex].src;
                }
            }
        }

        // Sticky button on mobile
        if (window.innerWidth < 768) {
            const bookBtn = document.querySelector('.al-product-cta');
            bookBtn.classList.add('al-sticky');
        }
    </script>
    @endpush
</x-shop::layouts>
