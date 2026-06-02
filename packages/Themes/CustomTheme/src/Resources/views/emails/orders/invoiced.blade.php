@component('shop::emails.layout')
    <div style="margin-bottom: 34px;">
        <span style="font-size: 22px;font-weight: 600;color: #121A26">
            @lang('custom-theme::app.emails.orders.payment-confirmed.title')
        </span>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('custom-theme::app.emails.orders.payment-confirmed.greeting')
        </p>

        <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
            @lang('custom-theme::app.emails.orders.payment-confirmed.review-message')
        </p>
    </div>

    {{-- Reservation Summary --}}
    <div style="background:#f8f8f8;border-radius:6px;padding:20px 24px;margin-bottom:32px;">
        <div style="font-size:18px;font-weight:600;color:#121A26;margin-bottom:16px;">
            📄 @lang('custom-theme::app.emails.orders.payment-confirmed.summary-title')
        </div>

        <table style="width:100%;font-size:15px;color:#384860;line-height:28px;">
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.payment-confirmed.order-number')
                </td>
                <td>#{{ $invoice->order->increment_id }}</td>
            </tr>
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.payment-confirmed.vehicle-model')
                </td>
                <td>{{ $invoice->order->items->first()?->name }}</td>
            </tr>
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.payment-confirmed.pickup-date')
                </td>
                <td>{{ core()->formatDate($invoice->order->created_at, 'd/m/Y') }}</td>
            </tr>
            @if ($invoice->order->shipping_address)
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.payment-confirmed.return-date')
                </td>
                <td>{{ $invoice->order->shipping_address->address }}</td>
            </tr>
            @endif
            <tr>
                <td style="font-weight:600;padding-right:16px;white-space:nowrap;">
                    @lang('custom-theme::app.emails.orders.payment-confirmed.amount-paid')
                </td>
                <td>{{ core()->formatPrice($invoice->order->grand_total, $invoice->order->order_currency_code) }}</td>
            </tr>
        </table>
    </div>

    {{-- What Happens Next --}}
    <div style="margin-bottom:32px;">
        <div style="font-size:18px;font-weight:600;color:#121A26;margin-bottom:12px;">
            💡 @lang('custom-theme::app.emails.orders.payment-confirmed.next-steps-title')
        </div>

        <ol style="font-size:15px;color:#384860;line-height:26px;padding-left:20px;margin:0;">
            <li style="margin-bottom:8px;">@lang('custom-theme::app.emails.orders.payment-confirmed.next-step-1')</li>
            <li style="margin-bottom:8px;">@lang('custom-theme::app.emails.orders.payment-confirmed.next-step-2')</li>
            <li style="margin-bottom:8px;">@lang('custom-theme::app.emails.orders.payment-confirmed.next-step-3')</li>
        </ol>
    </div>

    {{-- Contact note --}}
    <div style="font-size:15px;color:#5E5E5E;line-height:24px;border-left:4px solid #2969FF;padding-left:12px;margin-bottom:24px;">
        📞 @lang('custom-theme::app.emails.orders.payment-confirmed.contact-note')
    </div>

    <p style="font-size:15px;color:#384860;">
        @lang('custom-theme::app.emails.orders.payment-confirmed.sign-off')
    </p>
@endcomponent
