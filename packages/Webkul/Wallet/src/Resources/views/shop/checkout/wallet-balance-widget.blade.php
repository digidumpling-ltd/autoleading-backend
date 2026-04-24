@auth('customer')
<v-wallet-checkout-balance
    v-if="cart"
    :payment-method="cart.payment_method"
    :formatted-total="cart.formatted_grand_total"
    :grand-total="cart.grand_total"
></v-wallet-checkout-balance>

@pushOnce('scripts')
    <script type="module">
        app.component('v-wallet-checkout-balance', {
            template: `
                <div
                    v-if="paymentMethod === 'wallet' && walletStatus"
                    class="mt-4 rounded-lg border p-3"
                    :class="walletStatus.can_afford ? 'border-green-200 bg-green-50' : 'border-red-200 bg-red-50'"
                >
                    <p class="text-sm font-medium" :class="walletStatus.can_afford ? 'text-green-700' : 'text-red-700'">
                        {{ balanceLabel }} {{ walletStatus.formatted_balance }}
                    </p>

                    <template v-if="!walletStatus.can_afford">
                        <p class="mt-1 text-xs text-red-600">{{ insufficientHint }}</p>
                        <a
                            :href="walletStatus.wallet_url + '?reason=insufficient_balance&required=' + walletStatus.shortfall"
                            class="mt-2 inline-block rounded-lg bg-[#d18a1b] px-4 py-2 text-xs font-medium text-white hover:bg-[#b8760f]"
                        >{{ topUpLabel }}</a>
                    </template>

                    <template v-else>
                        <p class="mt-1 text-xs text-green-600">{{ chargeNote }} {{ formattedTotal }}</p>
                    </template>
                </div>
            `,

            props: {
                paymentMethod: { type: String, default: null },
                formattedTotal: { type: String, default: '' },
                grandTotal: { type: [Number, String], default: 0 },
            },

            data() {
                return {
                    walletStatus: null,
                    balanceLabel: "@lang('bagisto-wallet::app.checkout.wallet-balance-label')",
                    insufficientHint: "@lang('bagisto-wallet::app.checkout.insufficient-balance-hint')",
                    topUpLabel: "@lang('bagisto-wallet::app.checkout.insufficient-balance-button')",
                    chargeNote: "@lang('bagisto-wallet::app.checkout.wallet-charge-note')",
                };
            },

            watch: {
                paymentMethod(method) {
                    if (method === 'wallet') {
                        this.fetchStatus();
                    } else {
                        this.walletStatus = null;
                    }
                },
            },

            mounted() {
                if (this.paymentMethod === 'wallet') {
                    this.fetchStatus();
                }
            },

            methods: {
                fetchStatus() {
                    this.$axios.get('{{ route('shop.wallet.checkout.status') }}')
                        .then(response => { this.walletStatus = response.data; })
                        .catch(() => { this.walletStatus = null; });
                },
            },
        });
    </script>
@endPushOnce
@endauth
