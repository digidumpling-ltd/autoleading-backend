<div class="mt-3.5 flex gap-2.5 max-xl:flex-wrap">
    <!-- Left column -->
    <div class="flex flex-1 flex-col gap-2 max-xl:flex-auto">

        <!-- General -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('custom_promotions::app.admin.wallet-rules.create.general')
            </p>

            <x-admin::form.control-group>
                <x-admin::form.control-group.label class="required">
                    @lang('custom_promotions::app.admin.wallet-rules.create.name')
                </x-admin::form.control-group.label>
                <x-admin::form.control-group.control
                    type="text"
                    name="name"
                    rules="required"
                    :value="old('name', $rule?->name)"
                    :label="trans('custom_promotions::app.admin.wallet-rules.create.name')"
                    :placeholder="trans('custom_promotions::app.admin.wallet-rules.create.name')"
                />
                <x-admin::form.control-group.error control-name="name" />
            </x-admin::form.control-group>

            <x-admin::form.control-group class="!mb-0">
                <x-admin::form.control-group.label>
                    @lang('custom_promotions::app.admin.wallet-rules.create.description')
                </x-admin::form.control-group.label>
                <x-admin::form.control-group.control
                    type="textarea"
                    name="description"
                    :value="old('description', $rule?->description)"
                    :label="trans('custom_promotions::app.admin.wallet-rules.create.description')"
                    :placeholder="trans('custom_promotions::app.admin.wallet-rules.create.description')"
                />
            </x-admin::form.control-group>
        </div>

        <!-- Conditions (Vue component owns the card header with title + condition-type) -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <v-promotion-rule-form
                :attribute-options='@json($attributeOptions ?? [])'
                :existing-conditions='@json(old("conditions", $rule?->conditions ?? []))'
                :existing-condition-type="{{ old('condition_type', $rule?->condition_type ?? 1) }}"
            ></v-promotion-rule-form>
        </div>

        <!-- Actions (Vue component — dynamic mode/value fields) -->
        <div class="box-shadow rounded bg-white p-4 dark:bg-gray-900">
            <p class="mb-4 text-base font-semibold text-gray-800 dark:text-white">
                @lang('custom_promotions::app.admin.wallet-rules.create.actions')
            </p>

            <v-wallet-rule-actions
                initial-action-type="{{ old('action_type', $rule?->action_type ?? '') }}"
                initial-reward-mode="{{ old('reward_mode', $rule?->reward_mode ?? '') }}"
                initial-reward-value="{{ old('reward_value', $rule?->reward_value ?? '') }}"
                :action-type-options='@json(trans("custom_promotions::app.admin.wallet-rules.create.action-types"))'
                :reward-mode-options='@json(trans("custom_promotions::app.admin.wallet-rules.create.reward-modes"))'
                label-action-type="{{ trans('custom_promotions::app.admin.wallet-rules.create.action-type') }}"
                label-reward-mode="{{ trans('custom_promotions::app.admin.wallet-rules.create.reward-mode') }}"
                label-reward-value="{{ trans('custom_promotions::app.admin.wallet-rules.create.reward-value') }}"
                label-reward-value-fixed="{{ trans('custom_promotions::app.admin.wallet-rules.create.reward-value-fixed') }}"
                label-reward-value-percent="{{ trans('custom_promotions::app.admin.wallet-rules.create.reward-value-percent') }}"
            ></v-wallet-rule-actions>
        </div>

    </div>

    <!-- Right column -->
    <div class="flex w-[360px] max-w-full flex-col gap-2 max-sm:w-full">

        <!-- Settings -->
        <x-admin::accordion>
            <x-slot:header>
                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                    @lang('custom_promotions::app.admin.wallet-rules.create.settings')
                </p>
            </x-slot>

            <x-slot:content>
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('custom_promotions::app.admin.wallet-rules.create.sort-order')
                    </x-admin::form.control-group.label>
                    <x-admin::form.control-group.control
                        type="text"
                        name="sort_order"
                        :value="old('sort_order', $rule?->sort_order)"
                        :label="trans('custom_promotions::app.admin.wallet-rules.create.sort-order')"
                        placeholder="0"
                    />
                </x-admin::form.control-group>

                <!-- Channels -->
                <div class="mb-2.5">
                    <x-admin::form.control-group.label class="required">
                        @lang('custom_promotions::app.admin.wallet-rules.create.channels')
                    </x-admin::form.control-group.label>

                    @php($checkedChannels = old('channels', $rule ? $rule->channels->pluck('id')->toArray() : []))

                    @foreach(core()->getAllChannels() as $channel)
                        <x-admin::form.control-group class="!mb-2 flex items-center gap-2.5">
                            <x-admin::form.control-group.control
                                type="checkbox"
                                :id="'channel_' . '_' . $channel->id"
                                name="channels[]"
                                rules="required"
                                :value="$channel->id"
                                :for="'channel_' . '_' . $channel->id"
                                :label="trans('custom_promotions::app.admin.wallet-rules.create.channels')"
                                :checked="in_array($channel->id, $checkedChannels)"
                            />
                            <label
                                class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                for="{{ 'channel__' . $channel->id }}"
                                v-pre
                            >
                                {{ core()->getChannelName($channel) }}
                            </label>
                        </x-admin::form.control-group>
                    @endforeach

                    <x-admin::form.control-group.error control-name="channels[]" />
                </div>

                <!-- Customer Groups -->
                <div class="mb-2.5">
                    <x-admin::form.control-group.label class="required">
                        @lang('custom_promotions::app.admin.wallet-rules.create.customer-groups')
                    </x-admin::form.control-group.label>

                    @php($checkedGroups = old('customer_groups', $rule ? $rule->customerGroups->pluck('id')->toArray() : []))

                    @foreach(app('Webkul\Customer\Repositories\CustomerGroupRepository')->all() as $group)
                        <x-admin::form.control-group class="!mb-2 flex items-center gap-2.5">
                            <x-admin::form.control-group.control
                                type="checkbox"
                                :id="'customer_group_' . '_' . $group->id"
                                name="customer_groups[]"
                                rules="required"
                                :value="$group->id"
                                :for="'customer_group_' . '_' . $group->id"
                                :label="trans('custom_promotions::app.admin.wallet-rules.create.customer-groups')"
                                :checked="in_array($group->id, $checkedGroups)"
                            />
                            <label
                                class="cursor-pointer text-xs font-medium text-gray-600 dark:text-gray-300"
                                for="{{ 'customer_group__' . $group->id }}"
                                v-pre
                            >
                                {{ $group->name }}
                            </label>
                        </x-admin::form.control-group>
                    @endforeach

                    <x-admin::form.control-group.error control-name="customer_groups[]" />
                </div>

                <!-- Status -->
                <x-admin::form.control-group class="!mb-0">
                    <x-admin::form.control-group.label>
                        @lang('custom_promotions::app.admin.wallet-rules.create.status')
                    </x-admin::form.control-group.label>
                    <x-admin::form.control-group.control
                        type="switch"
                        name="status"
                        value="1"
                        :label="trans('custom_promotions::app.admin.wallet-rules.create.status')"
                        :checked="(boolean) old('status', $rule?->status)"
                    />
                </x-admin::form.control-group>
            </x-slot>
        </x-admin::accordion>

        <!-- Marketing Time -->
        <x-admin::accordion>
            <x-slot:header>
                <p class="p-2.5 text-base font-semibold text-gray-800 dark:text-white">
                    @lang('custom_promotions::app.admin.wallet-rules.create.marketing-time')
                </p>
            </x-slot>

            <x-slot:content>
                <x-admin::form.control-group>
                    <x-admin::form.control-group.label>
                        @lang('custom_promotions::app.admin.wallet-rules.create.starts-from')
                    </x-admin::form.control-group.label>
                    <x-admin::form.control-group.control
                        type="date"
                        name="starts_from"
                        :value="old('starts_from', $rule?->starts_from)"
                        :label="trans('custom_promotions::app.admin.wallet-rules.create.starts-from')"
                    />
                    <x-admin::form.control-group.error control-name="starts_from" />
                </x-admin::form.control-group>

                <x-admin::form.control-group class="!mb-0">
                    <x-admin::form.control-group.label>
                        @lang('custom_promotions::app.admin.wallet-rules.create.ends-till')
                    </x-admin::form.control-group.label>
                    <x-admin::form.control-group.control
                        type="date"
                        name="ends_till"
                        :value="old('ends_till', $rule?->ends_till)"
                        :label="trans('custom_promotions::app.admin.wallet-rules.create.ends-till')"
                    />
                    <x-admin::form.control-group.error control-name="ends_till" />
                </x-admin::form.control-group>
            </x-slot>
        </x-admin::accordion>

    </div>
</div>

@pushOnce('scripts')
<script type="text/x-template" id="v-wallet-rule-actions-template">
    <div>
        <!-- Action Type -->
        <div class="mb-4">
            <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">
                @{{ labelActionType }}
            </label>
            <select
                name="action_type"
                v-model="actionType"
                class="custom-select w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
            >
                <option value="">-- @{{ labelActionType }} --</option>
                <option v-for="(label, key) in actionTypeOptions" :key="key" :value="key">
                    @{{ label }}
                </option>
            </select>
        </div>

        <!-- Reward Mode -->
        <div class="mb-4">
            <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">
                @{{ labelRewardMode }}
            </label>
            <select
                name="reward_mode"
                v-model="rewardMode"
                class="custom-select w-full rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
            >
                <option value="">-- @{{ labelRewardMode }} --</option>
                <option v-for="(label, key) in rewardModeOptions" :key="key" :value="key">
                    @{{ label }}
                </option>
            </select>
        </div>

        <!-- Reward Value — label changes based on mode -->
        <div class="mb-4" v-show="rewardMode !== ''">
            <label class="mb-1.5 flex items-center gap-1 text-xs font-medium text-gray-800 dark:text-white required">
                @{{ rewardMode === 'percentage' ? labelRewardValuePercent : labelRewardValueFixed }}
            </label>
            <input
                type="text"
                name="reward_value"
                v-model="rewardValue"
                placeholder="0"
                class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
            />
        </div>

        <!-- Always render reward_value so it submits even when mode not yet chosen -->
        <input v-show="rewardMode === ''" type="hidden" name="reward_value" :value="rewardValue" />
    </div>
</script>

<script>
    window.addEventListener('load', function () {
        app.component('v-wallet-rule-actions', {
            template: '#v-wallet-rule-actions-template',

            props: {
                initialActionType:      { type: String, default: '' },
                initialRewardMode:      { type: String, default: '' },
                initialRewardValue:     { type: [String, Number], default: '' },
                actionTypeOptions:      { type: Object, default: () => ({}) },
                rewardModeOptions:      { type: Object, default: () => ({}) },
                labelActionType:        { type: String, default: 'Action Type' },
                labelRewardMode:        { type: String, default: 'Reward Mode' },
                labelRewardValue:       { type: String, default: 'Reward Value' },
                labelRewardValueFixed:  { type: String, default: 'Fixed Amount' },
                labelRewardValuePercent:{ type: String, default: 'Percentage (%)' },
            },

            data() {
                return {
                    actionType: this.initialActionType,
                    rewardMode: this.initialRewardMode,
                    rewardValue: this.initialRewardValue,
                };
            },
        });
    });
</script>
@endPushOnce
