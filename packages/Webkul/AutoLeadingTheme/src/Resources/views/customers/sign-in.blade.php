@push('meta')
    <meta name="description" content="@lang('shop::app.customers.login-form.page-title')">
    <meta name="keywords"    content="@lang('shop::app.customers.login-form.page-title')">
@endpush

<x-auto-leading-theme::layouts.auth>
    <x-slot:title>
        @lang('shop::app.customers.login-form.page-title')
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

            <h1 class="al-auth-heading">{{ __('auto-leading-theme::app.auth_page.sign_in_title') }}</h1>
            <p class="al-auth-subheading">{{ __('auto-leading-theme::app.auth_page.sign_in_sub') }}</p>

            {{-- Flash messages --}}
            @if ($message = session('success'))
                <x-auto-leading-theme::alert type="success" :message="$message" />
            @endif
            @if ($message = session('error'))
                <x-auto-leading-theme::alert type="error" :message="$message" />
            @endif

            {!! view_render_event('bagisto.shop.customers.login.before') !!}

            <x-shop::form :action="route('shop.customer.session.create')">

                {!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                {{-- Email --}}
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.login-form.email')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="email"
                        name="email"
                        rules="required|email"
                        value=""
                        :label="trans('shop::app.customers.login-form.email')"
                        placeholder="email@example.com"
                        :aria-label="trans('shop::app.customers.login-form.email')"
                        aria-required="true"
                    />

                    <x-shop::form.control-group.error control-name="email" />
                </x-shop::form.control-group>

                {{-- Password --}}
                <x-shop::form.control-group>
                    <x-shop::form.control-group.label class="required">
                        @lang('shop::app.customers.login-form.password')
                    </x-shop::form.control-group.label>

                    <x-shop::form.control-group.control
                        type="password"
                        id="password"
                        name="password"
                        rules="required|min:6"
                        value=""
                        :label="trans('shop::app.customers.login-form.password')"
                        :placeholder="trans('shop::app.customers.login-form.password')"
                        :aria-label="trans('shop::app.customers.login-form.password')"
                        aria-required="true"
                    />

                    <x-shop::form.control-group.error control-name="password" />
                </x-shop::form.control-group>

                {{-- Show password / Forgot password --}}
                <div class="al-auth-pw-row">
                    <label class="al-auth-check-label" for="show-password">
                        <input
                            type="checkbox"
                            id="show-password"
                            onchange="alTogglePassword()"
                        >
                        @lang('shop::app.customers.login-form.show-password')
                    </label>

                    <a
                        href="{{ route('shop.customers.forgot_password.create') }}"
                        class="al-auth-forgot"
                    >
                        @lang('shop::app.customers.login-form.forgot-pass')
                    </a>
                </div>

                {{-- Captcha --}}
                @if (core()->getConfigData('customer.captcha.credentials.status'))
                    <x-shop::form.control-group>
                        {!! \Webkul\Customer\Facades\Captcha::render() !!}
                        <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                    </x-shop::form.control-group>
                @endif

                <button type="submit" class="al-auth-submit">
                    @lang('shop::app.customers.login-form.button-title')
                </button>

                {!! view_render_event('bagisto.shop.customers.login_form_controls.after') !!}

            </x-shop::form>

            {!! view_render_event('bagisto.shop.customers.login.after') !!}

            <p class="al-auth-switch">
                @lang('shop::app.customers.login-form.new-customer')
                <a href="{{ route('shop.customers.register.index') }}">
                    @lang('shop::app.customers.login-form.create-your-account')
                </a>
            </p>

            <p class="al-auth-copyright">
                @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
            </p>

        </div>
    </div>

</x-auto-leading-theme::layouts.auth>

@push('scripts')
    {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}

    <script>
        function alTogglePassword() {
            const field = document.getElementById('password');
            field.type = field.type === 'password' ? 'text' : 'password';
        }
    </script>
@endpush
