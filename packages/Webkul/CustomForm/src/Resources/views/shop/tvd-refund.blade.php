<x-shop::layouts>
    <x-slot:title>
        @lang('customform::app.title')
    </x-slot>

    <div class="container mt-8 max-1180:px-5 max-md:mt-6 max-md:px-4">
        <div class="m-auto w-full max-w-[870px] rounded-xl border border-zinc-200 p-16 px-[90px] max-md:px-8 max-md:py-8 max-sm:border-none max-sm:p-0">
            <h1 class="font-dmserif text-4xl max-md:text-3xl max-sm:text-xl">
                @lang('customform::app.title')
            </h1>

            <div class="mt-14 rounded max-sm:mt-8">
                <x-shop::form :action="route('shop.tvd-form.submit')">
                    {{-- Field 1: Chinese Name --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.chinese-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="chinese_name"
                            rules="required"
                            :value="old('chinese_name')"
                            :label="trans('customform::app.fields.chinese-name')"
                            :placeholder="trans('customform::app.fields.chinese-name-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="chinese_name" />
                    </x-shop::form.control-group>

                    {{-- Field 2: English Name --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.english-name')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="english_name"
                            rules="required"
                            :value="old('english_name')"
                            :label="trans('customform::app.fields.english-name')"
                            :placeholder="trans('customform::app.fields.english-name-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="english_name" />
                    </x-shop::form.control-group>

                    {{-- Field 3: Last Rental Model --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.rental-model')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="rental_model"
                            rules="required"
                            :value="old('rental_model')"
                            :label="trans('customform::app.fields.rental-model')"
                            :placeholder="trans('customform::app.fields.rental-model-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="rental_model" />
                    </x-shop::form.control-group>

                    {{-- Field 4: Return Date --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.return-date')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="date"
                            name="return_date"
                            rules="required"
                            :value="old('return_date')"
                            :label="trans('customform::app.fields.return-date')"
                        />

                        <x-shop::form.control-group.error control-name="return_date" />
                    </x-shop::form.control-group>

                    {{-- Field 5: Contact Number --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.contact-number')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="contact_number"
                            rules="required"
                            :value="old('contact_number')"
                            :label="trans('customform::app.fields.contact-number')"
                            :placeholder="trans('customform::app.fields.contact-number-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="contact_number" />
                    </x-shop::form.control-group>

                    {{-- Field 6: Email --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.email')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="email"
                            name="email"
                            rules="required|email"
                            :value="old('email')"
                            :label="trans('customform::app.fields.email')"
                            :placeholder="trans('customform::app.fields.email-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="email" />
                    </x-shop::form.control-group>

                    {{-- Field 7: Refund Type --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label class="required">
                            @lang('customform::app.fields.refund-type')
                        </x-shop::form.control-group.label>

                        <div class="flex flex-col gap-2 mt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="radio"
                                    name="refund_type"
                                    value="local"
                                    @checked(old('refund_type', 'local') === 'local')
                                />
                                @lang('customform::app.fields.refund-type-local')
                            </label>

                            <label class="flex items-center gap-2 cursor-pointer">
                                <input
                                    type="radio"
                                    name="refund_type"
                                    value="overseas"
                                    @checked(old('refund_type', 'local') === 'overseas')
                                />
                                @lang('customform::app.fields.refund-type-overseas')
                            </label>
                        </div>

                        <x-shop::form.control-group.error control-name="refund_type" />
                    </x-shop::form.control-group>

                    {{-- Field 8: Local Bank Info --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label>
                            @lang('customform::app.fields.local-bank-info')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="local_bank_info"
                            :value="old('local_bank_info')"
                            :label="trans('customform::app.fields.local-bank-info')"
                            :placeholder="trans('customform::app.fields.local-bank-info-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="local_bank_info" />
                    </x-shop::form.control-group>

                    {{-- Field 9: Overseas Bank Info --}}
                    <x-shop::form.control-group>
                        <x-shop::form.control-group.label>
                            @lang('customform::app.fields.overseas-bank-info')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="text"
                            name="overseas_bank_info"
                            :value="old('overseas_bank_info')"
                            :label="trans('customform::app.fields.overseas-bank-info')"
                            :placeholder="trans('customform::app.fields.overseas-bank-info-placeholder')"
                        />

                        <x-shop::form.control-group.error control-name="overseas_bank_info" />
                    </x-shop::form.control-group>

                    {{-- Captcha --}}
                    @if (core()->getConfigData('customer.captcha.credentials.status'))
                        <x-shop::form.control-group class="mt-5">
                            {!! \Webkul\Customer\Facades\Captcha::render() !!}

                            <x-shop::form.control-group.error control-name="recaptcha_token" />
                        </x-shop::form.control-group>
                    @endif

                    {{-- Submit --}}
                    <div class="mt-8 flex flex-wrap items-center gap-9 max-sm:justify-center max-sm:text-center">
                        <button
                            class="primary-button m-0 mx-auto block w-full max-w-[374px] rounded-2xl px-11 py-4 text-center text-base max-md:max-w-full max-md:rounded-lg max-md:py-3 max-sm:py-1.5 ltr:ml-0 rtl:mr-0"
                            type="submit"
                        >
                            @lang('customform::app.submit')
                        </button>
                    </div>
                </x-shop::form>
            </div>
        </div>
    </div>

    @push('scripts')
        {!! \Webkul\Customer\Facades\Captcha::renderJS() !!}
    @endpush
</x-shop::layouts>
