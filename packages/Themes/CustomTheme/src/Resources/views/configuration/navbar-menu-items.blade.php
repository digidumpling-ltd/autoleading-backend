@php
    $currentChannel = core()->getRequestedChannel();
    $currentLocale  = core()->getRequestedLocale();
    $storedValue    = system_config()->getConfigData($field->getNameKey(), $currentChannel->code, $currentLocale->code) ?? '[]';
    $fieldName      = $field->getNameField();
@endphp

<input
    type="hidden"
    name="keys[]"
    value="{{ json_encode($child) }}"
/>

<div class="mb-4 last:!mb-0">
    <x-admin::form.control-group>
        <x-admin::form.control-group.label>
            @lang('custom-theme::app.configuration.general.design.navbar.menu-items')
        </x-admin::form.control-group.label>

        <v-navbar-menu-items
            field-name="{{ $fieldName }}"
            :initial-items="{{ $storedValue ?: '[]' }}"
        ></v-navbar-menu-items>
    </x-admin::form.control-group>
</div>

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-navbar-menu-items-template"
    >
        <div>
            <input
                type="hidden"
                :name="fieldName"
                :value="JSON.stringify(items)"
            />

            <!-- Column headers -->
            <div
                class="mb-1.5 grid gap-2.5 text-xs font-medium text-gray-500 dark:text-gray-400"
                style="grid-template-columns: 1fr 1fr 80px 36px"
                v-if="items.length"
            >
                <span>@lang('custom-theme::app.configuration.general.design.navbar.menu-items-item')</span>
                <span>URL</span>
                <span>@lang('custom-theme::app.configuration.general.design.navbar.menu-items-order')</span>
                <span></span>
            </div>

            <!-- Item rows -->
            <div
                v-for="(item, index) in items"
                :key="index"
                class="mb-2 grid items-start gap-2.5"
                style="grid-template-columns: 1fr 1fr 80px 36px"
            >
                <!-- Label -->
                <input
                    type="text"
                    v-model="item.label"
                    placeholder="@lang('custom-theme::app.configuration.general.design.navbar.menu-items-item')"
                    class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                />

                <!-- URL -->
                <input
                    type="text"
                    v-model="item.url"
                    placeholder="@lang('custom-theme::app.configuration.general.design.navbar.menu-items-url')"
                    class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                />

                <!-- Order -->
                <input
                    type="number"
                    v-model.number="item.order"
                    min="0"
                    class="w-full rounded-md border px-3 py-2.5 text-sm text-gray-600 transition-all hover:border-gray-400 focus:border-gray-400 focus:outline-none dark:border-gray-800 dark:bg-gray-900 dark:text-gray-300 dark:hover:border-gray-400 dark:focus:border-gray-400"
                />

                <!-- Delete -->
                <span
                    class="icon-delete cursor-pointer rounded-md p-1.5 text-2xl transition-all hover:bg-gray-200 dark:hover:bg-gray-800"
                    @click="removeItem(index)"
                ></span>
            </div>

            <!-- Add button -->
            <div
                class="secondary-button mt-1 flex w-max cursor-pointer items-center gap-1.5 text-sm"
                @click="addItem"
            >
                <span class="icon-add text-base"></span>
                @lang('custom-theme::app.configuration.general.design.navbar.menu-items-add')
            </div>
        </div>
    </script>

    <script type="module">
        app.component('v-navbar-menu-items', {
            template: '#v-navbar-menu-items-template',

            props: {
                fieldName: {
                    type: String,
                    required: true,
                },
                initialItems: {
                    type: Array,
                    default: () => [],
                },
            },

            data() {
                return {
                    items: this.initialItems.map(item => ({ ...item })),
                };
            },

            methods: {
                addItem() {
                    this.items.push({ label: '', url: '/', order: this.items.length });
                },

                removeItem(index) {
                    this.items.splice(index, 1);
                },
            },
        });
    </script>
@endPushOnce
