@php
    $quickLinks = [
        ['label' => __('auto-leading-theme::app.nav.home'),    'url' => route('shop.home.index')],
        ['label' => __('auto-leading-theme::app.footer.car_models'), 'url' => route('shop.search.index')],
        ['label' => __('auto-leading-theme::app.nav.services'), 'url' => '#'],
        ['label' => __('auto-leading-theme::app.nav.about'),   'url' => '#'],
        ['label' => __('auto-leading-theme::app.nav.contact'), 'url' => '#'],
    ];

    $typeAttrOptions = app(\Webkul\Attribute\Repositories\AttributeRepository::class)
        ->findOneByField('code', 'type')?->options ?? collect();

    $carModelLinks = $typeAttrOptions->map(fn ($opt) => [
        'label' => $opt->admin_name,
        'url'   => route('shop.search.index', ['type' => $opt->id]),
    ])->toArray();

    if (empty($carModelLinks)) {
        $carModelLinks = [
            ['label' => __('auto-leading-theme::app.types.sedan'),      'url' => route('shop.search.index')],
            ['label' => __('auto-leading-theme::app.types.sports'),     'url' => route('shop.search.index')],
            ['label' => __('auto-leading-theme::app.types.suv'),        'url' => route('shop.search.index')],
            ['label' => __('auto-leading-theme::app.types.convertible'),'url' => route('shop.search.index')],
        ];
    }
@endphp

<footer class="al-footer">
    <div class="al-shell py-12">
        <div class="al-footer-grid">

            {{-- Quick Links --}}
            <x-auto-leading-theme::footer-column
                :heading="__('auto-leading-theme::app.footer.quick_links')"
                :links="$quickLinks"
            />

            {{-- Car Models --}}
            <x-auto-leading-theme::footer-column
                :heading="__('auto-leading-theme::app.footer.car_models')"
                :links="$carModelLinks"
            />

            {{-- Contact --}}
            <x-auto-leading-theme::footer-column
                :heading="__('auto-leading-theme::app.footer.contact')"
                :links="[]"
            >
                <address class="al-footer-contact">
                    <p>{{ __('auto-leading-theme::app.footer.address') }}</p>
                    <p class="mt-2">
                        <a href="tel:{{ __('auto-leading-theme::app.footer.phone') }}">
                            {{ __('auto-leading-theme::app.footer.phone') }}
                        </a>
                    </p>
                    <p class="mt-1">
                        <a href="mailto:{{ __('auto-leading-theme::app.footer.email') }}">
                            {{ __('auto-leading-theme::app.footer.email') }}
                        </a>
                    </p>
                </address>
            </x-auto-leading-theme::footer-column>

        </div>
    </div>

    <div class="al-footer-bar">
        <div class="al-shell flex flex-wrap justify-between items-center gap-2 text-sm">
            <p>&copy; {{ date('Y') }} AutoLeading. {{ __('auto-leading-theme::app.footer.all_rights_reserved') }}</p>
        </div>
    </div>
</footer>
