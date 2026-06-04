<v-product-gallery ref="gallery">
    <x-shop::shimmer.products.gallery />
</v-product-gallery>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-product-gallery-template"
    >
        <div>
            <!-- For large screens greater than 1180px. -->
            <div class="sticky top-20 flex h-max gap-8 max-1180:hidden">
                <!-- Product Image and Videos Slider -->
                <div class="flex-24 h-509 flex min-w-[100px] max-w-[100px] flex-wrap place-content-start justify-center gap-2.5 overflow-y-auto overflow-x-hidden">
                    <!-- Arrow Up -->
                    <span
                        class="icon-arrow-up cursor-pointer text-2xl"
                        role="button"
                        aria-label="@lang('shop::app.components.products.carousel.previous')"
                        tabindex="0"
                        @click="swipeDown"
                        v-if="lengthOfMedia"
                    >
                    </span>

                    <!-- Swiper Container -->
                    <div
                        ref="swiperContainer"
                        class="flex flex-col max-h-[540px] gap-2.5 [&>*]:flex-[0] overflow-auto scroll-smooth scrollbar-hide"
                    >
                        <template v-for="(media, index) in [...media.images, ...media.videos]">
                            <video
                                v-if="media.type == 'videos'"
                                :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border ${isActiveMedia(index) ? 'pointer-events-none border border-navyBlue' : 'border-white'}`"
                                @click="change(media, index)"
                                alt="{{ $product->name }}"
                                tabindex="0"
                            >
                                <source
                                    :src="media.video_url"
                                    type="video/mp4"
                                />
                            </video>

                            <img
                                v-else
                                :class="`transparent max-h-[100px] min-w-[100px] cursor-pointer rounded-xl border ${isActiveMedia(index) ? 'pointer-events-none border border-navyBlue' : 'border-white'}`"
                                :src="media.small_image_url"
                                alt="{{ $product->name }}"
                                width="100"
                                height="100"
                                tabindex="0"
                                @click="change(media, index)"
                            />
                        </template>
                    </div>

                    <!-- Arrow Down -->
                    <span
                        class="icon-arrow-down cursor-pointer text-2xl"
                        v-if= "lengthOfMedia"
                        role="button"
                        aria-label="@lang('shop::app.components.products.carousel.previous')"
                        tabindex="0"
                        @click="swipeTop"
                    >
                    </span>
                </div>

                <!-- Product Base Image and Video with Shimmer-->
                <div
                    class="max-h-[610px] max-w-[560px]"
                    v-show="isMediaLoading"
                >
                    <div class="shimmer min-h-[607px] min-w-[560px] rounded-xl bg-zinc-200"></div>
                </div>

                <div
                    class="max-h-[610px] max-w-[560px]"
                    v-show="! isMediaLoading"
                >
                    <img
                        class="min-w-[450px] cursor-pointer rounded-xl"
                        :src="baseFile.path"
                        v-if="baseFile.type == 'image'"
                        alt="{{ $product->name }}"
                        width="560"
                        height="610"
                        tabindex="0"
                        @click="isImageZooming = !isImageZooming"
                        @load="onMediaLoad()"
                    />

                    <div
                        class="min-w-[450px] cursor-pointer rounded-xl"
                        tabindex="0"
                        v-if="baseFile.type == 'video'"
                    >
                        <video
                            controls
                            width="475"
                            alt="{{ $product->name }}"
                            @click="isImageZooming = !isImageZooming"
                            @loadeddata="onMediaLoad()"
                            :key="baseFile.path"
                        >
                            <source
                                :src="baseFile.path"
                                type="video/mp4"
                            />
                        </video>
                    </div>
                </div>
            </div>

            <!-- Product Images and Videos for Medium & Small Screen -->
            <div
                class="overflow-hidden 1180:hidden"
                v-show="isMediaLoading"
            >
                <div class="shimmer aspect-square max-h-screen w-screen bg-zinc-200"></div>
            </div>
        
            <div
                class="scrollbar-hide flex w-screen gap-8 overflow-auto max-sm:gap-5 1180:hidden"
                v-show="! isMediaLoading"
            >
                <!-- Show single media if there is only one image or video -->
                <template 
                    v-if="media.images.length + media.videos.length <= 1"
                    v-for="(media, index) in [...media.images, ...media.videos]"
                >
                    <div class="w-full flex-shrink-0 snap-center">
                        <video
                            v-if="media.type == 'videos'"
                            alt="{{ $product->name }}"
                            controls
                            @click="isImageZooming = !isImageZooming"
                            class="w-full"
                        >
                            <source
                                :src="media.video_url"
                                type="video/mp4"
                            />
                        </video>
            
                        <img
                            v-else
                            :src="media.large_image_url"
                            alt="{{ $product->name }}"
                            width="490"
                            height="550"
                            @click="isImageZooming = !isImageZooming"
                            class="w-full"
                        />
                    </div>
                </template>
                
                <!-- Show carousel if there is more than one image or video -->
                <v-product-carousel
                    v-else
                    :options="[...media.images, ...media.videos]"
                    @click="isImageZooming = !isImageZooming"
                >
                    <x-shop::shimmer.products.gallery />
                </v-product-carousel>
            </div>
            
            <!-- Gallery Images Zoomer -->
            <x-shop::image-zoomer 
                ::attachments="attachments" 
                ::is-image-zooming="isImageZooming" 
                ::initial-index="`media_${activeIndex}`"
            />
        </div>
    </script>

    <script type="module">
        app.component('v-product-gallery', {
            template: '#v-product-gallery-template',

            data() {
                return {
                    isImageZooming: false,

                    isMediaLoading: true,

                    media: {
                        images: @json(product_image()->getGalleryImages($product)),

                        videos: @json(product_video()->getVideos($product)),
                    },

                    baseFile: {
                        type: '',

                        path: ''
                    },

                    activeIndex: 0,

                    containerOffset: 110,
                };
            },

            watch: {
                'media.images': {
                    deep: true,

                    handler(newImages, oldImages) {
                        let selectedImage = newImages?.[this.activeIndex];

                        if (JSON.stringify(newImages) !== JSON.stringify(oldImages) && selectedImage?.large_image_url) {
                            this.baseFile.path = selectedImage.large_image_url;
                        }
                    },
                },
            },
        
            mounted() {
                if (this.media.images.length) {

                    this.baseFile.type = 'image';

                    this.baseFile.path = this.media.images[0].large_image_url;
                } else if (this.media.videos.length) {

                    this.baseFile.type = 'video';

                    this.baseFile.path = this.media.videos[0].video_url;
                }
            },

            computed: {
                lengthOfMedia() {
                    if (this.media.images.length) {
                        return [...this.media.images, ...this.media.videos].length > 5;
                    }
                },

                attachments() {
                    return [...this.media.images, ...this.media.videos].map(media => ({
                        url: media.type === 'videos' ? media.video_url : media.original_image_url,
                        
                        type: media.type === 'videos' ? 'video' : 'image',
                    }));
                },
            },

            methods: {
                isActiveMedia(index) {
                    return index === this.activeIndex;
                },
                
                onMediaLoad() {
                    this.isMediaLoading = false;
                },

                change(media, index) {
                    this.isMediaLoading = true;

                    if (media.type == 'videos') {
                        this.baseFile.type = 'video';

                        this.baseFile.path = media.video_url;

                        this.onMediaLoad();
                    } else {
                        this.baseFile.type = 'image';

                        this.baseFile.path = media.large_image_url;
                    }

                    if (index > this.activeIndex) {
                        this.swipeDown();
                    } else if (index < this.activeIndex) {
                        this.swipeTop();
                    }

                    this.activeIndex = index;
                },

                swipeTop() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop -= this.containerOffset;
                },

                swipeDown() {
                    const container = this.$refs.swiperContainer;

                    container.scrollTop += this.containerOffset;
                },
            },
        });
    </script>

    <script
        type="text/x-template"
        id="v-product-carousel-template"
    >
        <div class="relative m-auto flex w-full overflow-hidden">
            <div
                class="inline-flex translate-x-0 cursor-pointer transition-transform duration-700 ease-out will-change-transform"
                ref="sliderContainer"
            >
                <div
                    class="grid max-h-screen w-screen content-center bg-cover bg-no-repeat"
                    v-for="(media, index) in options"
                    ref="slide"
                >
                    <template v-if="media.type == 'videos'">
                        <video
                            controls
                            width="100%"
                            :alt="media.video_url"
                            :key="media.video_url"
                        >
                            <source
                                :src="media.video_url"
                                type="video/mp4"
                            />
                        </video>
                    </template>

                    <template v-else>
                        <img
                            class="aspect-square max-h-full w-full max-w-full select-none transition-transform duration-300 ease-in-out"
                            :src="media.large_image_url"
                            :alt="media.large_image_url"
                        />
                    </template>
                </div>
            </div>

            <div
                class="absolute bottom-3 left-0 flex w-full justify-center max-sm:bottom-2.5"
                v-if="options?.length > 1"
            >
                <div
                    v-for="(media, index) in options"
                    class="mx-1 h-1.5 w-1.5 cursor-pointer rounded-full"
                    :class="{ 'bg-navyBlue': index === Math.abs(currentIndex), 'opacity-30 bg-gray-500': index !== Math.abs(currentIndex) }"
                    role="button"
                >
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component("v-product-carousel", {
            template: '#v-product-carousel-template',

            props: ['options'],

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
                    viewportWidth: window.innerWidth,
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

                this.init();

                window.addEventListener('resize', this.onResize);
            },

            watch: {
                options: function() {
                    this.slider = this.$refs.sliderContainer;

                    if (
                        this.$refs.slide
                        && typeof this.$refs.slide[Symbol.iterator] === 'function'
                    ) {
                        this.slides = Array.from(this.$refs.slide);
                    }

                    this.resetIndex();

                    this.init();
                }
            },

            methods: {
                init() {
                    this.direction = document.dir;

                    if (this.direction === 'rtl') {
                        this.startFrom = -1;
                    }

                    this.slides.forEach((slide, index) => {
                        slide.querySelector('img')?.addEventListener('dragstart', (e) => e.preventDefault());
                        slide.addEventListener('touchstart', this.handleDragStart, { passive: true });
                        slide.addEventListener('touchend', this.handleDragEnd);
                        slide.addEventListener('touchmove', this.handleDrag, { passive: true });
                    });

                    this.setPositionByIndex();
                },

                resetIndex() {
                    if (this.currentIndex >= this.slides.length) {
                        this.currentIndex = this.slides.length - 1;
                    }

                    this.setPositionByIndex();
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

                    if (this.direction === 'ltr') {
                        if (movedBy < -100 && this.currentIndex < this.slides.length - 1) {
                            this.currentIndex += 1;
                        }

                        if (movedBy > 100 && this.currentIndex > 0) {
                            this.currentIndex -= 1;
                        }
                    } else {
                        if (movedBy > 100 && this.currentIndex < this.slides.length - 1) {
                            if (Math.abs(this.currentIndex) !== this.slides.length - 1) {
                                this.currentIndex -= 1;
                            }
                        }

                        if (movedBy < -100 && this.currentIndex < 0) {
                            this.currentIndex += 1;
                        }
                    }

                    this.setPositionByIndex();
                },

                animation() {
                    this.setSliderPosition();

                    if (this.isDragging) {
                        requestAnimationFrame(this.animation);
                    }
                },

                setPositionByIndex() {
                    this.currentTranslate = this.currentIndex * -this.viewportWidth;
                    this.prevTranslate = this.currentTranslate;
                    this.setSliderPosition();
                },

                setSliderPosition() {
                    if (this.slider) {
                        this.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                    }
                },

                onResize() {
                    this.viewportWidth = window.innerWidth;
                    this.setPositionByIndex();
                },
            },
        });
    </script>
@endpushOnce