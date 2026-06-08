<x-admin::layouts>

    <x-slot:title>
        @lang('rewards::app.admin.rewards.wallet-topup.edit.title')
    </x-slot:title>

    <x-admin::form action="{{ route('admin.reward.wallet-topup.update', $rule->id) }}">
        <div class="flex items-center justify-between">
            <p class="py-3 text-lg font-bold text-gray-800 dark:text-white">
                @lang('rewards::app.admin.rewards.wallet-topup.edit.title')
            </p>

            <div class="flex gap-x-2.5 items-center">
                <a
                    href="{{ route('admin.reward.wallet-topup.index') }}"
                    class="transparent-button hover:bg-gray-200 dark:hover:bg-gray-800 dark:text-white"
                >
                    @lang('admin::app.catalog.attributes.create.back-btn')
                </a>

                <button type="submit" class="primary-button">
                    @lang('rewards::app.common.save-btn')
                </button>
            </div>
        </div>

        <div class="border-b px-4 py-2.5 dark:border-gray-800">

            <!-- Customer Group -->
            <x-admin::form.control-group class="mb-2.5">
                <x-admin::form.control-group.label>
                    @lang('rewards::app.admin.rewards.wallet-topup.create.customer-group')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    name="customer_group_id"
                    value="{{ old('customer_group_id', $rule->customer_group_id ?? '') }}"
                    :label="trans('rewards::app.admin.rewards.wallet-topup.create.customer-group')"
                >
                    <option value="">@lang('rewards::app.admin.rewards.wallet-topup.create.all-groups')</option>

                    @foreach ($customerGroups as $group)
                        <option
                            value="{{ $group->id }}"
                            {{ old('customer_group_id', $rule->customer_group_id) == $group->id ? 'selected' : '' }}
                        >
                            {{ $group->name }}
                        </option>
                    @endforeach
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="customer_group_id" />
            </x-admin::form.control-group>

            <!-- Trigger -->
            <x-admin::form.control-group class="mb-2.5">
                <x-admin::form.control-group.label class="required">
                    @lang('rewards::app.admin.rewards.wallet-topup.create.trigger')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    name="trigger"
                    rules="required"
                    value="{{ old('trigger', $rule->trigger) }}"
                    :label="trans('rewards::app.admin.rewards.wallet-topup.create.trigger')"
                >
                    <option value="">@lang('rewards::app.admin.rewards.wallet-topup.create.select-trigger')</option>
                    <option value="wallet_topup" {{ old('trigger', $rule->trigger) === 'wallet_topup' ? 'selected' : '' }}>
                        @lang('rewards::app.admin.rewards.wallet-topup.create.trigger-wallet-topup')
                    </option>
                    <option value="wallet_spend" {{ old('trigger', $rule->trigger) === 'wallet_spend' ? 'selected' : '' }}>
                        @lang('rewards::app.admin.rewards.wallet-topup.create.trigger-wallet-spend')
                    </option>
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="trigger" />
            </x-admin::form.control-group>

            <div class="flex gap-4 max-sm:flex-wrap">
                <!-- Min Amount -->
                <x-admin::form.control-group class="w-full">
                    <x-admin::form.control-group.label>
                        @lang('rewards::app.admin.rewards.wallet-topup.create.min-amount')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="min_amount"
                        value="{{ old('min_amount', $rule->min_amount) }}"
                        :label="trans('rewards::app.admin.rewards.wallet-topup.create.min-amount')"
                        :placeholder="trans('rewards::app.admin.rewards.wallet-topup.create.min-amount')"
                    />

                    <x-admin::form.control-group.error control-name="min_amount" />
                </x-admin::form.control-group>

                <!-- Max Amount -->
                <x-admin::form.control-group class="w-full">
                    <x-admin::form.control-group.label>
                        @lang('rewards::app.admin.rewards.wallet-topup.create.max-amount')
                    </x-admin::form.control-group.label>

                    <x-admin::form.control-group.control
                        type="text"
                        name="max_amount"
                        value="{{ old('max_amount', $rule->max_amount) }}"
                        :label="trans('rewards::app.admin.rewards.wallet-topup.create.max-amount')"
                        :placeholder="trans('rewards::app.admin.rewards.wallet-topup.create.max-amount')"
                    />

                    <x-admin::form.control-group.error control-name="max_amount" />
                </x-admin::form.control-group>
            </div>

            <!-- Mode + Value (Vue-reactive) -->
            <v-topup-rule-mode-value
                initial-mode="{{ old('mode', $rule->mode) }}"
                initial-value="{{ old('value', $rule->value) }}"
                label-fixed="{{ trans('rewards::app.admin.rewards.wallet-topup.create.value-fixed') }}"
                label-percent="{{ trans('rewards::app.admin.rewards.wallet-topup.create.value-percent') }}"
                label-mode="{{ trans('rewards::app.admin.rewards.wallet-topup.create.mode') }}"
                label-select-mode="{{ trans('rewards::app.admin.rewards.wallet-topup.create.select-mode') }}"
                label-mode-fixed="{{ trans('rewards::app.admin.rewards.wallet-topup.create.mode-fixed') }}"
                label-mode-percent="{{ trans('rewards::app.admin.rewards.wallet-topup.create.mode-percent') }}"
            ></v-topup-rule-mode-value>

            <!-- Priority -->
            <x-admin::form.control-group class="mb-2.5">
                <x-admin::form.control-group.label>
                    @lang('rewards::app.admin.rewards.wallet-topup.create.priority')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="text"
                    name="priority"
                    value="{{ old('priority', $rule->priority) }}"
                    :label="trans('rewards::app.admin.rewards.wallet-topup.create.priority')"
                    :placeholder="trans('rewards::app.admin.rewards.wallet-topup.create.priority')"
                />

                <x-admin::form.control-group.error control-name="priority" />
            </x-admin::form.control-group>

            <!-- Status -->
            <x-admin::form.control-group class="mb-2.5">
                <x-admin::form.control-group.label class="required">
                    @lang('rewards::app.admin.rewards.wallet-topup.create.status')
                </x-admin::form.control-group.label>

                <x-admin::form.control-group.control
                    type="select"
                    name="status"
                    rules="required"
                    value="{{ old('status', $rule->status ? '1' : '0') }}"
                    :label="trans('rewards::app.admin.rewards.wallet-topup.create.status')"
                >
                    <option value="">@lang('rewards::app.admin.rewards.wallet-topup.create.select-status')</option>
                    <option value="1" {{ old('status', $rule->status ? '1' : '0') == '1' ? 'selected' : '' }}>
                        @lang('rewards::app.admin.rewards.wallet-topup.create.active')
                    </option>
                    <option value="0" {{ old('status', $rule->status ? '1' : '0') == '0' ? 'selected' : '' }}>
                        @lang('rewards::app.admin.rewards.wallet-topup.create.inactive')
                    </option>
                </x-admin::form.control-group.control>

                <x-admin::form.control-group.error control-name="status" />
            </x-admin::form.control-group>

        </div>
    </x-admin::form>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-topup-rule-mode-value-template"
        >
            <div>
                <!-- Mode -->
                <div class="mb-4">
                    <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">
                        @{{ labelMode }}
                    </label>

                    <v-field
                        v-slot="{ field, errors }"
                        name="mode"
                        :value="initialMode"
                        rules="required"
                        :label="labelMode"
                    >
                        <select
                            name="mode"
                            v-bind="field"
                            :class="[errors.length ? 'border border-red-500' : '']"
                            class="custom-select w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                            @change="mode = $event.target.value"
                        >
                            <option value="">@{{ labelSelectMode }}</option>
                            <option value="fixed">@{{ labelModeFixed }}</option>
                            <option value="percent">@{{ labelModePercent }}</option>
                        </select>
                    </v-field>

                    <v-error-message name="mode" v-slot="{ message }">
                        <p class="mt-1 text-xs italic text-red-600" v-text="message"></p>
                    </v-error-message>
                </div>

                <!-- Value — shown when mode is selected; label changes with mode -->
                <div class="mb-4" v-show="mode !== ''">
                    <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">
                        @{{ mode === 'percent' ? labelPercent : labelFixed }}
                    </label>

                    <v-field
                        v-slot="{ field, errors }"
                        name="value"
                        :value="initialValue"
                        rules="required|decimal:4|min_value:0"
                        :label="mode === 'percent' ? labelPercent : labelFixed"
                    >
                        <input
                            type="text"
                            name="value"
                            v-bind="field"
                            :class="[errors.length ? 'border !border-red-600 hover:border-red-600' : '']"
                            class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                        />
                    </v-field>

                    <v-error-message name="value" v-slot="{ message }">
                        <p class="mt-1 text-xs italic text-red-600" v-text="message"></p>
                    </v-error-message>
                </div>
            </div>
        </script>

        <script>
            window.addEventListener('load', function () {
                app.component('v-topup-rule-mode-value', {
                    template: '#v-topup-rule-mode-value-template',

                    props: {
                        initialMode:      { type: String, default: '' },
                        initialValue:     { type: String, default: '' },
                        labelFixed:       { type: String, default: '' },
                        labelPercent:     { type: String, default: '' },
                        labelMode:        { type: String, default: '' },
                        labelSelectMode:  { type: String, default: '' },
                        labelModeFixed:   { type: String, default: '' },
                        labelModePercent: { type: String, default: '' },
                    },

                    data() {
                        return {
                            mode: this.initialMode,
                        };
                    },
                });
            });
        </script>
    @endPushOnce

</x-admin::layouts>
