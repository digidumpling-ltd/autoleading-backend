<!-- Register Reward -->
@if (
    core()->getConfigData('reward.general.general.reward-when-customer-register') 
    && core()->getConfigData('reward.general.general.module-status')
)
    <p class="text-[#858585]"><b> @lang('rewards::app.shop.register.register-reward', ['points' => core()->getConfigData('reward.general.general.reward-when-customer-register')])</b></p>
@endif