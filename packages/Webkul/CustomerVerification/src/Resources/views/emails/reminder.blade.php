@component('admin::emails.layout')
    @php $verificationUrl = route('shop.customer.verification.index'); @endphp

    <div style="margin-bottom: 34px;">
        <p style="font-size: 16px; color: #5E5E5E; line-height: 24px;">
            {{ trans('customer-verification::app.emails.reminder.greeting', ['customer_name' => $customer->name]) }}
        </p>

        <p style="font-size: 16px; color: #5E5E5E; line-height: 24px;">
            {{ trans('customer-verification::app.emails.reminder.default-message') }}
        </p>
    </div>

    <div style="margin-bottom: 40px;">
        <a
            href="{{ $verificationUrl }}"
            style="display: inline-block; padding: 12px 24px; background-color: #2969FF; color: #ffffff; font-size: 16px; font-weight: 600; text-decoration: none; border-radius: 6px;"
        >
            {{ trans('customer-verification::app.emails.reminder.cta') }}
        </a>
    </div>
@endcomponent
