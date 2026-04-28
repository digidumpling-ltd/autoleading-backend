@props([
    'name',
    'price',
    'url',
    'badge'        => null,
    'image'        => null,
    'productId'    => null,
    'isWishlisted' => false,
    'imagesCount'  => 0,
    'tag'          => null,
])

@php $isAuth = auth()->guard('customer')->check(); @endphp

<div class="al-car-card" style="position:relative;cursor:pointer;">

    {{-- Stretched link — clicking anywhere on the card navigates to the product page --}}
    <a href="{{ $url }}" class="al-card-link" aria-label="{{ $name }}" style="position:absolute;inset:0;z-index:1;border-radius:inherit;"></a>

    {{-- Image / Thumb --}}
    <div class="al-car-thumb">

        @if ($image)
            <img
                src="{{ $image }}"
                alt="{{ $name }}"
                style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;"
                loading="lazy"
            >
        @else
            <span class="al-car-mark">{{ mb_substr($name, 0, 1) }}</span>
        @endif

        {{-- Badge (top-left) --}}
        @if ($badge)
            <span class="al-car-badge">{{ $badge }}</span>
        @endif

        {{-- Image count (top-right) --}}
        @if ($imagesCount > 0)
            <span style="position:absolute;top:0.75rem;right:0.75rem;z-index:2;display:flex;align-items:center;gap:0.2rem;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);color:#fff;font-size:0.72rem;font-weight:600;padding:0.2rem 0.55rem;border-radius:999px;">
                <x-heroicon-o-photo style="width:0.85rem;height:0.85rem;flex-shrink:0;" />
                {{ $imagesCount }}
            </span>
        @endif

        {{-- Wishlist button — z-index:2 keeps it above the stretched link --}}
        @if ($productId)
            <v-al-wishlist-btn
                product-id="{{ $productId }}"
                :is-wishlisted="{{ $isWishlisted ? 'true' : 'false' }}"
                :is-customer="{{ $isAuth ? 'true' : 'false' }}"
                wishlist-url="{{ route('shop.api.customers.account.wishlist.store') }}"
            ></v-al-wishlist-btn>
        @endif
    </div>

    {{-- Body --}}
    <div class="al-car-body">
        <h3>{{ $name }}</h3>

        <div class="al-car-footer">
            <p class="al-car-price">{{ $price }}</p>
            @if ($tag)
                <span class="al-car-tag">{{ $tag }}</span>
            @endif
        </div>
    </div>
</div>

@pushOnce('scripts')
    <script type="text/x-template" id="v-al-wishlist-btn-template">
        <button
            type="button"
            @click.prevent="toggle"
            :disabled="loading"
            :style="wishlisted ? 'color:#F0A500;' : 'color:#fff;'"
            style="position:absolute;bottom:0.75rem;left:0.75rem;z-index:2;width:2rem;height:2rem;border-radius:50%;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s,opacity 0.2s;"
            aria-label="{{ __('auto-leading-theme::app.common.add_to_wishlist') }}"
        >
            <x-heroicon-o-star v-if="!wishlisted" style="width:1rem;height:1rem;flex-shrink:0;" />
            <x-heroicon-s-star v-else              style="width:1rem;height:1rem;flex-shrink:0;" />
        </button>
    </script>

    <script type="module">
        app.component('v-al-wishlist-btn', {
            template: '#v-al-wishlist-btn-template',

            props: {
                productId:    { type: [Number, String], required: true },
                isWishlisted: { type: Boolean, default: false },
                isCustomer:   { type: Boolean, default: false },
                wishlistUrl:  { type: String, required: true },
            },

            data() {
                return {
                    wishlisted: this.isWishlisted,
                    loading: false,
                };
            },

            methods: {
                toggle() {
                    if (!this.isCustomer) { alOpenAuthDialog(); return; }
                    if (this.loading) return;
                    this.loading = true;
                    fetch(this.wishlistUrl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                            'X-Requested-With': 'XMLHttpRequest',
                        },
                        body: JSON.stringify({ product_id: this.productId }),
                    })
                    .then(r => r.json())
                    .then(() => { this.wishlisted = !this.wishlisted; })
                    .finally(() => { this.loading = false; });
                },
            },
        });
    </script>
@endPushOnce
