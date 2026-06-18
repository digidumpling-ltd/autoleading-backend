@php
$customer = auth()->guard('customer')->user();
$service = app(\Webkul\MobilePass\Services\MobilePassService::class);
@endphp

@if ($customer?->is_verified && $customer->group && $service->hasGoogleCredentials())
@php
$pass = $service->getCustomerGooglePass($customer->id);

$locale = app()->getLocale();

$saveButtonFile = public_path('vendor/mobile-pass/images/wallet-button/' . $locale . '.svg');
$saveButtonSrc  = asset('vendor/mobile-pass/images/wallet-button/' . (file_exists($saveButtonFile) ? $locale : 'en') . '.svg');
$saveBadgeFile  = public_path('vendor/mobile-pass/images/wallet-badge/' . $locale . '.svg');
$saveBadgeSrc   = asset('vendor/mobile-pass/images/wallet-badge/' . (file_exists($saveBadgeFile) ? $locale : 'en') . '.svg');

$viewButtonFile = public_path('vendor/mobile-pass/images/view-wallet-button/' . $locale . '.svg');
$viewButtonSrc  = asset('vendor/mobile-pass/images/view-wallet-button/' . (file_exists($viewButtonFile) ? $locale : 'en') . '.svg');
$viewBadgeFile  = public_path('vendor/mobile-pass/images/view-wallet-badge/' . $locale . '.svg');
$viewBadgeSrc   = asset('vendor/mobile-pass/images/view-wallet-badge/' . (file_exists($viewBadgeFile) ? $locale : 'en') . '.svg');
@endphp

<div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
    <p class="text-sm font-medium">
        @lang('mobile-pass::app.customers.account.profile.loyalty-pass')
    </p>

    <div>
        @if ($pass)
            <a href="{{ $pass->addToWalletUrl() }}" target="_blank">
                <img src="{{ $viewButtonSrc }}" alt="@lang('mobile-pass::app.common.view-on-google-wallet')"
                    class="max-md:hidden" style="height:48px;width:auto;">
                <img src="{{ $viewBadgeSrc }}" alt="@lang('mobile-pass::app.common.view-on-google-wallet')"
                    class="md:hidden" style="height:48px;width:auto;">
            </a>
        @else
            <a href="{{ route('shop.customers.account.mobile-pass.google.save') }}">
                <img src="{{ $saveButtonSrc }}" alt="@lang('mobile-pass::app.common.save-to-google-wallet')"
                    class="max-md:hidden" style="height:48px;width:auto;">
                <img src="{{ $saveBadgeSrc }}" alt="@lang('mobile-pass::app.common.save-to-google-wallet')"
                    class="md:hidden" style="height:48px;width:auto;">
            </a>
        @endif
    </div>
</div>
@endif
