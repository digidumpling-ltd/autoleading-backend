{{--
    Rental Promo Card — self-contained Vue component (inline template literal, no x-template).

    Usage:
        @include('custom_promotions::shop.components.rental-promo-card')
        <v-rental-promo-card :date-from="booking.date_from" :date-to="booking.date_to" />

    Can be @include-d anywhere in a Blade view — inside or outside a parent @pushOnce block.
--}}
@pushOnce('scripts')
<script type="module">
    app.component('v-rental-promo-card', {
        template: `
            <div
                v-show="matchedPromos.length > 0"
                class="mt-3 rounded-lg border border-green-200 bg-green-50 p-3 dark:border-green-800 dark:bg-green-900/20"
            >
                <p class="mb-2 text-sm font-semibold text-green-800 dark:text-green-300">
                    @{{ heading }}
                </p>
                <ul class="space-y-1">
                    <li
                        v-for="promo in matchedPromos"
                        :key="promo.id"
                        class="text-xs text-green-700 dark:text-green-400"
                    >
                        <span v-if="promo.actions && promo.actions.length">
                            @{{ promo.actions.map(a => a.label).join(' · ') }}
                        </span>
                    </li>
                </ul>
            </div>
        `,

        props: {
            dateFrom: { type: String, default: '' },
            dateTo:   { type: String, default: '' },
            apiUrl:   { type: String, default: '{{ route('custom_promotions.rental.check') }}' },
        },

        data() {
            return {
                matchedPromos: [],
                heading: '{{ addslashes(trans('custom_promotions::app.shop.promo-card.heading')) }}',
            };
        },

        watch: {
            dateFrom() { this.fetchMatchedPromos(); },
            dateTo()   { this.fetchMatchedPromos(); },
        },

        methods: {
            async fetchMatchedPromos() {
                if (! this.dateFrom || ! this.dateTo) {
                    this.matchedPromos = [];
                    return;
                }

                try {
                    const res  = await fetch(`${this.apiUrl}?date_from=${this.dateFrom}&date_to=${this.dateTo}`);
                    const data = await res.json();
                    this.matchedPromos = data.data ?? [];
                } catch {
                    this.matchedPromos = [];
                }
            },
        },
    });
</script>
@endPushOnce
