<v-wallet-balance-card
    :customer-id="customer.id"
    base-url="{{ rtrim(config('app.url'), '/') }}/{{ config('app.admin_url') }}/customers"
></v-wallet-balance-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-wallet-balance-card-template"
    >
        <div class="box-shadow rounded bg-white dark:bg-gray-900">
            <!-- Header -->
            <div class="flex items-center justify-between p-1.5">
                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                    @lang('bagisto-wallet::app.admin.customers.wallet.balance-card.title')
                </p>

                <div class="flex items-center">
                    <div
                        class="flex cursor-pointer items-center gap-1.5 px-2.5 text-blue-600 transition-all hover:underline"
                        @click="openDrawer"
                    >
                        @lang('bagisto-wallet::app.common.edit-btn')
                    </div>

                    <span
                        class="cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950"
                        :class="[isOpen ? 'icon-arrow-up' : 'icon-arrow-down']"
                        @click="isOpen = ! isOpen"
                    ></span>
                </div>
            </div>

            <!-- Content -->
            <div class="px-4 pb-4" v-show="isOpen">
                <template v-if="loading">
                    <div class="shimmer mb-2 h-8 w-28 rounded"></div>
                    <div class="shimmer h-3 w-20 rounded"></div>
                </template>

                <template v-else>
                    <p class="text-3xl font-bold text-gray-800 dark:text-white" v-text="balance"></p>

                    <a
                        :href="`${baseUrl}/${customerId}/wallet`"
                        class="mt-1 inline-block text-xs font-medium text-blue-600 hover:underline dark:text-blue-400"
                    >
                        @lang('bagisto-wallet::app.admin.customers.wallet.balance-card.view-history')
                    </a>
                </template>
            </div>

            <!-- Adjust drawer -->
            <x-admin::drawer ref="drawerRef">
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <p class="my-2.5 text-xl font-medium dark:text-white">
                            @lang('bagisto-wallet::app.admin.customers.wallet.balance-card.drawer-title')
                        </p>

                        <div class="ltr:mr-11 rtl:ml-11">
                            <button
                                type="button"
                                class="primary-button"
                                :disabled="saving"
                                @click="submit"
                            >
                                @lang('bagisto-wallet::app.common.save-btn')
                            </button>
                        </div>
                    </div>
                </x-slot>

                <x-slot:content>
                    <!-- Type -->
                    <div class="mb-4">
                        <label class="mb-1.5 flex required items-center gap-1 text-xs font-medium text-gray-800 dark:text-white">
                            @lang('bagisto-wallet::app.admin.customers.wallet.balance-card.type-label')
                        </label>

                        <div class="flex gap-4">
                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" v-model="form.type" value="add" class="accent-blue-600" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    @lang('bagisto-wallet::app.admin.customers.wallet.type-add')
                                </span>
                            </label>

                            <label class="flex cursor-pointer items-center gap-2">
                                <input type="radio" v-model="form.type" value="deduct" class="accent-blue-600" />
                                <span class="text-sm text-gray-700 dark:text-gray-300">
                                    @lang('bagisto-wallet::app.admin.customers.wallet.type-deduct')
                                </span>
                            </label>
                        </div>

                        <p v-if="errors.type" class="mt-1 text-xs italic text-red-600" v-text="errors.type"></p>
                    </div>

                    <!-- Amount -->
                    <div class="mb-4">
                        <label class="mb-1.5 flex required items-center gap-1 text-xs font-medium text-gray-800 dark:text-white">
                            @lang('bagisto-wallet::app.admin.customers.wallet.amount')
                        </label>

                        <input
                            type="number"
                            v-model="form.amount"
                            min="0.01"
                            step="0.01"
                            :class="errors.amount ? 'border-red-500 hover:border-red-500 focus:border-red-500' : ''"
                            class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            placeholder="@lang('bagisto-wallet::app.admin.customers.wallet.balance-card.amount-placeholder')"
                        />

                        <p v-if="errors.amount" class="mt-1 text-xs italic text-red-600" v-text="errors.amount"></p>
                    </div>

                    <!-- Reason -->
                    <div class="mb-4">
                        <label class="mb-1.5 flex required items-center gap-1 text-xs font-medium text-gray-800 dark:text-white">
                            @lang('bagisto-wallet::app.admin.customers.wallet.reason')
                        </label>

                        <textarea
                            v-model="form.reason"
                            rows="4"
                            :class="errors.reason ? 'border-red-500 hover:border-red-500 focus:border-red-500' : ''"
                            class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            placeholder="@lang('bagisto-wallet::app.admin.customers.wallet.balance-card.reason-placeholder')"
                        ></textarea>

                        <p v-if="errors.reason" class="mt-1 text-xs italic text-red-600" v-text="errors.reason"></p>
                    </div>

                    <!-- Notify Customer (only for add) -->
                    <div v-show="form.type === 'add'" class="mb-2">
                        <label class="flex w-max cursor-pointer select-none items-center gap-1 p-1.5">
                            <input
                                type="checkbox"
                                v-model="form.notify_customer"
                                class="peer hidden"
                            >

                            <span class="icon-uncheckbox peer-checked:icon-checked cursor-pointer rounded-md text-2xl peer-checked:text-blue-600"></span>

                            <p class="flex cursor-pointer items-center gap-x-1 font-semibold text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-gray-100">
                                @lang('bagisto-wallet::app.admin.customers.wallet.notify-customer')
                            </p>
                        </label>
                    </div>
                </x-slot>
            </x-admin::drawer>
        </div>
    </script>

    <script type="module">
        app.component('v-wallet-balance-card', {
            template: '#v-wallet-balance-card-template',

            props: {
                customerId: { type: [Number, String], required: true },
                baseUrl:    { type: String, required: true },
            },

            data() {
                return {
                    isOpen:  true,
                    loading: true,
                    balance: '',
                    saving:  false,
                    errors:  { type: '', amount: '', reason: '' },
                    form:    { type: 'add', amount: '', reason: '', notify_customer: false },
                };
            },

            mounted() {
                this.$axios.get(`${this.baseUrl}/${this.customerId}/wallet/balance`)
                    .then(r => { this.balance = r.data.balance; })
                    .finally(() => { this.loading = false; });
            },

            methods: {
                openDrawer() {
                    this.form   = { type: 'add', amount: '', reason: '', notify_customer: false };
                    this.errors = { type: '', amount: '', reason: '' };
                    this.$refs.drawerRef.toggle();
                },

                submit() {
                    this.errors = { type: '', amount: '', reason: '' };
                    let valid = true;

                    if (! this.form.type) {
                        this.errors.type = '@lang('bagisto-wallet::app.admin.customers.wallet.balance-card.error-type')';
                        valid = false;
                    }

                    if (! this.form.amount || parseFloat(this.form.amount) < 0.01) {
                        this.errors.amount = '@lang('bagisto-wallet::app.admin.customers.wallet.balance-card.error-amount')';
                        valid = false;
                    }

                    if (! this.form.reason || this.form.reason.trim().length < 5) {
                        this.errors.reason = '@lang('bagisto-wallet::app.admin.customers.wallet.balance-card.error-reason')';
                        valid = false;
                    }

                    if (! valid) return;

                    this.saving = true;

                    this.$axios.post(`${this.baseUrl}/${this.customerId}/wallet/ajax-adjust`, {
                        type:            this.form.type,
                        amount:          this.form.amount,
                        reason:          this.form.reason,
                        notify_customer: this.form.notify_customer ? 1 : 0,
                    })
                    .then(r => {
                        this.balance = r.data.balance;
                        this.$refs.drawerRef.toggle();
                        this.$emitter.emit('add-flash', {
                            type:    'success',
                            message: r.data.message,
                        });
                    })
                    .catch(err => {
                        if (err.response?.status === 422) {
                            const errs = err.response.data.errors ?? {};
                            this.errors.type   = errs.type?.[0]   ?? '';
                            this.errors.amount = errs.amount?.[0] ?? '';
                            this.errors.reason = errs.reason?.[0] ?? '';
                        }
                    })
                    .finally(() => {
                        this.saving = false;
                    });
                },
            },
        });
    </script>
@endPushOnce
