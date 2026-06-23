@php
    $calendarAvailability = app(\Webkul\BookingProduct\Helpers\Booking::class)->getCalendarAvailability($bookingProduct);
    $waNumber     = preg_replace('/\D/', '', core()->getConfigData('catalog.products.whatsapp.number') ?? '');
    $waMessage    = urlencode(
        trans('custom-theme::app.products.view.type.booking.rental.whatsapp-message', [
            'name' => $product->name,
            'url'  => url()->current(),
        ])
    );
    $rentalMaxDays = (int) (core()->getConfigData('catalog.products.rental.max_days') ?? 0);
    $dayPricingRules = $day_pricing_rules ?? [];
@endphp

@push('scripts')
    <script>
        window.__rentalMaxDays = {{ $rentalMaxDays }};
        console.log('[CustomTheme] rentalMaxDays =', window.__rentalMaxDays);
    </script>
@endpush

<v-rental-slots
    :booking-product="{{ $bookingProduct }}"
    :availability="{{ json_encode($calendarAvailability) }}"
    :base-price="{{ $product->getTypeInstance()->getMinimalPrice() ?? 0 }}"
    :base-regular-price="{{ $product->getTypeInstance()->getRegularMinimalPrice() ?? 0 }}"
    :day-pricing-rules="{{ json_encode($dayPricingRules) }}"
></v-rental-slots>

@include('custom_promotions::shop.components.rental-promo-card')

@pushOnce('scripts')
    <script
        type="text/x-template"
        id="v-rental-slots-template"
    >
        <div class="grid grid-cols-1 gap-2.5">
            <template v-if="renting_type == 'daily_hourly'">
                <x-shop::form.control-group.label class="w-full required">
                    @lang('shop::app.products.view.type.booking.rental.choose-rent-option')
                </x-shop::form.control-group.label>

                <div class="mb-3 grid grid-cols-2 gap-2.5">
                    <!-- Daily Radio Button -->
                    <span class="flex gap-x-4">
                        <input
                            type="radio"
                            class="hidden peer"
                            id="booking[daily]"
                            name="booking[renting_type]"
                            value="daily"
                            v-model="sub_renting_type"
                        >

                        <label
                            class="text-2xl icon-radio-unselect peer-checked:icon-radio-select text-navyBlue"
                            for="booking[daily]"
                        >
                        </label>

                        <label
                            class="cursor-pointer text-[#6E6E6E]"
                            for="booking[daily]"
                        >
                            @lang('shop::app.products.view.type.booking.rental.daily-basis')
                        </label>
                    </span>

                    <!-- Hourly Radio Button -->
                    <span class="flex gap-x-4">
                        <input
                            type="radio"
                            class="hidden peer"
                            id="booking[hourly]"
                            name="booking[renting_type]"
                            value="hourly"
                            v-model="sub_renting_type"
                        >

                        <label
                            class="text-2xl icon-radio-unselect peer-checked:icon-radio-select text-navyBlue"
                            for="booking[hourly]"
                        >
                        </label>

                        <label
                            class="cursor-pointer text-[#6E6E6E]"
                            for="booking[hourly]"
                        >
                            @lang('shop::app.products.view.type.booking.rental.hourly-basis')
                        </label>
                    </span>
                </div>
            </template>

            <div class="flex flex-col gap-2.5" v-if="renting_type != 'daily' && sub_renting_type == 'hourly'">
                <div  class="grid gap-1.5">
                    <label class="required">
                        @lang('shop::app.products.view.type.booking.rental.select-slot')
                    </label>

                    <div class="flex gap-2.5">
                        <!-- Select Slot Date -->
                        <x-shop::form.control-group class="!mb-0 w-full">
                            <x-shop::form.control-group.label class="hidden">
                                @lang('shop::app.products.view.type.booking.rental.select-date')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="date"
                                name="booking[date]"
                                class="max-sm!px-2 max-sm!text-xs"
                                rules="required"
                                :label="trans('shop::app.products.view.type.booking.rental.select-date')"
                                :placeholder="trans('shop::app.products.view.type.booking.rental.select-date')"
                                ::min-date="minDate"
                                ::max-date="maxDate"
                                ::disable="disabledDates"
                                @change="dateSelected($event)"
                            />

                            <x-shop::form.control-group.error control-name="booking[date]" />
                        </x-shop::form.control-group>

                        <!-- Select Slot -->
                        <x-shop::form.control-group class="!mb-0 w-full">
                            <x-shop::form.control-group.label class="hidden">
                                @lang('shop::app.products.view.type.booking.rental.select-slot')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="select"
                                class="!mb-1"
                                name="booking[slot]"
                                rules="required"
                                v-model="selected_slot"
                                :label="trans('shop::app.products.view.type.booking.rental.select-date')"
                                :placeholder="trans('shop::app.products.view.type.booking.rental.select-date')"
                            >
                                <option value="">
                                    @lang('shop::app.products.view.type.booking.rental.select-slot')
                                </option>

                                <option v-if="! slots?.length">
                                    @lang('shop::app.products.view.type.booking.rental.no-slots-available')
                                </option>

                                <option
                                    v-for="(slot, index) in slots"
                                    :value="index"
                                    v-text="slot.time"
                                >
                                </option>
                            </x-shop::form.control-group.control>

                            <x-shop::form.control-group.error control-name="booking[slot]" />
                        </x-shop::form.control-group>
                    </div>
                </div>

                <div
                    class="grid gap-1.5"
                    v-if="parseInt(slots[selected_slot] && slots[selected_slot]?.slots?.length)"
                >
                    <label class="required">
                        @lang('shop::app.products.view.type.booking.rental.select-rent-time')
                    </label>

                    <div class="flex gap-2.5">
                        <!-- Select Time Slot From -->
                        <x-shop::form.control-group class="!mb-0 w-full">
                            <x-shop::form.control-group.label class="hidden">
                                @lang('shop::app.products.view.type.booking.rental.select-date')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="select"
                                name="booking[slot][from]"
                                rules="required"
                                v-model="booking.slot_from"
                                :label="trans('shop::app.products.view.type.booking.rental.select-date')"
                                :placeholder="trans('shop::app.products.view.type.booking.rental.select-date')"
                            >
                                <option value="">
                                    @lang('shop::app.products.view.type.booking.rental.select-time-slot')
                                </option>

                                <option
                                    v-for="slot in slots[selected_slot]?.slots"
                                    :value="slot.from_timestamp"
                                    v-text="slot.from"
                                >
                                </option>
                            </x-shop::form.control-group.control>

                            <x-shop::form.control-group.error control-name="booking[slot][from]" />
                        </x-shop::form.control-group>

                        <!-- Select Time Slot To -->
                        <x-shop::form.control-group class="!mb-0 w-full">
                            <x-shop::form.control-group.label class="hidden">
                                @lang('shop::app.products.view.type.booking.rental.slot')
                            </x-shop::form.control-group.label>

                            <x-shop::form.control-group.control
                                type="select"
                                name="booking[slot][to]"
                                rules="required"
                                v-model="booking.slot_to"
                                :label="trans('shop::app.products.view.type.booking.rental.slot')"
                                :placeholder="trans('shop::app.products.view.type.booking.rental.slot')"
                            >
                                <option value="">
                                    @lang('shop::app.products.view.type.booking.rental.select-time-slot')
                                </option>

                                <option
                                    v-for="slot in availableToSlots"
                                    :value="slot?.to_timestamp"
                                    v-text="slot.to"
                                >
                                </option>
                            </x-shop::form.control-group.control>

                            <x-shop::form.control-group.error control-name="booking[slot][to]" />
                        </x-shop::form.control-group>
                    </div>
                </div>
            </div>

            <div v-else>
                <label class="required">
                    @lang('shop::app.products.view.type.booking.rental.select-date')
                </label>

                <div class="flex gap-2.5">
                    <!-- Select Date From -->
                    <x-shop::form.control-group class="!mb-0 w-full">
                        <x-shop::form.control-group.label class="hidden">
                            @lang('shop::app.products.view.type.booking.rental.from')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="date"
                            name="booking[date_from]"
                            rules="required"
                            v-model="booking.date_from"
                            :label="trans('shop::app.products.view.type.booking.rental.from')"
                            :placeholder="trans('shop::app.products.view.type.booking.rental.from')"
                            ::min-date="minDate"
                            ::max-date="maxDate"
                            ::disable="disabledDates"
                            @change="dateSelected($event)"
                        />

                        <x-shop::form.control-group.error control-name="booking[date_from]" />
                    </x-shop::form.control-group>

                    <!-- Select Date To -->
                    <x-shop::form.control-group class="!mb-0 w-full">
                        <x-shop::form.control-group.label class="hidden">
                            @lang('shop::app.products.view.type.booking.rental.to')
                        </x-shop::form.control-group.label>

                        <x-shop::form.control-group.control
                            type="date"
                            name="booking[date_to]"
                            ::rules="'required|after:' + booking.date_from + (maxRentalDays ? '|within_max_days:' + booking.date_from + ',' + maxRentalDays : '')"
                            v-model="booking.date_to"
                            :label="trans('shop::app.products.view.type.booking.rental.to')"
                            :placeholder="trans('shop::app.products.view.type.booking.rental.to')"
                            ::min-date="booking.date_from || minDate"
                            ::max-date="maxDateTo"
                            ::disable="disabledDates"
                            @change="dateSelected($event)"
                        />

                        <x-shop::form.control-group.error control-name="booking[date_to]" />
                    </x-shop::form.control-group>
                </div>
            </div>

            <!-- Pricing Tiers + Rental Summary -->
            <div class="mt-3 rounded-lg border border-zinc-200 bg-zinc-50 p-4 text-sm">

                <!-- Sliding Day Pricing Tiers -->
                <template v-if="dayPricingRules && dayPricingRules.length && (renting_type === 'daily' || (renting_type === 'daily_hourly' && sub_renting_type === 'daily'))">
                    <div
                        class="flex cursor-pointer select-none items-center justify-between"
                        @click="tiersOpen = !tiersOpen"
                    >
                        <p class="font-semibold text-zinc-700">
                            @lang('custom-theme::app.products.view.type.booking.rental.pricing-tiers')
                        </p>

                        <span :class="tiersOpen ? 'icon-arrow-up' : 'icon-arrow-down'" class="text-2xl text-zinc-500"></span>
                    </div>

                    <div v-show="tiersOpen" class="mt-2 divide-y divide-zinc-200">
                        <div
                            v-for="(rule, index) in dayPricingRules"
                            :key="index"
                            class="flex items-center justify-between py-1.5"
                            :class="{ 'font-medium text-zinc-900': isActiveRule(rule) }"
                        >
                            <span class="text-zinc-600" v-text="tierLabel(rule)"></span>
                            <span v-text="$shop.formatPrice(tierRate(rule))"></span>
                        </div>
                    </div>

                    <div class="mt-3 border-t border-zinc-200"></div>
                </template>

                <p class="mb-3 font-semibold" :class="{ 'mt-3': dayPricingRules && dayPricingRules.length && (renting_type === 'daily' || (renting_type === 'daily_hourly' && sub_renting_type === 'daily')) }">
                    @lang('shop::app.products.view.type.booking.rental.summary-title')
                </p>

                <template v-if="hasSelection">
                    <div v-if="Number(basePrice) > 0" class="flex items-center justify-between py-1">
                        <span class="text-zinc-600">
                            @lang('shop::app.products.view.type.booking.rental.base-rental-fee')
                        </span>

                        <span class="flex items-center gap-2">
                            <span
                                v-if="hasBaseDiscount"
                                class="text-zinc-400 line-through"
                                v-text="formattedBaseRegularPrice"
                            >
                            </span>

                            <span
                                class="font-medium"
                                v-text="formattedBasePrice"
                            >
                            </span>
                        </span>
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <span
                            class="text-zinc-600"
                            v-text="rateLineLabel"
                        >
                        </span>

                        <span
                            class="font-medium"
                            v-text="formattedRateTotal"
                        >
                        </span>
                    </div>

                    <div class="mt-3 flex items-center justify-between border-t border-zinc-200 pt-3">
                        <span class="font-semibold">
                            @lang('shop::app.products.view.type.booking.rental.total')
                        </span>

                        <span
                            class="text-base font-semibold"
                            v-text="formattedGrandTotal"
                        >
                        </span>
                    </div>
                </template>

                <template v-else>
                    <p class="text-xs text-zinc-500">
                        @lang('shop::app.products.view.type.booking.rental.select-dates-hint')
                    </p>
                </template>
            </div>

            <!-- Rental promo card -->
            <v-rental-promo-card
                :date-from="booking.date_from"
                :date-to="booking.date_to"
            />

            <!-- WhatsApp Enquiry Button -->
            @if ($waNumber)
                <a
                    href="https://wa.me/{{ $waNumber }}?text={{ $waMessage }}"
                    target="_blank"
                    rel="noopener"
                    class="flex w-full items-center justify-center gap-2 rounded-xl bg-[#25D366] px-4 py-3 text-base font-medium text-white transition hover:bg-[#1ebe57]"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 fill-current" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/>
                        <path d="M12 0C5.373 0 0 5.373 0 12c0 2.127.558 4.126 1.532 5.855L.057 23.998l6.305-1.654A11.954 11.954 0 0012 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 21.818a9.823 9.823 0 01-5.012-1.376l-.36-.214-3.733.979 1-3.642-.235-.374A9.818 9.818 0 012.182 12C2.182 6.57 6.57 2.182 12 2.182S21.818 6.57 21.818 12 17.43 21.818 12 21.818z"/>
                    </svg>

                    @lang('custom-theme::app.products.view.type.booking.rental.whatsapp-enquire')
                </a>
            @endif
        </div>
    </script>

    <script type="module">
        defineRule('after', (value, [target]) => {
            if (! value || ! target) {
                return false;
            }

            return new Date(value) >= new Date(target);
        });

        app.component('v-rental-slots', {
            template: '#v-rental-slots-template',

            props: ['bookingProduct', 'availability', 'basePrice', 'baseRegularPrice', 'dayPricingRules'],

            data() {
                return {
                    tiersOpen: true,

                    renting_type: "{{ $bookingProduct->rental_slot?->renting_type ?? 'daily' }}",

                    sub_renting_type: 'hourly',

                    slots: [],

                    selected_slot: '',

                    booking: {
                        date_from: '',

                        date_to: '',

                        slot_from: '',

                        slot_to: '',
                    },
                }
            },

            computed: {
                minDate() {
                    const today = new Date();

                    const availableFrom = this.availability?.available_from
                        ? new Date(this.availability.available_from + 'T00:00:00')
                        : null;

                    const effective = availableFrom && availableFrom > today
                        ? availableFrom
                        : today;

                    return this.formatDate(effective);
                },

                maxDate() {
                    if (this.availability?.available_every_week) {
                        return '';
                    }

                    return this.availability?.available_to ?? '';
                },

                disabledDates() {
                    const disabledDates = this.availability?.disabled_dates ?? [];

                    const predicates = [];

                    /**
                     * The `valid_weekdays` array reflects the days that have
                     * *hourly* slot configuration. For a daily rental, every
                     * day within the availability window should be selectable
                     * regardless of the hourly schedule, so we only apply the
                     * weekday filter when the user is actually booking an
                     * hourly slot.
                     */
                    const activeType = this.renting_type === 'daily_hourly'
                        ? this.sub_renting_type
                        : this.renting_type;

                    if (activeType === 'hourly') {
                        const validWeekdays = this.availability?.valid_weekdays ?? [0, 1, 2, 3, 4, 5, 6];

                        if (validWeekdays.length < 7) {
                            predicates.push((date) => ! validWeekdays.includes(date.getDay()));
                        }
                    }

                    if (disabledDates.length) {
                        predicates.push(...disabledDates);
                    }

                    return predicates;
                },

                activeRentingType() {
                    return this.renting_type === 'daily_hourly'
                        ? this.sub_renting_type
                        : this.renting_type;
                },

                availableToSlots() {
                    const subSlots = this.slots[this.selected_slot]?.slots ?? [];

                    const from = Number(this.booking.slot_from);

                    if (! from) {
                        return subSlots;
                    }

                    return subSlots.filter(slot => Number(slot.to_timestamp) > from);
                },

                durationDays() {
                    if (! this.booking.date_from || ! this.booking.date_to) {
                        return 0;
                    }

                    const from = new Date(this.booking.date_from + 'T00:00:00');
                    const to = new Date(this.booking.date_to + 'T00:00:00');
                    const diff = Math.round((to - from) / (1000 * 60 * 60 * 24)) + 1;

                    return Math.max(1, diff);
                },

                durationHours() {
                    if (! this.booking.slot_from || ! this.booking.slot_to) {
                        return 0;
                    }

                    const diff = Number(this.booking.slot_to) - Number(this.booking.slot_from);

                    return Math.max(0, Math.round(diff / 3600));
                },

                duration() {
                    return this.activeRentingType === 'hourly'
                        ? this.durationHours
                        : this.durationDays;
                },

                effectiveDailyRate() {
                    const slot = this.bookingProduct?.rental_slot ?? {};
                    const dailyPrice = Number(slot.daily_price ?? 0);
                    const days = this.durationDays;
                    const rules = this.dayPricingRules ?? [];

                    if (! days || ! rules.length) {
                        return dailyPrice;
                    }

                    const match = rules.find(r => {
                        const min = Number(r.min_days);
                        const max = r.max_days !== null && r.max_days !== undefined && r.max_days !== ''
                            ? Number(r.max_days)
                            : Infinity;

                        return days >= min && days <= max;
                    });

                    if (! match) {
                        return dailyPrice;
                    }

                    if (match.discount_type === 'fixed') {
                        return Math.max(0, dailyPrice - Number(match.discount_value));
                    }

                    return dailyPrice * (1 - Number(match.discount_value) / 100);
                },

                rateUnit() {
                    const slot = this.bookingProduct?.rental_slot ?? {};

                    return this.activeRentingType === 'hourly'
                        ? Number(slot.hourly_price ?? 0)
                        : this.effectiveDailyRate;
                },

                rateTotal() {
                    return this.rateUnit * this.duration;
                },

                grandTotal() {
                    return Number(this.basePrice ?? 0) + this.rateTotal;
                },

                hasSelection() {
                    return this.duration > 0;
                },

                rateLineLabel() {
                    const durationWord = this.activeRentingType === 'hourly'
                        ? (this.duration === 1
                            ? "@lang('shop::app.products.view.type.booking.rental.hour')"
                            : "@lang('shop::app.products.view.type.booking.rental.hours')")
                        : (this.duration === 1
                            ? "@lang('shop::app.products.view.type.booking.rental.day')"
                            : "@lang('shop::app.products.view.type.booking.rental.days')");

                    const template = this.activeRentingType === 'hourly'
                        ? "@lang('shop::app.products.view.type.booking.rental.hourly-rate-line')"
                        : "@lang('shop::app.products.view.type.booking.rental.daily-rate-line')";

                    return template
                        .replace(':count', `${this.duration} ${durationWord}`)
                        .replace(':rate', this.$shop.formatPrice(this.rateUnit));
                },

                formattedBasePrice() {
                    return this.$shop.formatPrice(Number(this.basePrice ?? 0));
                },

                hasBaseDiscount() {
                    return Number(this.baseRegularPrice ?? 0) > Number(this.basePrice ?? 0);
                },

                formattedBaseRegularPrice() {
                    return this.$shop.formatPrice(Number(this.baseRegularPrice ?? 0));
                },

                formattedRateTotal() {
                    return this.$shop.formatPrice(this.rateTotal);
                },

                formattedGrandTotal() {
                    return this.$shop.formatPrice(this.grandTotal);
                },
            },

            watch: {
                'booking.slot_from'(newVal) {
                    const from = Number(newVal);

                    if (from && Number(this.booking.slot_to) <= from) {
                        this.booking.slot_to = '';
                    }
                },
            },

            methods: {
                tierRate(rule) {
                    const slot = this.bookingProduct?.rental_slot ?? {};
                    const dailyPrice = Number(slot.daily_price ?? 0);

                    if (rule.discount_type === 'fixed') {
                        return Math.max(0, dailyPrice - Number(rule.discount_value));
                    }

                    return dailyPrice * (1 - Number(rule.discount_value) / 100);
                },

                tierLabel(rule) {
                    const min = rule.min_days;
                    const max = rule.max_days;

                    if (! max) {
                        return `${min}+ @lang('custom-theme::app.products.view.type.booking.rental.days')`;
                    }

                    if (min === max) {
                        return `${min} @lang('custom-theme::app.products.view.type.booking.rental.days')`;
                    }

                    return `${min}–${max} @lang('custom-theme::app.products.view.type.booking.rental.days')`;
                },

                isActiveRule(rule) {
                    const days = this.durationDays;

                    if (! days) {
                        return false;
                    }

                    const min = Number(rule.min_days);
                    const max = rule.max_days !== null && rule.max_days !== undefined && rule.max_days !== ''
                        ? Number(rule.max_days)
                        : Infinity;

                    return days >= min && days <= max;
                },

                formatDate(d) {
                    const year = d.getFullYear();
                    const month = String(d.getMonth() + 1).padStart(2, '0');
                    const day = String(d.getDate()).padStart(2, '0');

                    return `${year}-${month}-${day}`;
                },

                dateSelected(params) {
                    let date = params.target.value;

                    this.$axios.get('{{ route('shop.booking-product.slots.index', ':id') }}'.replace(':id', this.bookingProduct.id), {
                        params: { date }
                    })
                        .then((response) => {
                            this.selected_slot = '';

                            this.slots = response.data.data;
                        })
                        .catch(error => {
                            if (error.response.status == 422) {
                                setErrors(error.response.data.errors);
                            }
                        });
                },
            },
        });
    </script>
@endpushOnce
