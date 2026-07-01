@php
    $logoMaxHeight  = (int) (core()->getConfigData('general.design.navbar.logo_max_height') ?? 40);
    $logoFit        = core()->getConfigData('general.design.navbar.logo_fit') ?? 'contain';
    $showStoreName      = (bool) core()->getConfigData('general.design.navbar.show_store_name_desktop');
    $storeNameFontSize  = (int) (core()->getConfigData('general.design.navbar.store_name_font_size') ?? 24);
    $showManualMenu = (bool) core()->getConfigData('general.design.navbar.show_manual_menu');
    $showCategories         = (bool) (core()->getConfigData('general.design.navbar.show_categories') ?? 1);
    $categoryView           = core()->getConfigData('general.design.categories.category_view');
    $showCategoriesDropdown = (bool) core()->getConfigData('general.design.navbar.show_categories_dropdown')
                              && $categoryView !== 'sidebar';
    $categoriesLabel        = core()->getConfigData('general.design.navbar.categories_dropdown_label') ?: trans('shop::app.components.layouts.header.desktop.bottom.all');
    $categoriesOrder        = (int) (core()->getConfigData('general.design.navbar.categories_order') ?? 0);

    $navItems = [];

    if ($showManualMenu) {
        foreach (json_decode(core()->getConfigData('general.design.navbar.menu_items') ?? '[]', true) ?? [] as $item) {
            if (! empty($item['label']) && ! empty($item['url'])) {
                $navItems[] = ['type' => 'link', 'order' => (int) ($item['order'] ?? 0), 'item' => $item];
            }
        }
    }

    if ($showCategories) {
        $navItems[] = ['type' => 'categories', 'order' => $categoriesOrder];
    }

    usort($navItems, fn ($a, $b) => $a['order'] <=> $b['order']);
@endphp

{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.before') !!}

<div class="flex min-h-[78px] w-full justify-between border border-b border-l-0 border-r-0 border-t-0 bg-white px-[60px] max-1180:px-8">
    <!-- Left Navigation Section -->
    <div class="flex items-center gap-x-8 max-[1180px]:gap-x-5">
        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.before') !!}

        <a
            href="{{ route('shop.home.index') }}"
            class="flex items-center gap-2"
            aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.bagisto')"
        >
            <img
                src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                style="width: {{ $logoMaxHeight }}px; min-width: {{ $logoMaxHeight }}px; height: auto;"
                class="object-{{ $logoFit }} object-center"
                alt="{{ config('app.name') }}"
            >

            @if ($showStoreName)
                <span class="font-bold text-gray-800" style="font-size: {{ $storeNameFontSize }}px; line-height: 1;">
                    {{ core()->getCurrentChannel()->name }}
                </span>
            @endif
        </a>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.logo.after') !!}

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.before') !!}

        <div class="flex items-center">
        @foreach ($navItems as $navItem)
            @if ($navItem['type'] === 'link')
                <a href="{{ $navItem['item']['url'] }}" class="flex h-[77px] items-center border-b-4 border-transparent px-3 uppercase whitespace-nowrap hover:border-navyBlue">
                    {{ $navItem['item']['label'] }}
                </a>
            @else
                @if ($showCategoriesDropdown)
                    <v-custom-categories-dropdown categories-label="{{ $categoriesLabel }}">
                        <div class="flex items-center gap-5">
                            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                        </div>
                    </v-custom-categories-dropdown>
                @else
                    <v-desktop-category>
                        <div class="flex items-center gap-5">
                            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
                        </div>
                    </v-desktop-category>
                @endif
            @endif
        @endforeach
        </div>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.category.after') !!}
    </div>

    <!-- Right Navigation Section -->
    <div class="flex items-center gap-x-9 max-[1100px]:gap-x-6 max-lg:gap-x-8">

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.before') !!}

        <v-search-bar
            search-route="{{ route('shop.search.index') }}"
            current-query="{{ request('query') }}"
            min-length="{{ core()->getConfigData('catalog.products.search.min_query_length') }}"
            max-length="{{ core()->getConfigData('catalog.products.search.max_query_length') }}"
            placeholder="@lang('shop::app.components.layouts.header.desktop.bottom.search-text')"
            search-label="@lang('shop::app.components.layouts.header.desktop.bottom.search')"
            submit-label="@lang('shop::app.components.layouts.header.desktop.bottom.submit')"
        >
            <span
                class="icon-search cursor-pointer text-2xl"
                role="button"
                aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.search')"
                tabindex="0"
            ></span>
        </v-search-bar>

        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.search_bar.after') !!}

        <!-- Right Navigation Links -->
        <div class="flex items-center gap-x-8 max-[1100px]:gap-x-6 max-lg:gap-x-8">

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.before') !!}

            <!-- Compare -->
            @if(core()->getConfigData('catalog.products.settings.compare_option'))
                <a
                    href="{{ route('shop.compare.index') }}"
                    aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.compare')"
                >
                    <span
                        class="inline-block text-2xl cursor-pointer icon-compare"
                        role="presentation"
                    ></span>
                </a>
            @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.compare.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.before') !!}

            <!-- Mini cart -->
            @if(core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                @include('shop::checkout.cart.mini-cart')
            @endif

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.mini_cart.after') !!}

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.before') !!}

            <!-- user profile -->
            <x-shop::dropdown position="bottom-{{ core()->getCurrentLocale()->direction === 'ltr' ? 'right' : 'left' }}">
                <x-slot:toggle>
                    <span
                        class="inline-block text-2xl cursor-pointer icon-users"
                        role="button"
                        aria-label="@lang('shop::app.components.layouts.header.desktop.bottom.profile')"
                        tabindex="0"
                    ></span>
                </x-slot>

                <!-- Guest Dropdown -->
                @guest('customer')
                    <x-slot:content>
                        <div class="grid gap-2.5">
                            <p class="text-xl font-dmserif">
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome-guest')
                            </p>

                            <p class="text-sm">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>

                        <p class="w-full mt-3 border border-zinc-200"></p>

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.before') !!}

                        <div class="flex gap-4 mt-6">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_in_button.before') !!}

                            <a
                                href="{{ route('shop.customer.session.create') }}"
                                class="block m-0 mx-auto text-base text-center primary-button w-max rounded-2xl px-7 max-md:rounded-lg ltr:ml-0 rtl:mr-0"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-in')
                            </a>

                            <a
                                href="{{ route('shop.customers.register.index') }}"
                                class="block m-0 mx-auto text-base text-center border-2 secondary-button w-max rounded-2xl px-7 max-md:rounded-lg max-md:py-3 ltr:ml-0 rtl:mr-0"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.sign-up')
                            </a>

                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.sign_up_button.after') !!}
                        </div>

                        {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.customers_action.after') !!}
                    </x-slot>
                @endguest

                <!-- Customers Dropdown -->
                @auth('customer')
                    <x-slot:content class="!p-0">
                        <div class="grid gap-2.5 p-5 pb-0">
                            <p class="text-xl font-dmserif" v-pre>
                                @lang('shop::app.components.layouts.header.desktop.bottom.welcome')'
                                {{ auth()->guard('customer')->user()->first_name }}
                            </p>

                            <p class="text-sm">
                                @lang('shop::app.components.layouts.header.desktop.bottom.dropdown-text')
                            </p>
                        </div>

                        <p class="w-full mt-3 border border-zinc-200"></p>

                        <div class="mt-2.5 grid gap-1 pb-2.5">
                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.before') !!}

                            <a
                                class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                                href="{{ route('shop.customers.account.profile.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.profile')
                            </a>

                            <a
                                class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                                href="{{ route('shop.customers.account.orders.index') }}"
                            >
                                @lang('shop::app.components.layouts.header.desktop.bottom.orders')
                            </a>

                            @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                <a
                                    class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                                    href="{{ route('shop.customers.account.wishlist.index') }}"
                                >
                                    @lang('shop::app.components.layouts.header.desktop.bottom.wishlist')
                                </a>
                            @endif

                            <!--Customers logout-->
                            @auth('customer')
                                <x-shop::form
                                    method="DELETE"
                                    action="{{ route('shop.customer.session.destroy') }}"
                                    id="customerLogout"
                                />

                                <a
                                    class="px-5 py-2 text-base cursor-pointer hover:bg-gray-100"
                                    href="{{ route('shop.customer.session.destroy') }}"
                                    onclick="event.preventDefault(); document.getElementById('customerLogout').submit();"
                                >
                                    @lang('shop::app.components.layouts.header.desktop.bottom.logout')
                                </a>
                            @endauth

                            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile_dropdown.links.after') !!}
                        </div>
                    </x-slot>
                @endauth
            </x-shop::dropdown>

            {!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.profile.after') !!}
        </div>
    </div>
</div>

@pushOnce('scripts')
    {{-- =====================================================================
         v-search-bar — clickable search icon that opens a full-width overlay
         ===================================================================== --}}
    <script
        type="text/x-template"
        id="v-search-bar-template"
    >
        <div @keydown.escape.window="closeSearch">
            <!-- Search Icon Trigger -->
            <div @click="openSearch">
                <slot></slot>
            </div>

            <!-- Backdrop -->
            <Transition
                enter-active-class="transition duration-150 ease-out"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition duration-100 ease-in"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="isOpen"
                    class="fixed inset-0 z-10 bg-black/40"
                    :style="{ top: panelTop + 'px' }"
                    @click="closeSearch"
                ></div>
            </Transition>

            <!-- Search Panel (drops below navbar) -->
            <Transition
                enter-active-class="transition duration-200 ease-out"
                enter-from-class="opacity-0 -translate-y-1"
                enter-to-class="opacity-100 translate-y-0"
                leave-active-class="transition duration-150 ease-in"
                leave-from-class="opacity-100 translate-y-0"
                leave-to-class="opacity-0 -translate-y-1"
            >
                <div
                    v-if="isOpen"
                    class="fixed left-0 right-0 z-10 bg-white border-b border-zinc-200 shadow-md px-[60px] py-4 flex items-center gap-4"
                    :style="{ top: panelTop + 'px' }"
                >
                    <form
                        :action="searchRoute"
                        class="flex flex-1 items-center"
                        role="search"
                        @submit="closeSearch"
                    >
                        <label :for="inputId" class="sr-only">@{{ searchLabel }}</label>

                        <div class="relative flex flex-1 items-center">
                            <div class="icon-search pointer-events-none absolute top-2.5 flex items-center text-xl ltr:left-3 rtl:right-3"></div>

                            <input
                                :id="inputId"
                                ref="searchInput"
                                type="text"
                                name="query"
                                :value="currentQuery"
                                :minlength="minLength"
                                :maxlength="maxLength"
                                :placeholder="placeholder"
                                :aria-label="searchLabel"
                                aria-required="true"
                                pattern="[^\\]+"
                                required
                                class="block w-full py-3 text-xs font-medium text-gray-900 transition-all border border-transparent rounded-lg bg-zinc-100 px-11 hover:border-gray-400 focus:border-gray-400 focus:outline-none"
                            >
                        </div>

                        <button type="submit" class="hidden" :aria-label="submitLabel"></button>
                    </form>

                    <!-- Close Button -->
                    <button
                        type="button"
                        class="flex-shrink-0 flex items-center justify-center w-10 h-10 rounded-full hover:bg-zinc-100 transition"
                        @click="closeSearch"
                        aria-label="Close search"
                    >
                        <span class="icon-cancel text-xl text-zinc-500"></span>
                    </button>
                </div>
            </Transition>
        </div>
    </script>

    {{-- =====================================================================
         v-desktop-category — exact copy of the default theme component,
         used when the "categories dropdown" setting is OFF.
         ===================================================================== --}}
    <script
        type="text/x-template"
        id="v-desktop-category-template"
    >
        <!-- Loading State -->
        <div
            class="flex items-center gap-5"
            v-if="isLoading"
        >
            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
        </div>

        <!-- Default category layout -->
        <div
            class="flex items-center"
            v-else-if="'{{ core()->getConfigData('general.design.categories.category_view') }}' !== 'sidebar'"
        >
            <div
                class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                v-for="category in categories"
            >
                <span>
                    <a :href="category.url" class="inline-block px-3 uppercase whitespace-nowrap">
                        @{{ category.name }}
                    </a>
                </span>

                <div
                    class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                    v-if="category.children && category.children.length"
                >
                    <div class="flex justify-between gap-x-[70px]">
                        <div
                            class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                            v-for="pairCategoryChildren in pairCategoryChildren(category)"
                        >
                            <template v-for="secondLevelCategory in pairCategoryChildren">
                                <p class="font-medium text-navyBlue">
                                    <a :href="secondLevelCategory.url">
                                        @{{ secondLevelCategory.name }}
                                    </a>
                                </p>

                                <ul
                                    class="grid grid-cols-[1fr] gap-3"
                                    v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                >
                                    <li
                                        class="text-sm font-medium text-zinc-500"
                                        v-for="thirdLevelCategory in secondLevelCategory.children"
                                    >
                                        <a :href="thirdLevelCategory.url">
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </li>
                                </ul>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar category layout -->
        <div v-else>
            <div class="flex items-center">
                <!-- "All" toggle button -->
                <div
                    class="flex h-[77px] cursor-pointer items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                    @click="toggleCategoryDrawer"
                >
                    <span class="flex items-center gap-1 px-3 uppercase whitespace-nowrap">
                        <span class="text-xl icon-hamburger"></span>
                        @lang('shop::app.components.layouts.header.desktop.bottom.all')
                    </span>
                </div>

                <!-- First 4 categories -->
                <div
                    class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
                    v-for="category in categories.slice(0, 4)"
                >
                    <span>
                        <a :href="category.url" class="inline-block px-3 uppercase whitespace-nowrap">
                            @{{ category.name }}
                        </a>
                    </span>

                    <div
                        class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9"
                        v-if="category.children && category.children.length"
                    >
                        <div class="flex justify-between gap-x-[70px]">
                            <div
                                class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                                v-for="pairCategoryChildren in pairCategoryChildren(category)"
                            >
                                <template v-for="secondLevelCategory in pairCategoryChildren">
                                    <p class="font-medium text-navyBlue">
                                        <a :href="secondLevelCategory.url">
                                            @{{ secondLevelCategory.name }}
                                        </a>
                                    </p>

                                    <ul
                                        class="grid grid-cols-[1fr] gap-3"
                                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                    >
                                        <li
                                            class="text-sm font-medium text-zinc-500"
                                            v-for="thirdLevelCategory in secondLevelCategory.children"
                                        >
                                            <a :href="thirdLevelCategory.url">
                                                @{{ thirdLevelCategory.name }}
                                            </a>
                                        </li>
                                    </ul>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Category Drawer -->
            <x-shop::drawer
                position="left"
                width="400px"
                ::is-active="isDrawerActive"
                @toggle="onDrawerToggle"
                @close="onDrawerClose"
            >
                <x-slot:toggle></x-slot>

                <x-slot:header class="border-b border-gray-200">
                    <div class="flex items-center justify-between w-full">
                        <p class="text-xl font-medium">
                            @lang('shop::app.components.layouts.header.desktop.bottom.categories')
                        </p>
                    </div>
                </x-slot>

                <x-slot:content class="!px-0">
                    <div class="relative h-full overflow-hidden">
                        <div
                            class="flex h-full transition-transform duration-300"
                            :class="{
                                'ltr:translate-x-0 rtl:translate-x-0': currentViewLevel !== 'third',
                                'ltr:-translate-x-full rtl:translate-x-full': currentViewLevel === 'third'
                            }"
                        >
                            <!-- First level -->
                            <div class="h-[calc(100vh-74px)] w-full flex-shrink-0 overflow-auto">
                                <div class="py-4">
                                    <div
                                        v-for="category in categories"
                                        :key="category.id"
                                        :class="{'mb-2': category.children && category.children.length}"
                                    >
                                        <div class="flex items-center justify-between px-6 py-2 transition-colors duration-200 cursor-pointer hover:bg-gray-100">
                                            <a :href="category.url" class="text-base font-medium text-black">
                                                @{{ category.name }}
                                            </a>
                                        </div>

                                        <div v-if="category.children && category.children.length">
                                            <div
                                                v-for="secondLevelCategory in category.children"
                                                :key="secondLevelCategory.id"
                                            >
                                                <div
                                                    class="flex items-center justify-between px-6 py-2 transition-colors duration-200 cursor-pointer hover:bg-gray-100"
                                                    @click="showThirdLevel(secondLevelCategory, category, $event)"
                                                >
                                                    <a :href="secondLevelCategory.url" class="text-sm font-normal">
                                                        @{{ secondLevelCategory.name }}
                                                    </a>

                                                    <span
                                                        v-if="secondLevelCategory.children && secondLevelCategory.children.length"
                                                        class="icon-arrow-right rtl:icon-arrow-left"
                                                    ></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Third level -->
                            <div
                                class="flex-shrink-0 w-full h-full"
                                v-if="currentViewLevel === 'third'"
                            >
                                <div class="px-6 py-4 border-b border-gray-200">
                                    <button
                                        @click="goBackToMainView"
                                        class="flex items-center justify-center gap-2 focus:outline-none"
                                        aria-label="Go back"
                                    >
                                        <span class="text-lg icon-arrow-left rtl:icon-arrow-right"></span>
                                        <p class="text-base font-medium text-black">
                                            @lang('shop::app.components.layouts.header.desktop.bottom.back-button')
                                        </p>
                                    </button>
                                </div>

                                <div class="py-4">
                                    <div
                                        v-for="thirdLevelCategory in currentSecondLevelCategory?.children"
                                        :key="thirdLevelCategory.id"
                                        class="mb-2"
                                    >
                                        <a
                                            :href="thirdLevelCategory.url"
                                            class="block px-6 py-2 text-sm transition-colors duration-200 hover:bg-gray-100"
                                        >
                                            @{{ thirdLevelCategory.name }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </x-slot>
            </x-shop::drawer>
        </div>
    </script>

    {{-- =====================================================================
         v-custom-categories-dropdown — single dropdown entry for all categories,
         used when the "categories dropdown" setting is ON.
         ===================================================================== --}}
    <script
        type="text/x-template"
        id="v-custom-categories-dropdown-template"
    >
        <div
            class="flex items-center gap-5"
            v-if="isLoading"
        >
            <span class="w-20 h-6 rounded shimmer" role="presentation"></span>
        </div>

        <div
            class="group relative flex h-[77px] items-center border-b-4 border-transparent hover:border-b-4 hover:border-navyBlue"
            v-else-if="categories.length"
        >
            <span class="inline-block px-3 uppercase whitespace-nowrap cursor-pointer">
                @{{ categoriesLabel }}
                <span class="text-sm icon-sort-down ml-1"></span>
            </span>

            <div class="pointer-events-none absolute top-[78px] z-[1] max-h-[580px] w-max max-w-[1260px] translate-y-1 overflow-auto overflow-x-auto border border-b-0 border-l-0 border-r-0 border-t border-[#F3F3F3] bg-white p-9 opacity-0 shadow-[0_6px_6px_1px_rgba(0,0,0,.3)] transition duration-300 ease-out group-hover:pointer-events-auto group-hover:translate-y-0 group-hover:opacity-100 group-hover:duration-200 group-hover:ease-in ltr:-left-9 rtl:-right-9">
                <div class="flex justify-between gap-x-[70px]">
                    <div
                        class="grid w-full min-w-max max-w-[150px] flex-auto grid-cols-[1fr] content-start gap-5"
                        v-for="pairCategoryChildren in pairCategoryChildren(categories)"
                    >
                        <template v-for="category in pairCategoryChildren">
                            <p class="font-medium text-navyBlue">
                                <a :href="category.url">@{{ category.name }}</a>
                            </p>

                            <ul
                                class="grid grid-cols-[1fr] gap-3"
                                v-if="category.children && category.children.length"
                            >
                                <li
                                    class="text-sm font-medium text-zinc-500"
                                    v-for="child in category.children"
                                >
                                    <a :href="child.url">@{{ child.name }}</a>
                                </li>
                            </ul>
                        </template>
                    </div>
                </div>
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-search-bar', {
            template: '#v-search-bar-template',

            props: {
                searchRoute:  { type: String, required: true },
                currentQuery: { type: String, default: '' },
                minLength:    { type: String, default: '1' },
                maxLength:    { type: String, default: '255' },
                placeholder:  { type: String, default: 'Search...' },
                searchLabel:  { type: String, default: 'Search' },
                submitLabel:  { type: String, default: 'Submit' },
            },

            data() {
                return {
                    isOpen: false,
                    inputId: 'v-search-bar-input',
                    panelTop: 78,
                };
            },

            mounted() {
                this._onScroll = () => {
                    if (this.isOpen) this.updatePanelTop();
                };

                window.addEventListener('scroll', this._onScroll, { passive: true });
            },

            beforeUnmount() {
                window.removeEventListener('scroll', this._onScroll);
            },

            methods: {
                updatePanelTop() {
                    const header = document.querySelector('header');
                    this.panelTop = header ? header.getBoundingClientRect().bottom : 78;
                },

                openSearch() {
                    this.updatePanelTop();
                    this.isOpen = true;
                    this.$nextTick(() => this.$refs.searchInput?.focus());
                },

                closeSearch() {
                    this.isOpen = false;
                },
            },
        });

        app.component('v-desktop-category', {
            template: '#v-desktop-category-template',

            data() {
                return {
                    isLoading: true,
                    categories: [],
                    isDrawerActive: false,
                    currentViewLevel: 'main',
                    currentSecondLevelCategory: null,
                    currentParentCategory: null,
                };
            },

            mounted() {
                this.initCategories();
            },

            methods: {
                initCategories() {
                    try {
                        const stored = localStorage.getItem('categories');

                        if (stored) {
                            this.categories = JSON.parse(stored);
                            this.isLoading = false;

                            return;
                        }
                    } catch (e) {}

                    this.getCategories();
                },

                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.isLoading = false;
                            this.categories = response.data.data;
                            localStorage.setItem('categories', JSON.stringify(this.categories));
                        })
                        .catch(error => console.log(error));
                },

                pairCategoryChildren(category) {
                    if (! category.children) return [];

                    return category.children.reduce((result, value, index, array) => {
                        if (index % 2 === 0) result.push(array.slice(index, index + 2));
                        return result;
                    }, []);
                },

                toggleCategoryDrawer() {
                    this.isDrawerActive = ! this.isDrawerActive;

                    if (this.isDrawerActive) {
                        this.currentViewLevel = 'main';
                    }
                },

                onDrawerToggle(event) {
                    this.isDrawerActive = event.isActive;
                },

                onDrawerClose() {
                    this.isDrawerActive = false;
                },

                showThirdLevel(secondLevelCategory, parentCategory, event) {
                    if (secondLevelCategory.children && secondLevelCategory.children.length) {
                        this.currentSecondLevelCategory = secondLevelCategory;
                        this.currentParentCategory = parentCategory;
                        this.currentViewLevel = 'third';

                        if (event) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                    }
                },

                goBackToMainView() {
                    this.currentViewLevel = 'main';
                },
            },
        });

        app.component('v-custom-categories-dropdown', {
            template: '#v-custom-categories-dropdown-template',

            props: {
                categoriesLabel: {
                    type: String,
                    default: 'Categories',
                },
            },

            data() {
                return {
                    isLoading: true,
                    categories: [],
                };
            },

            mounted() {
                this.initCategories();
            },

            methods: {
                initCategories() {
                    try {
                        const stored = localStorage.getItem('categories');

                        if (stored) {
                            this.categories = JSON.parse(stored);
                            this.isLoading = false;

                            return;
                        }
                    } catch (e) {}

                    this.getCategories();
                },

                getCategories() {
                    this.$axios.get("{{ route('shop.api.categories.tree') }}")
                        .then(response => {
                            this.isLoading = false;
                            this.categories = response.data.data;
                            localStorage.setItem('categories', JSON.stringify(this.categories));
                        })
                        .catch(error => console.log(error));
                },

                pairCategoryChildren(list) {
                    return list.reduce((result, value, index, array) => {
                        if (index % 2 === 0) result.push(array.slice(index, index + 2));
                        return result;
                    }, []);
                },
            },
        });
    </script>
@endPushOnce
{!! view_render_event('bagisto.shop.components.layouts.header.desktop.bottom.after') !!}
