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

<div
    class="al-car-card"
    x-data="{
        wishlisted: {{ $isWishlisted ? 'true' : 'false' }},
        loading: false,
        isCustomer: {{ $isAuth ? 'true' : 'false' }},
        toggleWishlist() {
            if (!this.isCustomer) { alOpenAuthDialog(); return; }
            if (this.loading) return;
            this.loading = true;
            fetch('{{ route('shop.api.customers.account.wishlist.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify({ product_id: {{ $productId ?? 0 }} })
            })
            .then(r => r.json())
            .then(() => { this.wishlisted = !this.wishlisted; })
            .finally(() => { this.loading = false; });
        },
        bookNow() {
            if (!this.isCustomer) { alOpenAuthDialog(); return; }
            window.location.href = '{{ $url }}';
        }
    }"
>
    {{-- Image / Thumb --}}
    <a href="{{ $url }}" class="al-car-thumb" aria-label="{{ $name }}">

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
            <span style="position:absolute;top:0.75rem;right:0.75rem;display:flex;align-items:center;gap:0.2rem;background:rgba(0,0,0,0.45);backdrop-filter:blur(4px);color:#fff;font-size:0.72rem;font-weight:600;padding:0.2rem 0.55rem;border-radius:999px;">
                <x-heroicon-o-photo style="width:0.85rem;height:0.85rem;flex-shrink:0;" />
                {{ $imagesCount }}
            </span>
        @endif

        {{-- Wishlist button (bottom-left) --}}
        @if ($productId)
            <button
                type="button"
                @click.prevent="toggleWishlist"
                :disabled="loading"
                :style="wishlisted ? 'color:#F0A500;' : 'color:#fff;'"
                style="position:absolute;bottom:0.75rem;left:0.75rem;width:2rem;height:2rem;border-radius:50%;background:rgba(0,0,0,0.4);backdrop-filter:blur(4px);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.2s,opacity 0.2s;"
                aria-label="{{ __('auto-leading-theme::app.common.add_to_wishlist') }}"
            >
                <x-heroicon-o-star x-show="!wishlisted" style="width:1rem;height:1rem;" />
                <x-heroicon-s-star x-show="wishlisted"  style="width:1rem;height:1rem;" />
            </button>
        @endif
    </a>

    {{-- Body --}}
    <div class="al-car-body">
        <h3>{{ $name }}</h3>

        <div class="al-car-footer">
            <p class="al-car-price">{{ $price }}</p>
            @if ($tag)
                <span class="al-car-tag">{{ $tag }}</span>
            @endif
        </div>

        {{-- Book Now --}}
        @if ($productId)
            <button
                type="button"
                @click="bookNow"
                :disabled="loading"
                class="al-car-cta"
                style="width:100%;justify-content:center;border:none;cursor:pointer;margin-top:0.85rem;gap:0.4rem;"
            >
                <x-heroicon-o-calendar-days style="width:1rem;height:1rem;flex-shrink:0;" />
                {{ __('auto-leading-theme::app.common.book_now') }}
            </button>
        @else
            <a
                href="{{ $url }}"
                class="al-car-cta"
                style="width:100%;justify-content:center;margin-top:0.85rem;gap:0.4rem;"
            >
                <x-heroicon-o-calendar-days style="width:1rem;height:1rem;flex-shrink:0;" />
                {{ __('auto-leading-theme::app.common.book_now') }}
            </a>
        @endif
    </div>
</div>
