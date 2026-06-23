@pushOnce('scripts')
    <script type="text/x-template" id="v-promotion-condition-item-template">
        <div class="mt-4 flex justify-between gap-4">
            <div class="flex flex-1 gap-4 max-sm:flex-1 max-sm:flex-wrap">
                <!-- Attribute select — always visible -->
                <select
                    :name="'conditions[' + index + '][attribute]'"
                    :id="'conditions[' + index + '][attribute]'"
                    class="custom-select min:w-1/3 flex h-10 w-1/3 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                    v-model="condition.attribute"
                >
                    <option value="">{{ trans('custom_promotions::app.admin.wallet-rules.create.choose-condition') }}</option>
                    <option
                        v-for="attr in attributeOptions"
                        :key="attr.code"
                        :value="attr.code"
                    >
                        @{{ attr.label }}
                    </option>
                </select>

                <!-- Operator select — only after attribute chosen -->
                <select
                    :name="'conditions[' + index + '][operator]'"
                    class="custom-select inline-flex h-10 w-full max-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                    v-if="matchedAttribute"
                    v-model="condition.operator"
                >
                    <option
                        v-for="op in operatorOptions"
                        :key="op.operator"
                        :value="op.operator"
                    >
                        @{{ op.label }}
                    </option>
                </select>

                <!-- Value — only after attribute chosen, type-aware -->
                <div v-if="matchedAttribute">
                    <template v-if="matchedAttribute.type === 'boolean'">
                        <select
                            :name="'conditions[' + index + '][value]'"
                            class="custom-select inline-flex h-10 w-full min-w-[196px] items-center justify-between gap-x-1 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 max-sm:max-w-full max-sm:flex-auto"
                            v-model="condition.value"
                        >
                            <option value="1">{{ trans('custom_promotions::app.admin.wallet-rules.create.yes') }}</option>
                            <option value="0">{{ trans('custom_promotions::app.admin.wallet-rules.create.no') }}</option>
                        </select>
                    </template>

                    <template v-else-if="matchedAttribute.type === 'date'">
                        <input
                            type="date"
                            :name="'conditions[' + index + '][value]'"
                            class="flex min-h-[39px] w-full rounded-md border px-3 py-2 text-sm text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                            v-model="condition.value"
                        />
                    </template>

                    <template v-else>
                        <input
                            type="text"
                            :name="'conditions[' + index + '][value]'"
                            class="flex h-10 w-[196px] rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                            v-model="condition.value"
                            placeholder="0"
                        />
                    </template>
                </div>
            </div>

            <!-- Delete -->
            <span
                class="icon-delete max-w-9 max-h-9 cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-100 dark:hover:bg-gray-950 max-sm:place-self-center"
                @click="$emit('remove', index)"
            ></span>
        </div>
    </script>

    <script type="text/x-template" id="v-promotion-rule-form-template">
        <div>
            <!-- Card header: title left, condition-type select right -->
            <div class="mb-8 flex items-center justify-between gap-4">
                <p class="text-base font-semibold text-gray-800 dark:text-white">
                    {{ trans('custom_promotions::app.admin.wallet-rules.create.conditions') }}
                </p>

                <x-admin::form.control-group class="!mb-0">
                    <select
                        name="condition_type"
                        class="custom-select ltr:pr-10 rtl:pl-10 flex h-10 rounded-md border bg-white px-3 py-2.5 text-sm font-normal text-gray-600 transition-all hover:border-gray-400 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400"
                        v-model="conditionType"
                    >
                        <option value="1">{{ trans('custom_promotions::app.admin.wallet-rules.create.all') }}</option>
                        <option value="0">{{ trans('custom_promotions::app.admin.wallet-rules.create.any') }}</option>
                    </select>
                </x-admin::form.control-group>
            </div>

            <!-- Condition rows -->
            <v-promotion-condition-item
                v-for="(condition, idx) in conditions"
                :key="idx"
                :index="idx"
                :condition="condition"
                :attribute-options="attributeOptions"
                @remove="removeCondition"
            ></v-promotion-condition-item>

            <!-- Add condition button -->
            <div
                class="secondary-button mt-4 max-w-max"
                @click="addCondition"
            >
                {{ trans('custom_promotions::app.admin.wallet-rules.create.add-condition') }}
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-promotion-condition-item', {
            template: '#v-promotion-condition-item-template',

            props: ['index', 'condition', 'attributeOptions'],

            emits: ['remove'],

            computed: {
                matchedAttribute() {
                    if (! this.condition.attribute) {
                        return null;
                    }

                    return this.attributeOptions.find(a => a.code === this.condition.attribute) || null;
                },

                operatorOptions() {
                    if (! this.matchedAttribute) {
                        return [];
                    }

                    const numeric = [
                        { operator: '==', label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.==") }}' },
                        { operator: '!=', label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.!=") }}' },
                        { operator: '>=', label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.>=") }}' },
                        { operator: '<=', label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.<=") }}' },
                        { operator: '>',  label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.>") }}' },
                        { operator: '<',  label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.<") }}' },
                    ];

                    const boolOps = [
                        { operator: '==', label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.==") }}' },
                        { operator: '!=', label: '{{ trans("custom_promotions::app.admin.wallet-rules.create.operators.!=") }}' },
                    ];

                    return this.matchedAttribute.type === 'boolean' ? boolOps : numeric;
                },
            },
        });

        app.component('v-promotion-rule-form', {
            template: '#v-promotion-rule-form-template',

            props: {
                attributeOptions:       { type: Array, default: () => [] },
                existingConditions:     { type: Array, default: () => [] },
                existingConditionType:  { type: [Number, String], default: 1 },
            },

            data() {
                return {
                    conditions:    this.existingConditions.length ? this.existingConditions : [],
                    conditionType: this.existingConditionType,
                };
            },

            methods: {
                addCondition() {
                    this.conditions.push({ attribute: '', operator: '==', value: '' });
                },

                removeCondition(index) {
                    this.conditions.splice(index, 1);
                },
            },
        });
    </script>
@endPushOnce
