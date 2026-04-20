<x-auto-leading-theme::layouts>
    @section('page_title')
        {{ __('shop::app.customers.signup-form.page-title') }}
    @endsection

    <div class="al-site min-h-screen pt-24 pb-12 bg-[#0d0d0d] flex items-center justify-center font-sans">
        <div class="al-shell max-w-lg w-full px-4">
            <div class="bg-[#1a1a1a] border border-white/5 rounded-2xl p-8 shadow-2xl">
                <div class="mb-8 text-center">
                    <h1 class="text-3xl font-bold text-white mb-2 italic tracking-tight">
                        AUTO<span class="text-[#F0A500]">LEADING</span>
                    </h1>
                    <p class="text-gray-400 text-sm uppercase tracking-widest">{{ __('shop::app.customers.signup-form.title') }}</p>
                </div>

                <form 
                    method="POST" 
                    action="{{ route('shop.customer.register.create') }}" 
                    @submit.prevent="$root.onSubmit"
                >
                    @csrf

                    {!! view_render_event('bagisto.shop.customers.registration_form_controls.before') !!}

                    <div class="space-y-5">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium mb-1 block required">
                                    {{ __('shop::app.customers.signup-form.first-name') }}
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="text"
                                    name="first_name"
                                    class="w-full bg-[#111] border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                                    :value="old('first_name')"
                                    rules="required"
                                    :label="__('shop::app.customers.signup-form.first-name')"
                                    :placeholder="__('shop::app.customers.signup-form.first-name')"
                                >
                                </x-shop::form.control-group.control>

                                <x-shop::form.control-group.error control-name="first_name"></x-shop::form.control-group.error>
                            </x-shop::form.control-group>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium mb-1 block required">
                                    {{ __('shop::app.customers.signup-form.last-name') }}
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="text"
                                    name="last_name"
                                    class="w-full bg-[#111] border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                                    :value="old('last_name')"
                                    rules="required"
                                    :label="__('shop::app.customers.signup-form.last-name')"
                                    :placeholder="__('shop::app.customers.signup-form.last-name')"
                                >
                                </x-shop::form.control-group.control>

                                <x-shop::form.control-group.error control-name="last_name"></x-shop::form.control-group.error>
                            </x-shop::form.control-group>
                        </div>

                        <x-shop::form.control-group>
                            <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium mb-1 block required">
                                {{ __('shop::app.customers.signup-form.email') }}
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="email"
                                name="email"
                                class="w-full bg-[#111] border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                                :value="old('email')"
                                rules="required|email"
                                :label="__('shop::app.customers.signup-form.email')"
                                :placeholder="__('shop::app.customers.signup-form.email')"
                            >
                            </x-shop::form.control-group.control>

                            <x-shop::form.control-group.error control-name="email"></x-shop::form.control-group.error>
                        </x-shop::form.control-group>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium mb-1 block required">
                                    {{ __('shop::app.customers.signup-form.password') }}
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="password"
                                    name="password"
                                    class="w-full bg-[#111] border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                                    rules="required|min:6"
                                    :label="__('shop::app.customers.signup-form.password')"
                                    :placeholder="__('shop::app.customers.signup-form.password')"
                                    ref="password"
                                >
                                </x-shop::form.control-group.control>

                                <x-shop::form.control-group.error control-name="password"></x-shop::form.control-group.error>
                            </x-shop::form.control-group>

                            <x-shop::form.control-group>
                                <x-shop::form.control-group.label class="text-gray-300 text-sm font-medium mb-1 block required">
                                    {{ __('shop::app.customers.signup-form.confirm_pass') }}
                                </x-shop::form.control-group.label>

                                <x-shop::form.control-group.control
                                    type="password"
                                    name="password_confirmation"
                                    class="w-full bg-[#111] border-white/10 text-white placeholder-gray-600 focus:border-[#F0A500] focus:ring-[#F0A500] rounded-xl px-4 py-3"
                                    rules="confirmed:@password"
                                    :label="__('shop::app.customers.signup-form.confirm_pass')"
                                    :placeholder="__('shop::app.customers.signup-form.confirm_pass')"
                                >
                                </x-shop::form.control-group.control>

                                <x-shop::form.control-group.error control-name="password_confirmation"></x-shop::form.control-group.error>
                            </x-shop::form.control-group>
                        </div>

                        {!! view_render_event('bagisto.shop.customers.registration_form_controls.after') !!}

                        <button 
                            type="submit" 
                            class="w-full bg-[#F0A500] hover:bg-[#C88600] text-black font-bold py-4 rounded-xl transition-all transform hover:scale-[1.01] active:scale-[0.99] uppercase tracking-wider text-sm shadow-lg shadow-orange-500/20 mt-4"
                        >
                            <x-heroicon-o-user-plus class="w-5 h-5 inline-block mr-2" />
                            {{ __('shop::app.customers.signup-form.button-title') }}
                        </button>

                        <div class="text-center pt-4 border-t border-white/5 mt-6">
                            <p class="text-gray-500 text-sm">
                                {{ __('shop::app.customers.signup-form.account_exists') }}
                                <a 
                                    href="{{ route('shop.customer.session.index') }}" 
                                    class="text-[#F0A500] font-semibold hover:underline ml-1"
                                >
                                    {{ __('shop::app.customers.signup-form.title') }}
                                </a>
                            </p>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-auto-leading-theme::layouts>
