{!! view_render_event('bagisto.shop.layout.footer.before') !!}

@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

@php
    $channel = core()->getCurrentChannel();

    $footerCustomization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    $phone       = core()->getConfigData('general.design.footer.phone');
    $email       = core()->getConfigData('general.design.footer.email');
    $address     = core()->getConfigData('general.design.footer.address');
    $whatsapp1   = core()->getConfigData('general.design.footer.whatsapp_1');
    $whatsapp2   = core()->getConfigData('general.design.footer.whatsapp_2');
    $hoursRental = core()->getConfigData('general.design.footer.hours_rental');
    $hoursSales  = core()->getConfigData('general.design.footer.hours_sales');

    $hasHours   = $hoursRental || $hoursSales;
    $hasContact = $phone || $email || $address || $whatsapp1 || $whatsapp2;

    $quickLinks = [];

    if ((bool) core()->getConfigData('general.design.navbar.show_manual_menu')) {
        foreach (json_decode(core()->getConfigData('general.design.navbar.menu_items') ?? '[]', true) ?? [] as $item) {
            if (! empty($item['label']) && ! empty($item['url'])) {
                $quickLinks[] = ['title' => $item['label'], 'url' => $item['url'], 'sort_order' => (int) ($item['order'] ?? 0)];
            }
        }
    }

    if ($footerCustomization?->options) {
        foreach ($footerCustomization->options as $section) {
            foreach ($section as $link) {
                if (! empty($link['url']) && ! empty($link['title'])) {
                    $quickLinks[] = ['title' => $link['title'], 'url' => $link['url'], 'sort_order' => (int) ($link['sort_order'] ?? 0)];
                }
            }
        }
    }

    usort($quickLinks, fn ($a, $b) => $a['sort_order'] <=> $b['sort_order']);
@endphp

<footer class="mt-9 bg-lightOrange max-sm:mt-10">

    {{-- Desktop: 3 columns inside container --}}
    <div class="py-[60px] max-1060:hidden">
        <div class="container">
        <div class="grid grid-cols-3 gap-x-10" v-pre>

            {{-- Column 1: Quick Links --}}
            @if (! empty($quickLinks))
            <div>
                <p class="mb-5 font-bold text-zinc-600">
                    {{ __('custom-theme::app.footer.quick_links') }}
                </p>

                <ul class="grid gap-5 text-zinc-600">
                    @foreach ($quickLinks as $link)
                        <li>
                            <a href="{{ $link['url'] }}" class="hover:text-navyBlue">
                                {{ $link['title'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
            @endif

            {{-- Column 2: Business Hours --}}
            @if ($hasHours)
                <div>
                    <p class="mb-5 font-bold text-zinc-600">
                        {{ __('custom-theme::app.footer.business_hours') }}
                    </p>

                    <ul class="grid gap-5 text-zinc-600">
                        <li>{{ __('custom-theme::app.footer.hours_days') }}</li>

                        @if ($hoursRental)
                            <li>{{ __('custom-theme::app.footer.rental_hours_label') }} {{ $hoursRental }}</li>
                        @endif

                        @if ($hoursSales)
                            <li>{{ __('custom-theme::app.footer.sales_hours_label') }} {{ $hoursSales }}</li>
                        @endif
                    </ul>
                </div>
            @endif

            {{-- Column 3: Hotline --}}
            @if ($hasContact)
                <div>
                    <p class="mb-5 font-bold text-zinc-600">
                        {{ __('custom-theme::app.footer.hotline') }}
                    </p>

                    <address class="not-italic grid gap-5 text-zinc-600">
                        @if ($phone)
                            <span>
                                {{ __('custom-theme::app.footer.phone_label') }}
                                <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="hover:text-navyBlue">{{ $phone }}</a>
                            </span>
                        @endif

                        @if ($email)
                            <span>
                                {{ __('custom-theme::app.footer.email_label') }}
                                <a href="mailto:{{ $email }}" class="hover:text-navyBlue">{{ $email }}</a>
                            </span>
                        @endif

                        @if ($address)
                            <span>{{ __('custom-theme::app.footer.address_label') }} {{ $address }}</span>
                        @endif

                        @if ($whatsapp1 || $whatsapp2)
                            <span>
                                {{ __('custom-theme::app.footer.whatsapp_label') }}
                                @if ($whatsapp1)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp1) }}" class="block hover:text-navyBlue">
                                        +{{ preg_replace('/\D/', '', $whatsapp1) }}
                                    </a>
                                @endif
                                @if ($whatsapp2)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp2) }}" class="block hover:text-navyBlue">
                                        +{{ preg_replace('/\D/', '', $whatsapp2) }}
                                    </a>
                                @endif
                            </span>
                        @endif
                    </address>
                </div>
            @endif

        </div>
        </div>
    </div>

    {{-- Mobile: accordions --}}
    <div class="hidden max-1060:grid max-1060:gap-3 max-1060:p-8 max-sm:px-4 max-sm:py-5">

        @if (! empty($quickLinks))
            <x-shop::accordion
                :is-active="false"
                class="!w-full rounded-xl !border-2 !border-[#e9decc] max-sm:rounded-lg"
            >
                <x-slot:header class="rounded-t-lg bg-[#F1EADF] font-bold max-md:p-2.5 max-sm:px-3 max-sm:py-2">
                    {{ __('custom-theme::app.footer.quick_links') }}
                </x-slot>

                <x-slot:content class="!bg-transparent !p-4">
                    <ul class="grid gap-5 text-zinc-600">
                        @foreach ($quickLinks as $link)
                            <li>
                                <a href="{{ $link['url'] }}" class="hover:text-navyBlue max-sm:text-xs">
                                    {{ $link['title'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </x-slot>
            </x-shop::accordion>
        @endif

        @if ($hasHours)
            <x-shop::accordion
                :is-active="false"
                class="!w-full rounded-xl !border-2 !border-[#e9decc] max-sm:rounded-lg"
            >
                <x-slot:header class="rounded-t-lg bg-[#F1EADF] font-bold max-md:p-2.5 max-sm:px-3 max-sm:py-2">
                    {{ __('custom-theme::app.footer.business_hours') }}
                </x-slot>

                <x-slot:content class="!bg-transparent !p-4">
                    <ul class="grid gap-5 text-zinc-600">
                        <li>{{ __('custom-theme::app.footer.hours_days') }}</li>

                        @if ($hoursRental)
                            <li>{{ __('custom-theme::app.footer.rental_hours_label') }} {{ $hoursRental }}</li>
                        @endif

                        @if ($hoursSales)
                            <li>{{ __('custom-theme::app.footer.sales_hours_label') }} {{ $hoursSales }}</li>
                        @endif
                    </ul>
                </x-slot>
            </x-shop::accordion>
        @endif

        @if ($hasContact)
            <x-shop::accordion
                :is-active="false"
                class="!w-full rounded-xl !border-2 !border-[#e9decc] max-sm:rounded-lg"
            >
                <x-slot:header class="rounded-t-lg bg-[#F1EADF] font-bold max-md:p-2.5 max-sm:px-3 max-sm:py-2">
                    {{ __('custom-theme::app.footer.hotline') }}
                </x-slot>

                <x-slot:content class="!bg-transparent !p-4">
                    <address class="not-italic grid gap-5 text-zinc-600">
                        @if ($phone)
                            <span>
                                {{ __('custom-theme::app.footer.phone_label') }}
                                <a href="tel:{{ preg_replace('/\D/', '', $phone) }}" class="hover:text-navyBlue max-sm:text-xs">{{ $phone }}</a>
                            </span>
                        @endif

                        @if ($email)
                            <span>
                                {{ __('custom-theme::app.footer.email_label') }}
                                <a href="mailto:{{ $email }}" class="hover:text-navyBlue max-sm:text-xs">{{ $email }}</a>
                            </span>
                        @endif

                        @if ($address)
                            <span class="max-sm:text-xs">{{ __('custom-theme::app.footer.address_label') }} {{ $address }}</span>
                        @endif

                        @if ($whatsapp1 || $whatsapp2)
                            <span>
                                {{ __('custom-theme::app.footer.whatsapp_label') }}
                                @if ($whatsapp1)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp1) }}" class="block hover:text-navyBlue max-sm:text-xs">
                                        +{{ preg_replace('/\D/', '', $whatsapp1) }}
                                    </a>
                                @endif
                                @if ($whatsapp2)
                                    <a href="https://wa.me/{{ preg_replace('/\D/', '', $whatsapp2) }}" class="block hover:text-navyBlue max-sm:text-xs">
                                        +{{ preg_replace('/\D/', '', $whatsapp2) }}
                                    </a>
                                @endif
                            </span>
                        @endif
                    </address>
                </x-slot>
            </x-shop::accordion>
        @endif

    </div>

    {{-- Bottom bar --}}
    <div class="bg-[#F1EADF] px-[60px] py-3.5 max-sm:px-5">
        {!! view_render_event('bagisto.shop.layout.footer.footer_text.before') !!}

        <p class="text-center text-zinc-600">
            @if (core()->getConfigData('general.content.footer.copyright_content'))
                {!! core()->getConfigData('general.content.footer.copyright_content') !!}
            @else
                @lang('shop::app.components.layouts.footer.footer-text', ['current_year' => date('Y')])
            @endif
        </p>

        {!! view_render_event('bagisto.shop.layout.footer.footer_text.after') !!}
    </div>

</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
