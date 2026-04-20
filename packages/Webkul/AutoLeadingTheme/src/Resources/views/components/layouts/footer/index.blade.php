<footer class="al-footer">
    <div class="al-shell py-12">
        <div class="al-footer-grid">
            <!-- Brand / About -->
            <div>
                <h2 class="al-footer-heading italic tracking-tighter text-white text-2xl">
                    AUTO<span class="text-[var(--al-orange)]">LEADING</span>
                </h2>
                <p class="mt-3 text-sm leading-relaxed">
                    {{ core()->getConfigData('general.general.store-information.name') ?? config('app.name') }}
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h3 class="al-footer-heading">{{ __('auto-leading-theme::app.footer.quick_links') }}</h3>
                <ul class="al-footer-links">
                    <li><a href="{{ route('shop.home.index') }}">{{ __('auto-leading-theme::app.nav.home') }}</a></li>
                    <li><a href="{{ route('shop.search.index') }}">{{ __('auto-leading-theme::app.footer.car_models') }}</a></li>
                    <li><a href="#">{{ __('auto-leading-theme::app.nav.services') }}</a></li>
                    <li><a href="#">{{ __('auto-leading-theme::app.nav.about') }}</a></li>
                    <li><a href="#">{{ __('auto-leading-theme::app.nav.contact') }}</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h3 class="al-footer-heading">{{ __('auto-leading-theme::app.footer.contact') }}</h3>
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
            </div>
        </div>
    </div>

    <div class="al-footer-bar">
        <div class="al-shell flex flex-wrap justify-between items-center gap-2 text-sm">
            <p>&copy; {{ date('Y') }} AutoLeading. {{ __('auto-leading-theme::app.footer.all_rights_reserved') }}</p>
        </div>
    </div>
</footer>
