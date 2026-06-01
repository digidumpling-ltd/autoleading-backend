<v-reward-balance-card
    :customer-id="customer.id"
    balance-url="{{ rtrim(config('app.url'), '/') }}/{{ config('app.admin_url') }}/rewards/system/balance"
    allocate-url="{{ rtrim(config('app.url'), '/') }}/{{ config('app.admin_url') }}/rewards/system/allocate"
    view-url="{{ rtrim(config('app.url'), '/') }}/{{ config('app.admin_url') }}/rewards/system/view"
></v-reward-balance-card>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-reward-balance-card-template"
    >
        <div class="rounded-xl border bg-white dark:border-gray-800 dark:bg-gray-900">
            <!-- Header -->
            <div class="flex items-center justify-between p-4">
                <p class="text-base font-semibold text-gray-800 dark:text-white">
                    @lang('rewards::app.admin.rewards.system.balance-card.title')
                </p>

                <a
                    :href="`${viewUrl}/${customerId}`"
                    class="cursor-pointer text-xs font-medium text-blue-600 hover:underline dark:text-blue-400"
                >
                    @lang('rewards::app.admin.rewards.system.balance-card.view-history')
                </a>
            </div>

            <!-- Balance row -->
            <div class="flex items-end justify-between border-t px-4 py-5 dark:border-gray-800">
                <div>
                    <template v-if="loading">
                        <div class="shimmer h-7 w-24 rounded"></div>
                    </template>

                    <template v-else>
                        <p class="text-3xl font-bold text-gray-800 dark:text-white" v-text="balance"></p>

                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            @lang('rewards::app.admin.rewards.system.balance-card.points-label')
                        </p>
                    </template>
                </div>

                <!-- Allocate button -->
                <div
                    class="secondary-button cursor-pointer"
                    @click="openDrawer"
                >
                    @lang('rewards::app.admin.rewards.system.view.allocate-btn')
                </div>
            </div>

            <!-- Allocate drawer -->
            <x-admin::drawer ref="drawerRef">
                <x-slot:header>
                    <div class="flex items-center justify-between">
                        <p class="my-2.5 text-xl font-medium dark:text-white">
                            @lang('rewards::app.admin.rewards.system.view.allocate-modal.title')
                        </p>

                        <div class="ltr:mr-11 rtl:ml-11">
                            <button
                                type="button"
                                class="primary-button"
                                :disabled="saving"
                                @click="submit"
                            >
                                @lang('rewards::app.admin.rewards.system.view.allocate-modal.save-btn')
                            </button>
                        </div>
                    </div>
                </x-slot>

                <x-slot:content>
                    <!-- Points -->
                    <div class="mb-4">
                        <label class="mb-1.5 flex required items-center gap-1 text-xs font-medium text-gray-800 dark:text-white">
                            @lang('rewards::app.admin.rewards.system.view.allocate-modal.points')
                        </label>

                        <input
                            type="number"
                            v-model="form.points"
                            min="1"
                            :class="errors.points ? 'border-red-500 hover:border-red-500 focus:border-red-500' : ''"
                            class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            placeholder="@lang('rewards::app.admin.rewards.system.view.allocate-modal.points-placeholder')"
                        />

                        <p v-if="errors.points" class="mt-1 text-xs italic text-red-600" v-text="errors.points"></p>
                    </div>

                    <!-- Reason -->
                    <div class="mb-4">
                        <label class="mb-1.5 flex required items-center gap-1 text-xs font-medium text-gray-800 dark:text-white">
                            @lang('rewards::app.admin.rewards.system.view.allocate-modal.reason')
                        </label>

                        <textarea
                            v-model="form.reason"
                            rows="4"
                            :class="errors.reason ? 'border-red-500 hover:border-red-500 focus:border-red-500' : ''"
                            class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            placeholder="@lang('rewards::app.admin.rewards.system.view.allocate-modal.reason-placeholder')"
                        ></textarea>

                        <p v-if="errors.reason" class="mt-1 text-xs italic text-red-600" v-text="errors.reason"></p>
                    </div>
                </x-slot>
            </x-admin::drawer>
        </div>
    </script>

    <script type="module">
        app.component('v-reward-balance-card', {
            template: '#v-reward-balance-card-template',

            props: {
                customerId:  { type: [Number, String], required: true },
                balanceUrl:  { type: String, required: true },
                allocateUrl: { type: String, required: true },
                viewUrl:     { type: String, required: true },
            },

            data() {
                return {
                    loading: true,
                    balance: 0,
                    saving:  false,
                    errors:  { points: '', reason: '' },
                    form:    { points: '', reason: '' },
                };
            },

            mounted() {
                this.$axios.get(`${this.balanceUrl}/${this.customerId}`)
                    .then(r => { this.balance = r.data.balance; })
                    .finally(() => { this.loading = false; });
            },

            methods: {
                openDrawer() {
                    this.form   = { points: '', reason: '' };
                    this.errors = { points: '', reason: '' };
                    this.$refs.drawerRef.toggle();
                },

                submit() {
                    this.errors = { points: '', reason: '' };

                    if (! this.form.points || parseInt(this.form.points) < 1) {
                        this.errors.points = '@lang('rewards::app.admin.rewards.system.view.allocate-modal.error-points')';
                    }

                    if (! this.form.reason.trim()) {
                        this.errors.reason = '@lang('rewards::app.admin.rewards.system.view.allocate-modal.error-reason')';
                    }

                    if (this.errors.points || this.errors.reason) {
                        return;
                    }

                    this.saving = true;

                    this.$axios.post(`${this.allocateUrl}/${this.customerId}`, {
                        points: this.form.points,
                        reason: this.form.reason,
                    })
                    .then(r => {
                        this.balance += parseInt(this.form.points);
                        this.$refs.drawerRef.toggle();
                        this.$emitter.emit('add-flash', {
                            type:    'success',
                            message: r.data.message,
                        });
                    })
                    .catch(err => {
                        if (err.response?.status === 422) {
                            const errs = err.response.data.errors ?? {};
                            this.errors.points = errs.points?.[0] ?? '';
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
