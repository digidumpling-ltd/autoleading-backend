@push('meta')
    <meta name="description" content="@lang('shop::app.customers.signup-form.page-title')">
    <meta name="keywords"    content="@lang('shop::app.customers.signup-form.page-title')">
@endpush

<x-auto-leading-theme::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    <div class="al-auth-page">
        <div class="al-auth-card">

            {{-- Logo --}}
            <a href="{{ route('shop.home.index') }}" class="al-auth-logo" aria-label="{{ config('app.name') }}">
                <img
                    src="{{ core()->getCurrentChannel()->logo_url ?? bagisto_asset('images/logo.svg') }}"
                    alt="{{ config('app.name') }}"
                >
            </a>

            <h1 class="al-auth-heading">{{ __('auto-leading-theme::app.auth_page.sign_up_title') }}</h1>
            <p class="al-auth-subheading">{{ __('auto-leading-theme::app.auth_page.sign_up_sub') }}</p>

            {!! view_render_event('bagisto.shop.customers.sign-up.before') !!}

            <x-shop::form action="{{ route('shop.customers.register.store') }}">

                {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                <div class="space-y-6">

                    {{-- First Name --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.customers.signup-form.first-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="first_name"
                            rules="required"
                            :value="old('first_name')"
                            :label="trans('shop::app.customers.signup-form.first-name')"
                            :placeholder="trans('shop::app.customers.signup-form.first-name')"
                            :aria-label="trans('shop::app.customers.signup-form.first-name')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="first_name" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.first_name.after') !!}

                    {{-- Last Name --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.customers.signup-form.last-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="last_name"
                            rules="required"
                            :value="old('last_name')"
                            :label="trans('shop::app.customers.signup-form.last-name')"
                            :placeholder="trans('shop::app.customers.signup-form.last-name')"
                            :aria-label="trans('shop::app.customers.signup-form.last-name')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="last_name" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.last_name.after') !!}

                    {{-- Email --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.customers.signup-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            name="email"
                            rules="required|email"
                            :value="old('email')"
                            :label="trans('shop::app.customers.signup-form.email')"
                            placeholder="email@example.com"
                            :aria-label="trans('shop::app.customers.signup-form.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.email.after') !!}

                    {{-- Password --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('shop::app.customers.signup-form.password')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            name="password"
                            rules="required|min:6"
                            :value="old('password')"
                            :label="trans('shop::app.customers.signup-form.password')"
                            :placeholder="trans('shop::app.customers.signup-form.password')"
                            ref="password"
                            :aria-label="trans('shop::app.customers.signup-form.password')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.password.after') !!}

                    {{-- Confirm Password --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label>
                            @lang('shop::app.customers.signup-form.confirm-pass')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="password"
                            name="password_confirmation"
                            rules="confirmed:@password"
                            value=""
                            :label="trans('shop::app.customers.signup-form.password')"
                            :placeholder="trans('shop::app.customers.signup-form.confirm-pass')"
                            :aria-label="trans('shop::app.customers.signup-form.confirm-pass')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password_confirmation" />
                    </x-shop::form.control-group>

                    {!! view_render_event('bagisto.shop.customers.signup_form.password_confirmation.after') !!}

                    {{-- Captcha --}}
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group>
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}
                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    {{-- Newsletter --}}
                    @if (core()->getConfigData('customer.settings.create_new_account_options.news_letter'))
                        <div class="flex select-none items-center gap-1.5">
                            <input
                                type="checkbox"
                                name="is_subscribed"
                                id="is-subscribed"
                                class="peer hidden"
                            />

                            <label
                                class="icon-uncheck peer-checked:icon-check-box cursor-pointer text-2xl text-navyBlue peer-checked:text-navyBlue"
                                for="is-subscribed"
                            ></label>

                            <label
                                class="cursor-pointer select-none text-base text-zinc-500 max-sm:text-sm ltr:pl-0 rtl:pr-0"
                                for="is-subscribed"
                            >
                                @lang('shop::app.customers.signup-form.subscribe-to-newsletter')
                            </label>
                        </div>
                    @endif

                    {!! view_render_event('bagisto.shop.customers.signup_form.newsletter_subscription.after') !!}

                    {{-- GDPR --}}
                    @if (
                        core()->getConfigData('general.gdpr.settings.enabled')
                        && core()->getConfigData('general.gdpr.agreement.enabled')
                    )
                        <div class="flex select-none items-center gap-1.5">
                            <x-shop::form.control-group.control
                                type="checkbox"
                                name="agreement"
                                id="agreement"
                                value="0"
                                rules="required"
                                for="agreement"
                            />

                            <label
                                class="cursor-pointer select-none text-base text-zinc-500 max-sm:text-sm"
                                for="agreement"
                                v-pre
                            >
                                {{ core()->getConfigData('general.gdpr.agreement.agreement_label') }}
                            </label>

                            @if (core()->getConfigData('general.gdpr.agreement.agreement_content'))
                                <span
                                    class="cursor-pointer text-base text-navyBlue max-sm:text-sm"
                                    @click="$refs.termsModal.open()"
                                >
                                    @lang('shop::app.customers.signup-form.click-here')
                                </span>
                            @endif
                        </div>

                        <x-shop::form.control-group.error control-name="agreement" />
                    @endif

                    <button type="submit" class="al-auth-submit">
                        @lang('shop::app.customers.signup-form.button-title')
                    </button>

                </div>

                {!! view_render_event('bagisto.shop.customers.signup_form_controls.after') !!}

            </x-shop::form>

            {!! view_render_event('bagisto.shop.customers.sign-up.after') !!}

            <p class="al-auth-switch">
                @lang('shop::app.customers.signup-form.account-exists')
                <a href="{{ route('shop.customer.session.index') }}">
                    @lang('shop::app.customers.signup-form.sign-in-button')
                </a>
            </p>

            <p class="al-auth-copyright">
                @lang('shop::app.customers.signup-form.footer', ['current_year' => date('Y')])
            </p>

        </div>
    </div>

</x-auto-leading-theme::layouts.auth>

@push('scripts')
    {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
@endpush

{{-- Terms & Conditions Modal --}}
@if (core()->getConfigData('general.gdpr.settings.enabled') && core()->getConfigData('general.gdpr.agreement.agreement_content'))
    <x-shop::modal ref="termsModal">
        <x-slot:toggle></x-slot>

        <x-slot:header class="!p-5">
            <p>@lang('shop::app.customers.signup-form.terms-conditions')</p>
        </x-slot>

        <x-slot:content class="!p-5">
            <div class="max-h-[500px] overflow-auto">
                {!! core()->getConfigData('general.gdpr.agreement.agreement_content') !!}
            </div>
        </x-slot>
    </x-shop::modal>
@endif
