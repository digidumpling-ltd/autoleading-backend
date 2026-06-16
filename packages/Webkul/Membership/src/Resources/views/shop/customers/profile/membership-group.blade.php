@php $group = auth()->guard('customer')->user()?->group; @endphp

<div class="grid w-full grid-cols-[2fr_3fr] border-b border-zinc-200 px-8 py-3 max-md:px-0">
    <p class="text-sm font-medium">
        @lang('bagisto-membership::app.shop.customers.profile.membership-group')
    </p>

    <p class="text-sm font-medium text-zinc-500">
        {{ $group?->name ?? '-' }}
    </p>
</div>
