<x-shop::layouts.account>
    <!-- Page Title -->
    <x-slot:title>
        @lang('shop::app.customers.account.profile.index.title')
    </x-slot>

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        @section('breadcrumbs')
            <x-shop::breadcrumbs name="profile" />
        @endSection
    @endif

    <div class="max-md:hidden">
        <x-shop::layouts.account.navigation />
    </div>

    <div class="flex-auto mx-4 max-md:mx-6 max-sm:mx-4">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <!-- Back Button -->
                <a
                    class="grid md:hidden"
                    href="{{ route('shop.customers.account.index') }}"
                >
                    <span class="text-2xl icon-arrow-left rtl:icon-arrow-right"></span>
                </a>

                <h2 class="text-2xl font-medium max-md:text-xl max-sm:text-base ltr:ml-2.5 md:ltr:ml-0 rtl:mr-2.5 md:rtl:mr-0">
                    @lang('shop::app.customers.account.profile.index.title')
                </h2>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit_button.before') !!}

            <a
                href="{{ route('shop.customers.account.profile.edit') }}"
                class="secondary-button border-zinc-200 px-5 py-3 font-normal max-md:rounded-lg max-md:py-2 max-sm:py-1.5 max-sm:text-sm"
            >
                @lang('shop::app.customers.account.profile.index.edit')
            </a>

            {!! view_render_event('bagisto.shop.customers.account.profile.edit_button.after') !!}
        </div>

        <!-- Profile Information -->
        <div class="grid grid-cols-1 mt-8 gap-y-6 max-md:mt-5 max-sm:gap-y-4">
            {!! view_render_event('bagisto.shop.customers.account.profile.first_name.before') !!}

            <div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
                <p class="text-sm font-medium">
                    @lang('shop::app.customers.account.profile.index.first-name')
                </p>

                <p class="text-sm font-medium text-zinc-500" v-pre>
                    {{ $customer->first_name }}
                </p>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.first_name.after') !!}

            {!! view_render_event('bagisto.shop.customers.account.profile.last_name.before') !!}

            <div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
                <p class="text-sm font-medium">
                    @lang('shop::app.customers.account.profile.index.last-name')
                </p>

                <p class="text-sm font-medium text-zinc-500" v-pre>
                    {{ $customer->last_name }}
                </p>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.last_name.after') !!}

            {!! view_render_event('bagisto.shop.customers.account.profile.gender.before') !!}

            <div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
                <p class="text-sm font-medium">
                    @lang('shop::app.customers.account.profile.index.gender')
                </p>

                <p
                    class="text-sm font-medium text-zinc-500"
                    v-pre
                >
                    {{ $customer->gender ?? '-'}}
                </p>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.gender.after') !!}

            {!! view_render_event('bagisto.shop.customers.account.profile.date_of_birth.before') !!}

            <div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
                <p class="text-sm font-medium">
                    @lang('shop::app.customers.account.profile.index.dob')
                </p>

                <p
                    class="text-sm font-medium text-zinc-500"
                    v-pre
                >
                    {{ $customer->date_of_birth ?? '-' }}
                </p>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.date_of_birth.after') !!}

            {!! view_render_event('bagisto.shop.customers.account.profile.email.before') !!}

            <div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
                <p class="text-sm font-medium">
                    @lang('shop::app.customers.account.profile.index.email')
                </p>

                <p
                    class="text-sm font-medium no-underline text-zinc-500"
                    v-pre
                >
                    {{ $customer->email }}
                </p>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.email.after') !!}

            <!-- Verification Status -->
            <div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
                <p class="text-sm font-medium">
                    Verification Status
                </p>

                <div class="flex items-center">
                    @if($customer->verification_status === 'approved')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Approved
                        </span>
                    @elseif($customer->verification_status === 'pending')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-4 h-4 mr-1 animate-spin" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                            </svg>
                            Pending Review
                        </span>
                    @elseif($customer->verification_status === 'rejected')
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Rejected
                        </span>
                    @else
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                            </svg>
                            Incomplete
                        </span>
                    @endif
                </div>
            </div>

            {!! view_render_event('bagisto.shop.customers.account.profile.delete.before') !!}

            <!-- Profile Delete modal -->
            <x-shop::form action="{{ route('shop.customers.account.profile.destroy') }}">
                <x-shop::modal>
                    <x-slot:toggle>
                        <div class="py-3 primary-button rounded-2xl px-11 max-md:hidden max-md:rounded-lg">
                            @lang('shop::app.customers.account.profile.index.delete-profile')
                        </div>

                        <div class="rounded-2xl py-3 text-center font-medium text-red-500 max-md:w-full max-md:max-w-full max-md:py-1.5 md:hidden">
                            @lang('shop::app.customers.account.profile.index.delete-profile')
                        </div>
                    </x-slot>

                    <x-slot:header>
                        <h2 class="text-2xl font-medium max-md:text-base">
                            @lang('shop::app.customers.account.profile.index.enter-password')
                        </h2>
                    </x-slot>

                    <x-slot:content>
                        <x-shop::form.control-group class="!mb-0">
                            <x-shop::form.control-group.control
                                type="password"
                                name="password"
                                class="px-6 py-4"
                                rules="required"
                                placeholder="Enter your password"
                            />

                            <x-shop::form.control-group.error
                                class="text-left"
                                control-name="password"
                            />
                        </x-shop::form.control-group>
                    </x-slot>

                    <!-- Modal Footer -->
                    <x-slot:footer>
                        <button
                            type="submit"
                            class="flex py-3 primary-button rounded-2xl px-11 max-md:rounded-lg max-md:px-6 max-md:text-sm"
                        >
                            @lang('shop::app.customers.account.profile.index.delete')
                        </button>
                    </x-slot>
                </x-shop::modal>
            </x-shop::form>

            {!! view_render_event('bagisto.shop.customers.account.profile.delete.after') !!}

        </div>
    </div>
</x-shop::layouts.account>