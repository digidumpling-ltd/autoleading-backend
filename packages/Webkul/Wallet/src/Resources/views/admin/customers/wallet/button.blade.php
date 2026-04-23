@if (bouncer()->hasPermission('customers.wallet'))
    <a
        class="inline-flex w-full max-w-max cursor-pointer items-center justify-between gap-x-2 px-1 py-1.5 text-center font-semibold text-gray-600 transition-all hover:rounded-md hover:bg-gray-200 dark:text-gray-300 dark:hover:bg-gray-800"
        :href="'{{ rtrim(config('app.url'), '/') }}/{{ config('app.admin_url') }}/customers/' + customer.id + '/wallet'"
    >
        <span class="icon-wallet text-2xl"></span>

        @lang('bagisto-wallet::app.admin.customers.wallet.view-wallet')
    </a>
@endif
