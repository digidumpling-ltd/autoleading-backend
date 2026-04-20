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

            <x-shop::form action="{{ route('shop.customer.session.create') }}">

                {!! view_render_event('bagisto.shop.customers.login_form_controls.before') !!}

                <div class="space-y-6">
                    {{-- Email --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium mb-1 block required">
                            @lang('shop::app.customers.login-form.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            name="email"
                            class="w-full bg-[#111] border border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                            :value="old('email')"
                            rules="required|email"
                            :label="trans('shop::app.customers.login-form.email')"
                            placeholder="email@example.com"
                            :aria-label="trans('shop::app.customers.login-form.email')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    {{-- Password --}}
                    <x-shop::form.control-group>
                        <div class="flex justify-between items-center mb-1">
                            <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium block required">
                                @lang('shop::app.customers.login-form.password')
                            </x-shop::form.control-group.label>

                            <a 
                                href="{{ route('shop.customers.forgot_password.create') }}" 
                                class="text-xs text-[#F0A500] hover:text-[#C88600] transition-colors"
                            >
                                @lang('shop::app.customers.login-form.forgot-pass')
                            </a>
                        </div>

                        <x-shop::form.control-group.control
                            type="password"
                            id="password"
                            name="password"
                            class="w-full bg-[#111] border border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                            rules="required|min:6"
                            :label="trans('shop::app.customers.login-form.password')"
                            :placeholder="trans('shop::app.customers.login-form.password')"
                            :aria-label="trans('shop::app.customers.login-form.password')"
                            aria-required="true"
                        />

                        <x-shop::form.control-group.error control-name="password" />
                    </x-shop::form.control-group>

                    {{-- Captcha --}}
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group>
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}
                            <x-shop::form.control-group.error control-name="g-recaptcha-response" />
                        </x-shop::form.control-group>
                    @endif

                    <button 
                        type="submit" 
                        class="w-full bg-[#F0A500] hover:bg-[#C88600] text-black font-bold py-4 rounded-xl transition-all transform hover:scale-[1.01] active:scale-[0.99] uppercase tracking-wider text-sm shadow-lg shadow-orange-500/20"
                    >
                        @lang('shop::app.customers.login-form.button-title')
                    </button>
                </div>

                {!! view_render_event('bagisto.shop.customers.login_form_controls.after') !!}

            </x-shop::form>

            {!! view_render_event('bagisto.shop.customers.login.after') !!}

            <div class="mt-8 text-center pt-6 border-t border-white/5">
                <p class="text-gray-500 text-sm">
                    @lang('shop::app.customers.login-form.new-customer')
                    <a 
                        href="{{ route('shop.customers.register.index') }}" 
                        class="text-[#F0A500] font-semibold hover:underline ml-1"
                    >
                        @lang('shop::app.customers.login-form.create-your-account')
                    </a>
                </p>
            </div>

            <p class="al-auth-copyright">
                @lang('shop::app.customers.login-form.footer', ['current_year' => date('Y')])
            </p>

        </div>
    </div>

</x-auto-leading-theme::layouts.auth>

@push('scripts')
    {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
@endpush
