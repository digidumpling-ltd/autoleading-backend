@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px;font-weight: 600;color: #121A26">
            @lang('custom-theme::app.emails.orders.order-confirmed.title')
        </span>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('custom-theme::app.emails.orders.order-confirmed.greeting')
        </p>
    </div>

    {{-- Confirmed Reservation Summary --}}
    <div style="background:#f8f8f8;border-radius:6px;padding:20px 24px;margin-bottom:32px;">
        <div style="font-size:18px;font-weight:600;color:#121A26;margin-bottom:16px;">
            🚗 @lang('custom-theme::app.emails.orders.order-confirmed.summary-title')
        </div>

        <table style="width:100%;font-size:15px;color:#384860;line-height:28px;">
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.order-confirmed.order-number')
                </td>
                <td>#{{ $order->increment_id }}</td>
            </tr>
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.order-confirmed.vehicle-model')
                </td>
                <td>{{ $order->items->first()?->name }}</td>
            </tr>
            @if ($order->shipping_address)
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.order-confirmed.pickup-time-location')
                </td>
                <td>{{ $order->shipping_address->address }}, {{ $order->shipping_address->city }}</td>
            </tr>
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.order-confirmed.return-time-location')
                </td>
                <td>{{ $order->shipping_address->address }}, {{ $order->shipping_address->city }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.order-confirmed.payment-status')
                </td>
                <td>@lang('custom-theme::app.emails.orders.order-confirmed.payment-paid')
                    — {{ core()->formatPrice($order->grand_total, $order->order_currency_code) }}
                </td>
            </tr>
        </table>
    </div>

    {{-- Process overview --}}
    <p style="font-size:15px;color:#384860;line-height:24px;margin-bottom:24px;">
        @lang('custom-theme::app.emails.orders.order-confirmed.process-overview')
    </p>

    {{-- Section 1: Required Documents --}}
    <div style="margin-bottom:28px;">
        <div style="font-size:17px;font-weight:600;color:#121A26;margin-bottom:8px;">
            1. @lang('custom-theme::app.emails.orders.order-confirmed.docs-title')
        </div>
        <p style="font-size:15px;color:#384860;line-height:24px;margin:0 0 8px;">
            @lang('custom-theme::app.emails.orders.order-confirmed.docs-intro')
        </p>
        <p style="font-size:15px;font-weight:600;color:#121A26;margin:8px 0 4px;">
            @lang('custom-theme::app.emails.orders.order-confirmed.docs-combinations-title')
        </p>
        <ul style="font-size:15px;color:#384860;line-height:26px;padding-left:20px;margin:0 0 8px;">
            <li>@lang('custom-theme::app.emails.orders.order-confirmed.docs-combo-a')</li>
            <li>@lang('custom-theme::app.emails.orders.order-confirmed.docs-combo-b')</li>
            <li>@lang('custom-theme::app.emails.orders.order-confirmed.docs-combo-c')</li>
        </ul>
        <p style="font-size:15px;color:#384860;line-height:24px;margin:4px 0;">
            @lang('custom-theme::app.emails.orders.order-confirmed.docs-email')
        </p>
        <p style="font-size:15px;color:#384860;line-height:24px;margin:4px 0;">
            @lang('custom-theme::app.emails.orders.order-confirmed.docs-deposit')
        </p>
    </div>

    {{-- Section 2: Pick-up Notes --}}
    <div style="margin-bottom:28px;">
        <div style="font-size:17px;font-weight:600;color:#121A26;margin-bottom:8px;">
            2. @lang('custom-theme::app.emails.orders.order-confirmed.pickup-notes-title')
        </div>
        <ul style="font-size:15px;color:#384860;line-height:26px;padding-left:20px;margin:0;">
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.pickup-note-punctuality')</li>
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.pickup-note-inspection')</li>
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.pickup-note-fuel')</li>
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.pickup-note-child')</li>
        </ul>
    </div>

    {{-- Section 3: During Rental --}}
    <div style="margin-bottom:28px;">
        <div style="font-size:17px;font-weight:600;color:#121A26;margin-bottom:8px;">
            3. @lang('custom-theme::app.emails.orders.order-confirmed.rental-title')
        </div>
        <ul style="font-size:15px;color:#384860;line-height:26px;padding-left:20px;margin:0;">
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.rental-hketoll')</li>
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.rental-amenities')</li>
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.rental-deduction')</li>
        </ul>
    </div>

    {{-- Section 4: Return & Settlement --}}
    <div style="margin-bottom:28px;">
        <div style="font-size:17px;font-weight:600;color:#121A26;margin-bottom:8px;">
            4. @lang('custom-theme::app.emails.orders.order-confirmed.return-title')
        </div>
        <ul style="font-size:15px;color:#384860;line-height:26px;padding-left:20px;margin:0;">
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.return-settlement')</li>
            <li style="margin-bottom:6px;">@lang('custom-theme::app.emails.orders.order-confirmed.return-deposit')</li>
        </ul>
    </div>

    {{-- Contact note --}}
    <div style="font-size:15px;color:#5E5E5E;line-height:24px;border-left:4px solid #2969FF;padding-left:12px;margin-bottom:24px;">
        @lang('custom-theme::app.emails.orders.order-confirmed.contact-note')
    </div>

    <p style="font-size:15px;color:#384860;">
        @lang('custom-theme::app.emails.orders.order-confirmed.sign-off')
    </p>
@endcomponent
