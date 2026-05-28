<x-admin::layouts>
    <x-slot:title>
        @lang('bagisto-membership::app.admin.tiers.title')
    </x-slot>

    <div class="mb-5 flex items-center justify-between gap-4">
        <p class="text-xl font-bold text-gray-800 dark:text-white">
            @lang('bagisto-membership::app.admin.tiers.title')
        </p>
    </div>

    <v-membership-tiers
        :initial-rules="{{ json_encode($tierRules->map(fn ($r) => ['id' => $r->id, 'min_balance' => $r->min_balance, 'max_balance' => $r->max_balance, 'customer_group_id' => $r->customer_group_id, 'sort_order' => $r->sort_order])->values()) }}"
        :customer-groups="{{ json_encode($customerGroups->map(fn ($g) => ['id' => $g->id, 'name' => $g->name, 'code' => $g->code])->values()) }}"
        store-url="{{ route('admin.membership.tiers.store') }}"
    ></v-membership-tiers>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-membership-tiers-template"
        >
            <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
                <!-- Header -->
                <div class="flex items-center justify-between gap-5 py-2">
                    <div class="flex flex-col gap-1">
                        <p class="text-base font-semibold text-gray-800 dark:text-white">
                            @lang('bagisto-membership::app.admin.tiers.subtitle')
                        </p>

                        <p class="text-xs font-medium text-gray-500 dark:text-gray-300">
                            @lang('bagisto-membership::app.admin.tiers.description')
                        </p>
                    </div>

                    <div class="secondary-button" @click="openDrawer">
                        @lang('bagisto-membership::app.admin.tiers.add-tier')
                    </div>
                </div>

                <!-- Tier chips summary -->
                <div class="mt-2 overflow-x-auto">
                    <template v-if="rules.length">
                        <div class="flex flex-wrap gap-2.5">
                            <div
                                v-for="(rule, index) in rules"
                                :key="rule.id || index"
                                class="flex items-center gap-1"
                            >
                                <p class="flex items-center rounded bg-gray-600 px-2.5 py-1 font-semibold text-white">
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
                            @lang('bagisto-membership::app.admin.tiers.no-rules')
                        </p>
                    </template>
                </div>

                <!-- Drawer -->
                <x-admin::drawer ref="drawerRef">
                    <x-slot:header>
                        <div class="flex items-center justify-between">
                            <p class="my-2.5 text-xl font-medium dark:text-white">
                                @lang('bagisto-membership::app.admin.tiers.drawer-title')
                            </p>

                            <div class="flex items-center gap-4 ltr:mr-11 rtl:ml-11">
                                <div
                                    class="w-fit cursor-pointer font-medium text-blue-600 dark:text-white"
                                    @click="addRow"
                                >
                                    @lang('bagisto-membership::app.admin.tiers.add-tier')
                                </div>

                                <button
                                    type="button"
                                    class="primary-button"
                                    :disabled="saving"
                                    @click="save"
                                >
                                    @lang('bagisto-membership::app.admin.tiers.save')
                                </button>
                            </div>
                        </div>
                    </x-slot:header>

                    <x-slot:content>
                        <!-- Validation errors -->
                        <template v-if="errors.length">
                            <div class="mb-3 rounded border border-red-300 bg-red-50 p-3 text-sm text-red-700 dark:bg-red-900/20">
                                <p v-for="error in errors" :key="error" v-text="error"></p>
                            </div>
                        </template>

                        <template v-if="draft.length">
                            <div
                                v-for="(row, index) in draft"
                                :key="index"
                                class="mx-2.5 mb-4 flex items-end gap-2.5"
                            >
                                <!-- Priority -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('bagisto-membership::app.admin.tiers.col-sort-order')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="number"
                                        ::name="'tiers[' + index + '][sort_order]'"
                                        v-model="row.sort_order"
                                        min="0"
                                        placeholder="0"
                                    />
                                </x-admin::form.control-group>

                                <!-- Min Balance -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('bagisto-membership::app.admin.tiers.col-min-balance')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="number"
                                        ::name="'tiers[' + index + '][min_balance]'"
                                        v-model="row.min_balance"
                                        min="0"
                                        step="0.01"
                                        placeholder="0.00"
                                    />
                                </x-admin::form.control-group>

                                <!-- Max Balance -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('bagisto-membership::app.admin.tiers.col-max-balance')
                                    </x-admin::form.control-group.label>

                                    <x-admin::form.control-group.control
                                        type="number"
                                        ::name="'tiers[' + index + '][max_balance]'"
                                        v-model="row.max_balance"
                                        min="0"
                                        step="0.01"
                                        placeholder="∞"
                                    />
                                </x-admin::form.control-group>

                                <!-- Customer Group -->
                                <x-admin::form.control-group class="!mb-0 w-full">
                                    <x-admin::form.control-group.label>
                                        @lang('bagisto-membership::app.admin.tiers.col-customer-group')
                                    </x-admin::form.control-group.label>

                                    <select
                                        v-model="row.customer_group_id"
                                        ::name="'tiers[' + index + '][customer_group_id]'"
                                        class="custom-select w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                                    >
                                        <option value="">
                                            @lang('bagisto-membership::app.admin.tiers.select-group')
                                        </option>
                                        <option
                                            v-for="group in customerGroups"
                                            :key="group.id"
                                            :value="group.id"
                                        >
                                            @{{ group.name }}
                                        </option>
                                    </select>
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
                                @lang('bagisto-membership::app.admin.tiers.no-rules')
                            </p>
                        </template>
                    </x-slot:content>
                </x-admin::drawer>
            </div>
        </script>

        <script type="module">
            app.component('v-membership-tiers', {
                template: '#v-membership-tiers-template',

                props: {
                    initialRules:   { type: Array,  default: () => [] },
                    customerGroups: { type: Array,  default: () => [] },
                    storeUrl:       { type: String, required: true },
                },

                data() {
                    return {
                        rules:  this.initialRules.map(r => ({ ...r })),
                        draft:  [],
                        errors: [],
                        saving: false,
                    };
                },

                methods: {
                    groupName(groupId) {
                        return this.customerGroups.find(g => g.id == groupId)?.name ?? groupId;
                    },

                    chipLabel(rule) {
                        const group = this.groupName(rule.customer_group_id);
                        const max   = rule.max_balance !== null && rule.max_balance !== '' ? rule.max_balance : '∞';
                        return `${group}: ${rule.min_balance}–${max}`;
                    },

                    openDrawer() {
                        this.draft  = this.rules.map(r => ({ ...r }));
                        this.errors = [];
                        this.$refs.drawerRef.toggle();
                    },

                    addRow() {
                        this.draft.push({
                            sort_order:        this.draft.length,
                            min_balance:       '',
                            max_balance:       '',
                            customer_group_id: '',
                        });
                    },

                    removeRule(index) {
                        this.$emitter.emit('open-confirm-modal', {
                            agree: () => {
                                this.rules.splice(index, 1);
                                this.saveQuiet();
                            },
                        });
                    },

                    save() {
                        this.saving = true;
                        this.errors = [];

                        this.$axios.post(this.storeUrl, { rules: this.draft })
                            .then(r => {
                                this.rules = (r.data.data || []).map(row => ({ ...row }));
                                this.$refs.drawerRef.toggle();
                                this.$emitter.emit('add-flash', { type: 'success', message: r.data.message });
                            })
                            .catch(err => {
                                if (err.response?.status === 422) {
                                    this.errors = err.response.data.errors || [];
                                }
                            })
                            .finally(() => { this.saving = false; });
                    },

                    saveQuiet() {
                        this.$axios.post(this.storeUrl, { rules: this.rules }).catch(() => {});
                    },
                },
            });
        </script>
    @endpushOnce
</x-admin::layouts>
