@php
    $bookingProduct = $product?->booking_products()?->first();
    $dayPricingRules = $bookingProduct
        ? app(\Webkul\RentalPricing\Repositories\BookingProductDayPricingRepository::class)
            ->findWhere(['booking_product_id' => $bookingProduct->id])
            ->values()
            ->toArray()
        : [];
@endphp

@if ($bookingProduct && $bookingProduct->type === 'rental')
    <v-day-pricing
        :booking-product-id="{{ $bookingProduct->id }}"
        :initial-rules="{{ json_encode($dayPricingRules) }}"
        store-url="{{ route('admin.rental-pricing.day-pricings.store', $bookingProduct->id) }}"
    ></v-day-pricing>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-day-pricing-template"
        >
            <div>
                <!-- Header row matching slots UI -->
                <div class="flex items-center justify-between gap-5 py-2">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">
                            @lang('rental-pricing::app.admin.day-pricing.title')
                        </p>

                        <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                            @lang('rental-pricing::app.admin.day-pricing.description')
                        </p>
                    </div>

                    <div class="secondary-button" @click="openDrawer">
                        @lang('rental-pricing::app.admin.day-pricing.add-rules')
                    </div>
                </div>

                <!-- Summary chips -->
                <div class="overflow-x-auto">
                    <template v-if="rules.length">
                        <div class="flex flex-wrap gap-x-2.5">
                            <div
                                v-for="(rule, index) in rules"
                                :key="index"
                                class="flex min-h-[38px] flex-wrap items-center gap-1"
                            >
                                <p class="flex items-center rounded bg-gray-600 px-2 py-1 font-semibold text-white">
                                    @{{ chipLabel(rule) }}
                                    <span
                                        class="icon-cross cursor-pointer text-lg text-white ltr:ml-1.5 rtl:mr-1.5"
                                        @click="removeRule(index)"
                                    ></span>
                                </p>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <p class="py-2 text-sm text-gray-400 dark:text-gray-500">
                            @lang('rental-pricing::app.admin.day-pricing.no-rules')
                        </p>
                    </template>
                </div>

                <!-- Side Drawer -->
                <x-admin::drawer ref="drawerRef">
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <p class="my-2.5 text-xl font-medium dark:text-white">
                                @lang('rental-pricing::app.admin.day-pricing.title')
                            </p>

                            <div class="flex items-center gap-4 ltr:mr-11 rtl:ml-11">
                                <div
                                    class="w-fit cursor-pointer font-medium text-blue-600 dark:text-white"
                                    @click="addRow"
                                >
                                    @lang('rental-pricing::app.admin.day-pricing.add-rule')
                                </div>

                                <button
                                    type="button"
                                    class="primary-button"
                                    :disabled="saving"
                                    @click="save"
                                >
                                    @lang('rental-pricing::app.admin.day-pricing.save')
                                </button>
                            </div>
                        </div>
                    </x-slot:header>

                    <x-slot:content>
                        <!-- Error list -->
                        <template v-if="errors.length">
                            <div class="mb-3 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/20">
                                <p v-for="error in errors" :key="error" v-text="error"></p>
                            </div>
                        </template>

                        <!-- Rule rows — each field has its own label+input stacked -->
                        <template v-if="draft.length">
                            <div
                                v-for="(row, index) in draft"
                                :key="index"
                                class="mx-2.5 mb-4 flex items-end gap-2.5"
                            >
                                <!-- Min Days -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('rental-pricing::app.admin.day-pricing.min-days')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="number"
                                        ::name="'day_pricing[' + index + '][min_days]'"
                                        v-model="row.min_days"
                                        min="1"
                                        :placeholder="trans('rental-pricing::app.admin.day-pricing.min-days')"
                                    />
                                </x-admin::form.control-group>

                                <!-- Max Days -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('rental-pricing::app.admin.day-pricing.max-days')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="number"
                                        ::name="'day_pricing[' + index + '][max_days]'"
                                        v-model="row.max_days"
                                        min="1"
                                        :placeholder="trans('rental-pricing::app.admin.day-pricing.max-days-placeholder')"
                                    />
                                </x-admin::form.control-group>

                                <!-- Discount Type -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('rental-pricing::app.admin.day-pricing.discount-type')
                                    </x-admin::form.control-group.label>

                                    <select
                                        v-model="row.discount_type"
                                        class="custom-select w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    >
                                        <option value="fixed">@lang('rental-pricing::app.admin.day-pricing.fixed')</option>
                                        <option value="percentage">@lang('rental-pricing::app.admin.day-pricing.percentage')</option>
                                    </select>
                                </x-admin::form.control-group>

                                <!-- Discount Value -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('rental-pricing::app.admin.day-pricing.discount-value')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="number"
                                        ::name="'day_pricing[' + index + '][discount_value]'"
                                        v-model="row.discount_value"
                                        min="0"
                                        step="0.01"
                                        ::placeholder="row.discount_type === 'percentage' ? '0–100' : '0.00'"
                                    />
                                </x-admin::form.control-group>

                                <!-- Delete -->
                                <div
                                    class="icon-delete mb-3 w-fit cursor-pointer p-1.5 text-2xl transition-all"
                                    @click="draft.splice(index, 1)"
                                ></div>
                            </div>
                        </template>

                        <template v-else>
                            <p class="mx-2.5 text-sm text-gray-400 dark:text-gray-500">
                                @lang('rental-pricing::app.admin.day-pricing.no-rules')
                            </p>
                        </template>
                    </x-slot:content>
                </x-admin::drawer>
            </div>
        </script>

        <script type="module">
            app.component('v-day-pricing', {
                template: '#v-day-pricing-template',

                props: {
                    bookingProductId: { type: Number, required: true },
                    initialRules:     { type: Array,  default: () => [] },
                    storeUrl:         { type: String, required: true },
                },

                data() {
                    return {
                        rules:  this.initialRules.map(r => ({
                            min_days:       r.min_days,
                            max_days:       r.max_days ?? '',
                            discount_type:  r.discount_type,
                            discount_value: r.discount_value,
                        })),
                        draft:  [],
                        errors: [],
                        saving: false,
                    };
                },

                methods: {
                    normalize(r) {
                        return {
                            min_days:       r.min_days,
                            max_days:       r.max_days ?? '',
                            discount_type:  r.discount_type,
                            discount_value: r.discount_value,
                        };
                    },

                    openDrawer() {
                        this.draft  = this.rules.map(r => ({ ...r }));
                        this.errors = [];
                        this.$refs.drawerRef.toggle();
                    },

                    addRow() {
                        this.draft.push({ min_days: '', max_days: '', discount_type: 'fixed', discount_value: '' });
                    },

                    removeRule(index) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.rules.splice(index, 1);
                                this.saveQuiet();
                            },
                        });
                    },

                    chipLabel(rule) {
                        const max = rule.max_days !== '' && rule.max_days != null ? rule.max_days : null;
                        const range = max ? `${rule.min_days}–${max}d` : `${rule.min_days}+d`;
                        const discount = rule.discount_type === 'percentage'
                            ? `${rule.discount_value}%`
                            : `-${rule.discount_value}`;

                        return `${range}: ${discount}`;
                    },

                    save() {
                        this.saving = true;
                        this.errors = [];

                        const payload = this.draft.map(r => ({
                            min_days:       r.min_days,
                            max_days:       r.max_days === '' ? null : r.max_days,
                            discount_type:  r.discount_type,
                            discount_value: r.discount_value,
                        }));

                        this.$axios.post(this.storeUrl, { rules: payload })
                            .then(r => {
                                this.rules = (r.data.data || []).map(this.normalize);
                                this.$refs.drawerRef.toggle();
                            })
                            .catch(err => {
                                if (err.response?.status === 422) {
                                    this.errors = err.response.data.errors || [];
                                }
                            })
                            .finally(() => { this.saving = false; });
                    },

                    saveQuiet() {
                        const payload = this.rules.map(r => ({
                            min_days:       r.min_days,
                            max_days:       r.max_days === '' ? null : r.max_days,
                            discount_type:  r.discount_type,
                            discount_value: r.discount_value,
                        }));

                        this.$axios.post(this.storeUrl, { rules: payload }).catch(() => {});
                    },
                },
            });
        </script>
    @endpushOnce
@endif
