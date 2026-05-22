@if (
    $order->status === 'pending'
    && $order->invoices->count() === 0
    && bouncer()->hasPermission('sales.orders.edit')
)
    <v-order-price-override>
        <div class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800">
            <span class="icon-edit text-2xl"></span>
            @lang('order-price-override::app.admin.sales.orders.edit.title')
        </div>
    </v-order-price-override>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-order-price-override-template"
        >
            <div>
                <div
                    class="transparent-button px-1 py-1.5 hover:bg-gray-200 dark:text-white dark:hover:bg-gray-800"
                    @click="$refs.editDrawer.open()"
                >
                    <span
                        class="icon-edit text-2xl"
                        role="presentation"
                        tabindex="0"
                    ></span>
                    @lang('order-price-override::app.admin.sales.orders.edit.title')
                </div>

                <!-- Edit Order drawer -->
                <x-admin::form
                    method="POST"
                    :action="route('admin.sales.orders.price-override.store', $order->id)"
                >
                    <x-admin::drawer ref="editDrawer">
                        <!-- Drawer Header -->
                        <x-slot:header>
                            <div class="grid h-8 gap-3">
                                <div class="flex items-center justify-between">
                                    <p class="text-xl font-medium dark:text-white">
                                        @lang('order-price-override::app.admin.sales.orders.edit.drawer-title')
                                    </p>

                                    <div class="flex items-center gap-2 ltr:mr-11 rtl:ml-11">
                                        <button
                                            type="button"
                                            class="secondary-button"
                                            @click="$refs.editDrawer.close()"
                                        >
                                            @lang('order-price-override::app.admin.common.cancel-btn')
                                        </button>

                                        <button
                                            type="submit"
                                            class="primary-button"
                                        >
                                            @lang('order-price-override::app.admin.common.save-btn')
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </x-slot>

                        <!-- Drawer Content -->
                        <x-slot:content class="!p-0">
                            <div class="grid p-4 !pt-0">
                                <div class="grid grid-cols-4 gap-2.5 border-b border-slate-300 py-2 text-sm font-semibold text-gray-600 dark:border-gray-800 dark:text-gray-300">
                                    <span class="col-span-2">@lang('order-price-override::app.admin.sales.orders.edit.product-name')</span>
                                    <span class="text-right">@lang('order-price-override::app.admin.sales.orders.edit.qty')</span>
                                    <span class="text-right">@lang('order-price-override::app.admin.sales.orders.edit.override-total')</span>
                                </div>

                                @foreach ($order->items as $item)
                                    <div class="grid grid-cols-4 items-center gap-2.5 border-b border-slate-300 py-4 dark:border-gray-800">
                                        <div class="col-span-2">
                                            <p
                                                class="break-all text-base font-semibold text-gray-800 dark:text-white"
                                                v-pre
                                            >
                                                {{ $item->name }}
                                            </p>
                                            <p class="text-sm text-gray-600 dark:text-gray-300">
                                                {{ core()->formatBasePrice($item->base_price) }}
                                            </p>
                                        </div>

                                        <div class="text-right text-gray-800 dark:text-white">
                                            {{ $item->qty_ordered }}
                                        </div>

                                        <div>
                                            <x-admin::form.control-group class="mb-0">
                                                <x-admin::form.control-group.control
                                                    type="text"
                                                    :name="'override_total['.$item->id.']'"
                                                    :value="number_format($item->total, 2)"
                                                    class="text-right"
                                                />
                                            </x-admin::form.control-group>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </x-slot>
                    </x-admin::drawer>
                </x-admin::form>
            </div>
        </script>

        <script type="module">
            app.component('v-order-price-override', {
                template: '#v-order-price-override-template',
            });
        </script>
    @endPushOnce
@endif
