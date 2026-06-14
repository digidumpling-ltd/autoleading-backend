@component('admin::emails.layout')
    @php $detailUrl = route('shop.customers.account.profile.index'); @endphp

    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px;font-weight: 600;color: #121A26">
            @lang('customer-verification::app.emails.verification-approved.title')
        </span> <br>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $customer->name]),👋
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('customer-verification::app.emails.verification-approved.greeting')
        </p>
    </div>

    <div style="font-size: 20px;font-weight: 600;color: #121A26">
        @lang('customer-verification::app.emails.verification-approved.summary')
    </div>

    <div style="margin-top: 20px;margin-bottom: 40px;line-height: 30px;font-size: 16px;color: #384860;">
        <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
            <span style="font-weight: 600;">@lang('customer-verification::app.common.customer_name')</span>
            <span>{{ $customer->name }}</span>
        </div>

        @if ($customer->reference_number)
            <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
                <span style="font-weight: 600;">@lang('customer-verification::app.common.reference_number')</span>
                <span>{{ $customer->reference_number }}</span>
            </div>
        @endif

        <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
            <span style="font-weight: 600;">@lang('customer-verification::app.common.verification_status_label')</span>
            <span style="color: #16a34a;font-weight: 600;">@lang('customer-verification::app.common.verification_status_approved')</span>
        </div>
    </div>

    <div style="margin-bottom: 40px;">
        <a
            href="{{ $detailUrl }}"
            style="display: inline-block;padding: 12px 24px;background-color: #2969FF;color: #ffffff;font-size: 16px;font-weight: 600;text-decoration: none;border-radius: 6px;"
        >
            @lang('customer-verification::app.emails.verification-approved.cta')
        </a>
    </div>
@endcomponent
