{{-- Personal Data Collection Statement --}}
<div class="mb-2 flex select-none items-center gap-1.5">
    <x-shop::form.control-group.control
        type="checkbox"
        name="agree_pdcs"
        id="agree-pdcs"
        value="1"
        rules="required"
        :label="trans('customer-verification::app.signup.pdcs_title')"
        for="agree-pdcs"
    />

    <label
        class="cursor-pointer select-none text-base text-zinc-500 max-sm:text-sm"
        for="agree-pdcs"
        v-pre
    >
        {!! trans('customer-verification::app.signup.pdcs_agree', [
            'link' => '<a href="#" target="_blank" class="text-navyBlue">' . trans('customer-verification::app.signup.pdcs_title') . '</a>',
        ]) !!}
    </label>
</div>

<x-shop::form.control-group.error control-name="agree_pdcs" />

{{-- Membership Terms & Conditions --}}
<div class="mb-2 flex select-none items-center gap-1.5">
    <x-shop::form.control-group.control
        type="checkbox"
        name="agree_membership_tnc"
        id="agree-membership-tnc"
        value="1"
        rules="required"
        :label="trans('customer-verification::app.signup.membership_tnc_title')"
        for="agree-membership-tnc"
    />

    <label
        class="cursor-pointer select-none text-base text-zinc-500 max-sm:text-sm"
        for="agree-membership-tnc"
        v-pre
    >
        {!! trans('customer-verification::app.signup.membership_tnc_agree', [
            'link' => '<a href="#" target="_blank" class="text-navyBlue">' . trans('customer-verification::app.signup.membership_tnc_title') . '</a>',
        ]) !!}
    </label>
</div>

<x-shop::form.control-group.error control-name="agree_membership_tnc" />
