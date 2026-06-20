@component('shop::emails.layout')
    <div style="padding: 30px;">

        <p style="font-weight: bold; font-size: 20px; color: #242424; line-height: 28px; margin-bottom: 8px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.dear', ['customer_name' => $data['name']])
        </p>

        <p style="font-size: 16px; color: #5E5E5E; line-height: 24px; margin-bottom: 8px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.greeting')
        </p>

        <p style="font-size: 16px; color: #5E5E5E; line-height: 24px; margin-bottom: 24px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.body')
        </p>

        <p style="font-weight: bold; font-size: 16px; color: #242424; line-height: 24px; margin-bottom: 12px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.transaction-details')
        </p>

        <table style="width: 100%; border-collapse: collapse; margin-bottom: 24px;">
            <tr>
                <td style="padding: 8px 12px; font-size: 15px; color: #5E5E5E; border: 1px solid #E5E7EB; background: #F9FAFB; width: 45%;">
                    @lang('bagisto-wallet::app.mail.wallet-reward.member-account')
                </td>
                <td style="padding: 8px 12px; font-size: 15px; color: #242424; border: 1px solid #E5E7EB;">
                    {{ $data['customer_id'] }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 15px; color: #5E5E5E; border: 1px solid #E5E7EB; background: #F9FAFB;">
                    @lang('bagisto-wallet::app.mail.wallet-reward.transaction-time')
                </td>
                <td style="padding: 8px 12px; font-size: 15px; color: #242424; border: 1px solid #E5E7EB;">
                    {{ $data['transaction_time'] }}
                </td>
            </tr>
            <tr>
                <td style="padding: 8px 12px; font-size: 15px; color: #5E5E5E; border: 1px solid #E5E7EB; background: #F9FAFB;">
                    @lang('bagisto-wallet::app.mail.wallet-reward.topup-amount')
                </td>
                <td style="padding: 8px 12px; font-size: 15px; color: #242424; border: 1px solid #E5E7EB;">
                    {{ $data['amount'] }}
                </td>
            </tr>
        </table>

        <p style="font-size: 15px; color: #5E5E5E; line-height: 24px; margin-bottom: 16px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.footer')
        </p>

        <p style="font-size: 15px; color: #5E5E5E; line-height: 24px; margin-bottom: 4px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.thanks')
        </p>

        <p style="font-size: 15px; color: #5E5E5E; line-height: 24px; margin-bottom: 4px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.closing')
        </p>

        <p style="font-size: 15px; font-weight: bold; color: #242424; line-height: 24px; margin-top: 16px;">
            @lang('bagisto-wallet::app.mail.wallet-reward.team')
        </p>

    </div>
@endcomponent
