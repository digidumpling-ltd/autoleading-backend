@push('meta')
    <meta name="description" content="@lang('shop::app.customers.signup-form.page-title')">
    <meta name="keywords"    content="@lang('shop::app.customers.signup-form.page-title')">
@endpush

<x-auto-leading-theme::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.signup-form.page-title')
    </x-slot>

    <div class="al-auth-page">
        <div class="al-auth-card al-auth-card--wide">

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

            <x-shop::form
                action="{{ route('shop.customers.register.store') }}"
                enctype="multipart/form-data"
            >
                {!! view_render_event('bagisto.shop.customers.signup_form_controls.before') !!}

                {{-- First Name + Last Name --}}
                <div class="al-auth-form-grid">
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
                </div>

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

                {{-- Password + Confirm Password --}}
                <div class="al-auth-form-grid">
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
                </div>

                {{-- Captcha --}}
                @if (core()->getConfigData('customer.captcha.credentials.status'))
                    <x-shop::form.control-group class="mt-4">
                        {!! \Webkul\Customer\Facades\Captcha::render() !!}
                        <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                    </x-shop::form.control-group>
                @endif

                {{-- Newsletter --}}
                @if (core()->getConfigData('customer.settings.create_new_account_options.news_letter'))
                    <div class="al-auth-checkbox-row">
                        <input type="checkbox" name="is_subscribed" id="is-subscribed" class="al-auth-native-checkbox">
                        <label for="is-subscribed" class="al-auth-check-label">
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
                    <div class="al-auth-checkbox-row">
                        <x-shop::form.control-group.control
                            type="checkbox"
                            name="agreement"
                            id="agreement"
                            value="0"
                            rules="required"
                            for="agreement"
                        />
                        <label class="al-auth-check-label" for="agreement" v-pre>
                            {{ core()->getConfigData('general.gdpr.agreement.agreement_label') }}
                        </label>

                        @if (core()->getConfigData('general.gdpr.agreement.agreement_content'))
                            <span
                                class="al-auth-gdpr-link"
                                @click="$refs.termsModal.open()"
                            >
                                @lang('shop::app.customers.signup-form.click-here')
                            </span>
                        @endif
                    </div>

                    <x-shop::form.control-group.error control-name="agreement" />
                @endif

                {{-- Personal Data Collection Statement --}}
                <v-field
                    name="agree_pdcs"
                    rules="required"
                    type="checkbox"
                    :value="true"
                    v-slot="{ field, errors }"
                    label="{{ __('auto-leading-theme::app.auth_page.pdcs_title') }}"
                >
                    <div class="al-auth-checkbox-row">
                        <input
                            type="checkbox"
                            id="agree-pdcs"
                            name="agree_pdcs"
                            v-bind="field"
                            :value="true"
                            class="al-auth-native-checkbox"
                            :class="{ 'al-auth-checkbox--error': errors.length }"
                        >
                        <label class="al-auth-check-label" for="agree-pdcs">
                            {!! __('auto-leading-theme::app.auth_page.pdcs_agree', [
                                'link' => '<a href="#" target="_blank" class="al-auth-tnc-link">' . __('auto-leading-theme::app.auth_page.pdcs_title') . '</a>',
                            ]) !!}
                        </label>
                    </div>
                </v-field>
                <v-error-message name="agree_pdcs" v-slot="{ message }">
                    <p class="al-auth-field-error" v-text="message"></p>
                </v-error-message>

                {{-- Membership Terms & Conditions --}}
                <v-field
                    name="agree_membership_tnc"
                    rules="required"
                    type="checkbox"
                    :value="true"
                    v-slot="{ field, errors }"
                    label="{{ __('auto-leading-theme::app.auth_page.membership_tnc_title') }}"
                >
                    <div class="al-auth-checkbox-row">
                        <input
                            type="checkbox"
                            id="agree-membership-tnc"
                            name="agree_membership_tnc"
                            v-bind="field"
                            :value="true"
                            class="al-auth-native-checkbox"
                            :class="{ 'al-auth-checkbox--error': errors.length }"
                        >
                        <label class="al-auth-check-label" for="agree-membership-tnc">
                            {!! __('auto-leading-theme::app.auth_page.membership_tnc_agree', [
                                'link' => '<a href="#" target="_blank" class="al-auth-tnc-link">' . __('auto-leading-theme::app.auth_page.membership_tnc_title') . '</a>',
                            ]) !!}
                        </label>
                    </div>
                </v-field>
                <v-error-message name="agree_membership_tnc" v-slot="{ message }">
                    <p class="al-auth-field-error" v-text="message"></p>
                </v-error-message>

                <button type="submit" class="al-auth-submit">
                    @lang('shop::app.customers.signup-form.button-title')
                </button>

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

    {{-- GDPR modal --}}
    @if (
        core()->getConfigData('general.gdpr.settings.enabled')
        && core()->getConfigData('general.gdpr.agreement.enabled')
        && core()->getConfigData('general.gdpr.agreement.agreement_content')
    )
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

</x-auto-leading-theme::layouts.auth>

@push('scripts')
    {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
@endpush
