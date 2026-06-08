@component('shop::emails.layout')
    <div>
        <div style="padding: 30px;">
            <div style="font-size: 20px;color: #242424;line-height: 30px;margin-bottom: 34px;">
                <p style="font-weight: bold;font-size: 20px;color: #242424;line-height: 24px;">
                    @lang('rewards::app.mail.approved.dear', ['customer_name' => $data['name']]),
                </p>
                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    {!! __('rewards::app.mail.approved.greeting') !!}
                </p>
                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    @if ($data['order_id'])
                        {!! __('rewards::app.mail.approved.points-rewarded', ['points' => $data['points'], 'order_id' => $data['order_id']]) !!}
                    @else
                        {!! __('rewards::app.mail.approved.points-rewarded-no-order', ['points' => $data['points']]) !!}
                    @endif
                </p>


                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    #Note. {!! __($data['note']) !!}
                </p>
                
                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    {!! __('rewards::app.mail.approved.status') !!}
                </p>

                <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                    {!! __('rewards::app.mail.approved.total-point-left',['total_reward_points' => $data['total_reward_points']]) !!}
                </p>
            </div>

            <p style="font-size: 16px;color: #5E5E5E;line-height: 24px;">
                @lang('rewards::app.mail.registration.thanks')
            </p>
        </div>
    </div>
@endcomponent