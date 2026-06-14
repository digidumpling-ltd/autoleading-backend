@component('admin::emails.layout')
    @php
        $detailUrl = route('admin.verification.show', $customer->id);
        $reference = $customer->reference_number;
    @endphp

    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px;font-weight: 600;color: #121A26">
            @lang('customer-verification::app.emails.documents-submitted.title')
        </span> <br>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('admin::app.emails.dear', ['admin_name' => core()->getAdminEmailDetails()['name']]),👋
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            {!! trans('customer-verification::app.emails.documents-submitted.greeting', [
                'customer_name' => '<strong>' . e($customer->name) . '</strong>',
            ]) !!}
        </p>
    </div>

    <div style="font-size: 20px;font-weight: 600;color: #121A26">
        @lang('customer-verification::app.emails.documents-submitted.summary')
    </div>

    <div style="margin-top: 20px;margin-bottom: 40px;line-height: 30px;font-size: 16px;color: #384860;">
        <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
            <span style="font-weight: 600;">@lang('customer-verification::app.common.customer_name')</span>
            <span>{{ $customer->name }}</span>
        </div>

        <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
            <span style="font-weight: 600;">@lang('customer-verification::app.common.email')</span>
            <span>{{ $customer->email }}</span>
        </div>

        @if ($customer->phone)
            <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
                <span style="font-weight: 600;">@lang('customer-verification::app.emails.documents-submitted.phone')</span>
                <span>{{ $customer->phone }}</span>
            </div>
        @endif

        <div style="display: grid;gap: 20px;grid-template-columns: 200px 1fr;">
            <span style="font-weight: 600;">@lang('customer-verification::app.common.reference_number')</span>
            <span>
                <a href="{{ $detailUrl }}" style="color: #2969FF;">{{ $reference }}</a>
            </span>
        </div>
    </div>

    <div style="margin-bottom: 40px;">
        <a
            href="{{ $detailUrl }}"
            style="display: inline-block;padding: 12px 24px;background-color: #2969FF;color: #ffffff;font-size: 16px;font-weight: 600;text-decoration: none;border-radius: 6px;"
        >
            @lang('customer-verification::app.emails.documents-submitted.review-btn')
        </a>
    </div>
@endcomponent
