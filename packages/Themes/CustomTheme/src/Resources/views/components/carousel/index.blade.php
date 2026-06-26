@props(['options'])

@php
    $carouselImages = $options['images'] ?? [];

    $firstImage = data_get($carouselImages, '0.image');

    $firstImageTitle = data_get($carouselImages, '0.title');
@endphp

@if ($firstImage)
    {{--
        Preload the LCP image in <head> so the browser starts fetching it
        before HTML parse reaches the <img> tag. Directly targets the LCP
        "resource load delay" subpart Lighthouse reports as the biggest
        contributor on this page.
    --}}
    @push('meta')
        <link
            rel="preload"
            as="image"
            href="{{ str_replace('storage', 'cache/small', $firstImage) }}"
            imagesrcset="{{ $firstImage }} 1920w, {{ str_replace('storage', 'cache/large', $firstImage) }} 1280w, {{ str_replace('storage', 'cache/medium', $firstImage) }} 1024w, {{ str_replace('storage', 'cache/small', $firstImage) }} 768w"
            imagesizes="100vw"
            fetchpriority="high"
        >
    @endpush
@endif

@php
    // Promo-specific portrait banner shown only on mobile (different ratio from the
    // landscape hero). Lives here in the theme override so it never touches header/footer.
    $alMobileBanner = 'storage/theme/1/almobilebanner0000042137promo.webp';
@endphp

{{-- Mobile-only hero banner (portrait). Hidden on md+ where the carousel shows. --}}
<a href="/for-rent" class="al-mobile-hero md:hidden block">
    <img
        src="{{ asset($alMobileBanner) }}"
        class="w-full h-auto select-none"
        style="display:block;width:100%;height:auto"
        alt="{{ $firstImageTitle ?? trans('shop::app.home.index.image-carousel') }}"
        fetchpriority="high"
    >
</a>

<v-carousel :images="{{ json_encode($carouselImages) }}" class="al-desktop-hero hidden md:block">
    <div class="overflow-hidden">
        @if ($firstImage)
            {{--
                Server-rendered first slide so the browser can discover and
                fetch the LCP image immediately, before Vue mounts the
                carousel. `sizes="100vw"` declares the actual rendered width
                (the img has `w-screen`) so the browser picks the smallest
                srcset variant that satisfies viewport_px × DPR — mobile
                412 × 1.75 ≈ 721 → 768w small variant. The inline `style`
                supplies width/aspect-ratio so the LCP element can paint
                before the Tailwind CSS bundle finishes parsing on slow
                mobile CPU.
            --}}
            <img
                src="{{ $firstImage }}"
                srcset="{{ $firstImage }} 1920w, {{ str_replace('storage', 'cache/large', $firstImage) }} 1280w, {{ str_replace('storage', 'cache/medium', $firstImage) }} 1024w, {{ str_replace('storage', 'cache/small', $firstImage) }} 768w"
                sizes="100vw"
                class="aspect-[2.743/1] max-h-screen w-screen select-none object-cover"
                style="width:100vw;aspect-ratio:2.743/1;max-height:100vh;object-fit:cover;display:block"
                alt="{{ $firstImageTitle ?? trans('shop::app.home.index.image-carousel') }}"
                fetchpriority="high"
                decoding="sync"
            >
        @else
            <div class="shimmer aspect-[2.743/1] max-h-screen w-screen"></div>
        @endif
    </div>
</v-carousel>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-carousel-template"
    >
        <div class="relative m-auto flex w-full overflow-hidden">
            <!-- Slider -->
            <div
                class="inline-flex translate-x-0 cursor-pointer transition-transform duration-700 ease-out will-change-transform"
                ref="sliderContainer"
            >
                <div
                    class="al-hero-slide relative max-h-screen w-screen bg-cover bg-no-repeat"
                    v-for="(image, index) in images"
                    :key="index"
                    @click="visitLink(image)"
                    ref="slide"
                >
                    <x-shop::media.images.lazy
                        class="aspect-[2.743/1] max-h-full w-full max-w-full select-none transition-transform duration-300 ease-in-out will-change-transform"
                        ::lazy="index === 0 ? false : true"
                        ::src="image.image"
                        ::srcset="image.image + ' 1920w, ' + image.image.replace('storage', 'cache/large') + ' 1280w,' + image.image.replace('storage', 'cache/medium') + ' 1024w, ' + image.image.replace('storage', 'cache/small') + ' 768w'"
                        sizes="100vw"
                        ::alt="image?.title || 'Carousel Image ' + (index + 1)"
                        tabindex="0"
                        ::fetchpriority="index === 0 ? 'high' : 'low'"
                        ::decoding="index === 0 ? 'sync' : 'async'"
                    />

                    <!-- Headline + CTA overlay (custom-theme) -->
                    <div class="al-hero-overlay" v-if="image.title">
                        <div class="al-hero-overlay-inner">
                            <h2 class="al-hero-title" v-html="image.title"></h2>
                            <span class="al-hero-cta" v-if="image.link">了解更多</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <span
                class="icon-arrow-left absolute left-2.5 top-1/2 -mt-[22px] hidden w-auto rounded-full bg-black/80 p-3 text-2xl font-bold text-white opacity-30 transition-all md:inline-block"
                :class="{
                    'cursor-not-allowed': direction == 'ltr' && currentIndex == 0,
                    'cursor-pointer hover:opacity-100': direction == 'ltr' ? currentIndex > 0 : currentIndex <= 0
                }"
                role="button"
                aria-label="@lang('shop::components.carousel.previous')"
                tabindex="0"
                v-if="images?.length >= 2"
                @click="navigate('prev')"
            >
            </span>

            <span
                class="icon-arrow-right absolute right-2.5 top-1/2 -mt-[22px] hidden w-auto rounded-full bg-black/80 p-3 text-2xl font-bold text-white opacity-30 transition-all md:inline-block"
                :class="{
                    'cursor-not-allowed': direction == 'rtl' && currentIndex == 0,
                    'cursor-pointer hover:opacity-100': direction == 'rtl' ? currentIndex < 0 : currentIndex >= 0
                }"
                role="button"
                aria-label="@lang('shop::components.carousel.next')"
                tabindex="0"
                v-if="images?.length >= 2"
                @click="navigate('next')"
            >
            </span>

            <!-- Pagination -->
            <div class="absolute bottom-5 left-0 flex w-full justify-center max-md:bottom-3.5 max-sm:bottom-2.5">
                <div
                    v-for="(image, index) in images"
                    :key="index"
                    class="sm:p-2.5 mx-1 h-3 w-3 cursor-pointer rounded-full max-md:h-2 max-md:w-2 max-sm:h-1.5 max-sm:w-1.5
                    p-2 focus:outline-none"
                    :class="{ 'bg-navyBlue': index === Math.abs(currentIndex), 'opacity-30 bg-gray-500': index !== Math.abs(currentIndex) }"
                    role="button"
                    tabindex="0"
                    :aria-label="'Go to slide ' + (index + 1)"
                    @click="navigateByPagination(index)"
                    @keydown.enter="navigateByPagination(index)"
                    @keydown.space.prevent="navigateByPagination(index)"
                >
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component("v-carousel", {
            template: '#v-carousel-template',

            props: ['images'],

            data() {
                return {
                    isDragging: false,
                    startPos: 0,
                    currentTranslate: 0,
                    prevTranslate: 0,
                    animationID: 0,
                    currentIndex: 0,
                    slider: '',
                    slides: [],
                    autoPlayInterval: null,
                    direction: 'ltr',
                    startFrom: 1,
                };
            },

            mounted() {
                this.slider = this.$refs.sliderContainer;

                if (
                    this.$refs.slide
                    && typeof this.$refs.slide[Symbol.iterator] === 'function'
                ) {
                    this.slides = Array.from(this.$refs.slide);
                }

                // Use requestIdleCallback for non-critical initialization
                if ('requestIdleCallback' in window) {
                    requestIdleCallback(() => {
                        this.init();
                        setTimeout(() => {
                            this.play();
                        }, 4000);
                    });
                } else {
                    setTimeout(() => {
                        this.init();
                        setTimeout(() => {
                            this.play();
                        }, 4000);
                    });
                }
            },

            beforeUnmount() {
                this.cleanup();
            },

            methods: {
                init() {
                    this.direction = document.dir;

                    if (this.direction == 'rtl') {
                        this.startFrom = -1;
                    }

                    this.slides.forEach((slide, index) => {
                        slide.querySelector('img')?.addEventListener('dragstart', (e) => e.preventDefault());

                        slide.addEventListener('mousedown', this.handleDragStart);

                        slide.addEventListener('touchstart', this.handleDragStart, { passive: true });

                        slide.addEventListener('mouseup', this.handleDragEnd);

                        slide.addEventListener('mouseleave', this.handleDragEnd);

                        slide.addEventListener('touchend', this.handleDragEnd, { passive: true });

                        slide.addEventListener('mousemove', this.handleDrag);

                        slide.addEventListener('touchmove', this.handleDrag, { passive: true });
                    });

                    window.addEventListener('resize', this.setPositionByIndex);
                },

                handleDragStart(event) {
                    this.startPos = event.type === 'mousedown' ? event.clientX : event.touches[0].clientX;

                    this.isDragging = true;

                    this.animationID = requestAnimationFrame(this.animation);
                },

                handleDrag(event) {
                    if (! this.isDragging) {
                        return;
                    }

                    const currentPosition = event.type === 'mousemove' ? event.clientX : event.touches[0].clientX;

                    this.currentTranslate = this.prevTranslate + currentPosition - this.startPos;
                },

                handleDragEnd(event) {
                    clearInterval(this.autoPlayInterval);

                    cancelAnimationFrame(this.animationID);

                    this.isDragging = false;

                    const movedBy = this.currentTranslate - this.prevTranslate;

                    if (this.direction == 'ltr') {
                        if (
                            movedBy < -100
                            && this.currentIndex < this.slides.length - 1
                        ) {
                            this.currentIndex += 1;
                        }

                        if (
                            movedBy > 100
                            && this.currentIndex > 0
                        ) {
                            this.currentIndex -= 1;
                        }
                    } else {
                        if (
                            movedBy > 100
                            && this.currentIndex < this.slides.length - 1
                        ) {
                            if (Math.abs(this.currentIndex) != this.slides.length - 1) {
                                this.currentIndex -= 1;
                            }
                        }

                        if (
                            movedBy < -100
                            && this.currentIndex < 0
                        ) {
                            this.currentIndex += 1;
                        }
                    }

                    this.setPositionByIndex();

                    this.play();
                },

                animation() {
                    this.setSliderPosition();

                    if (this.isDragging) {
                        requestAnimationFrame(this.animation);
                    }
                },

                setPositionByIndex() {
                    this.currentTranslate = this.currentIndex * -window.innerWidth;

                    this.prevTranslate = this.currentTranslate;

                    this.setSliderPosition();
                },

                setSliderPosition() {
                    if (this.slider) {
                        this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                    }
                },

                visitLink(image) {
                    if (image.link) {
                        window.location.href = image.link;
                    }
                },

                navigate(type) {
                    clearInterval(this.autoPlayInterval);

                    if (this.direction === 'rtl') {
                        type === 'next' ? this.prev() : this.next();
                    } else {
                        type === 'next' ? this.next() : this.prev();
                    }

                    this.setPositionByIndex();

                    this.play();
                },

                next() {
                    this.currentIndex = (this.currentIndex + this.startFrom) % this.images.length;
                },

                prev() {
                    this.currentIndex = this.direction == 'ltr'
                        ? this.currentIndex > 0 ? this.currentIndex - 1 : 0
                        : this.currentIndex < 0 ? this.currentIndex + 1 : 0;
                },

                navigateByPagination(index) {
                    this.direction == 'rtl' ? index = -index : '';

                    clearInterval(this.autoPlayInterval);

                    this.currentIndex = index;

                    this.setPositionByIndex();

                    this.play();
                },

                play() {
                    clearInterval(this.autoPlayInterval);

                    this.autoPlayInterval = setInterval(() => {
                        this.currentIndex = (this.currentIndex + this.startFrom) % this.images.length;

                        this.setPositionByIndex();
                    }, 5000);
                },

                cleanup() {
                    // Clear intervals and animation frames
                    clearInterval(this.autoPlayInterval);
                    cancelAnimationFrame(this.animationID);

                    // Remove event listeners
                    if (this.slides) {
                        this.slides.forEach(slide => {
                            slide.removeEventListener('mousedown', this.handleDragStart);
                            slide.removeEventListener('touchstart', this.handleDragStart);
                            slide.removeEventListener('mouseup', this.handleDragEnd);
                            slide.removeEventListener('mouseleave', this.handleDragEnd);
                            slide.removeEventListener('touchend', this.handleDragEnd);
                            slide.removeEventListener('mousemove', this.handleDrag);
                            slide.removeEventListener('touchmove', this.handleDrag);
                        });
                    }

                    window.removeEventListener('resize', this.setPositionByIndex);
                },
            },
        });
    </script>
@endpushOnce

@pushOnce('styles')
    <style>
        /* Responsive hero swap: portrait banner on mobile, landscape carousel on desktop.
           Explicit CSS (not only Tailwind utilities) so it works regardless of build purge. */
        .al-mobile-hero { display: block; }
        .al-desktop-hero { display: none; }
        @media (min-width: 768px) {
            .al-mobile-hero { display: none !important; }
            .al-desktop-hero { display: block !important; }
        }
        .al-hero-slide { position: relative; }
        .al-hero-overlay {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            pointer-events: none;
        }
        .al-hero-overlay-inner {
            margin-left: 8%;
            max-width: 520px;
            color: #fff;
            text-shadow: 0 2px 14px rgba(0, 0, 0, .5);
        }
        .al-hero-title {
            font-size: clamp(28px, 4vw, 64px);
            line-height: 1.1;
            font-weight: 800;
            margin-bottom: 24px;
        }
        .al-hero-cta {
            display: inline-block;
            background: #D4002A;
            color: #fff;
            padding: 14px 34px;
            border-radius: 999px;
            font-weight: 700;
            pointer-events: auto;
        }
        [dir="rtl"] .al-hero-overlay-inner { margin-left: 0; margin-right: 8%; }
        @media (max-width: 768px) {
            .al-hero-overlay-inner { margin-left: 6%; max-width: 82%; }
            .al-hero-title { margin-bottom: 14px; }
            .al-hero-cta { padding: 10px 24px; }
        }
    </style>
@endPushOnce

{{-- Homepage interactions: step modals + FAQ accordion (no URL hash, no scroll jump, smooth). --}}
{{-- Lives in the carousel override (homepage hero) so it never touches header/footer. --}}
@pushOnce('scripts')
    <script>
    /* Homepage interactions via event delegation on document — binds once, immune to
       render timing / Vue re-mounts. Modal target read from href (#id) since Purify strips data-*. */
    (function () {
        function closeModal(m) {
            if (!m) return;
            m.classList.remove('is-open');
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function (e) {
            // open step modal
            var opener = e.target.closest('.al-modal-open');
            if (opener) {
                e.preventDefault();
                var href = opener.getAttribute('href') || '';
                var id = href.charAt(0) === '#' ? href.slice(1) : (opener.getAttribute('data-modal') || '');
                var m = id ? document.getElementById(id) : null;
                if (m) { m.classList.add('is-open'); document.body.style.overflow = 'hidden'; }
                return;
            }
            // close via X button
            var x = e.target.closest('.al-modal-x');
            if (x) {
                e.preventDefault();
                closeModal(x.closest('.al-smodal'));
                return;
            }
            // close via backdrop click (clicked the overlay itself, not its content)
            if (e.target.classList && e.target.classList.contains('al-smodal')) {
                closeModal(e.target);
                return;
            }
            // FAQ accordion toggle — single-open: collapse siblings when one expands
            var q = e.target.closest('.al-faq .item > .q');
            if (q) {
                e.preventDefault();
                var item = q.parentElement;
                var willOpen = !item.classList.contains('is-open');
                // collapse all items in this FAQ group
                var group = item.closest('.al-faq');
                if (group) {
                    group.querySelectorAll('.item.is-open').forEach(function (other) {
                        other.classList.remove('is-open');
                        var oa = other.querySelector('.a');
                        if (oa) oa.style.maxHeight = '0px';
                    });
                }
                // open the clicked one (if it wasn't already open)
                if (willOpen) {
                    item.classList.add('is-open');
                    var ans = item.querySelector('.a');
                    if (ans) ans.style.maxHeight = ans.scrollHeight + 'px';
                }
                return;
            }
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                var open = document.querySelector('.al-smodal.is-open');
                if (open) closeModal(open);
            }
        });
    })();
    </script>
@endPushOnce
