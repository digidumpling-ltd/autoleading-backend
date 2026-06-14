@component('admin::emails.layout')
    @php $dashboardUrl = route('shop.customer.verification.index'); @endphp

    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px;font-weight: 600;color: #121A26">
            @lang('customer-verification::app.emails.verification-rejected.title')
        </span> <br>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('shop::app.emails.dear', ['customer_name' => $customer->name]),👋
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('customer-verification::app.emails.verification-rejected.greeting')
        </p>
    </div>

    <div style="font-size: 20px;font-weight: 600;color: #121A26">
        @lang('customer-verification::app.emails.verification-rejected.summary')
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
            <span style="color: #dc2626;font-weight: 600;">@lang('customer-verification::app.common.verification_status_rejected')</span>
        </div>

        @if ($customer->rejection_reason)
            <div style="margin-top: 10px;padding: 16px;background-color: #fef2f2;border-left: 4px solid #dc2626;border-radius: 4px;">
                <div style="font-weight: 600;color: #121A26;margin-bottom: 6px;">
                    @lang('customer-verification::app.common.rejection_reason')
                </div>
                <div style="color: #384860;">{{ $customer->rejection_reason }}</div>
            </div>
        @endif
    </div>

    <div style="margin-bottom: 40px;">
        <a
            href="{{ $dashboardUrl }}"
            style="display: inline-block;padding: 12px 24px;background-color: #2969FF;color: #ffffff;font-size: 16px;font-weight: 600;text-decoration: none;border-radius: 6px;"
        >
            @lang('customer-verification::app.emails.verification-rejected.cta')
        </a>
    </div>
@endcomponent
