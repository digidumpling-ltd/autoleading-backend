@php
$customer = auth()->guard('customer')->user();
$service = app(\Webkul\MobilePass\Services\MobilePassService::class);
@endphp

@if ($customer?->is_verified && $customer->group && $service->hasAppleCredentials())
@php
$pass = $service->getCustomerApplePass($customer->id);

$locale = app()->getLocale();

$buttonFile = public_path('vendor/mobile-pass/images/apple-wallet-button/' . $locale . '.svg');
$buttonSrc  = asset('vendor/mobile-pass/images/apple-wallet-button/' . (file_exists($buttonFile) ? $locale : 'en') . '.svg');
@endphp

<div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
    <p class="text-sm font-medium">
        @lang('mobile-pass::app.customers.account.profile.apple-loyalty-pass')
    </p>

    <div>
        @if ($pass)
            <a href="{{ $pass->addToWalletUrl() }}" target="_blank">
                <img src="{{ $buttonSrc }}" alt="@lang('mobile-pass::app.common.view-on-apple-wallet')"
                    style="height:48px;width:auto;">
            </a>
        @else
            <a href="{{ route('shop.customers.account.mobile-pass.apple.save') }}">
                <img src="{{ $buttonSrc }}" alt="@lang('mobile-pass::app.common.save-to-apple-wallet')"
                    style="height:48px;width:auto;">
            </a>
        @endif
    </div>
</div>
@endif
