@php
    $service = app(\Webkul\MobilePass\Services\MobilePassService::class);
    $pass = $service->getCustomerGooglePass((int) $customerId);
@endphp

<x-admin::accordion>
    <x-slot:header>
        <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
            @lang('mobile-pass::app.admin.customers.pass-status-card.title')
        </p>
    </x-slot:header>

    <x-slot:content>
        <div class="grid gap-y-2.5">
            @if ($pass)
                <p>
                    <span class="label-active">
                        @lang('mobile-pass::app.admin.customers.pass-status-card.issued')
                    </span>
                </p>

                <p class="text-gray-600 dark:text-gray-300">
                    {{ $pass->created_at->format('M d, Y') }}
                </p>

                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @lang('mobile-pass::app.admin.customers.pass-status-card.last-synced'):
                    {{ $pass->updated_at->diffForHumans() }}
                </p>

                <div>
                    <p
                        class="cursor-pointer text-red-600 transition-all hover:underline"
                        @click="$emitter.emit('open-confirm-modal', {
                            message: '{{ trans('mobile-pass::app.admin.customers.pass-status-card.delete-confirm') }}',
                            agree: () => {
                                this.$refs['delete-pass-form'].submit()
                            }
                        })"
                    >
                        @lang('mobile-pass::app.common.delete')
                    </p>
                </div>

                <form
                    ref="delete-pass-form"
                    method="POST"
                    action="{{ route('admin.customers.mobile-pass.destroy', $customerId) }}"
                    style="display:none"
                >
                    @csrf
                    @method('DELETE')
                </form>
            @else
                <p>
                    <span class="label-pending">
                        @lang('mobile-pass::app.admin.customers.pass-status-card.not-issued')
                    </span>
                </p>
            @endif
        </div>
    </x-slot:content>
</x-admin::accordion>
