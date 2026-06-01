<!-- Points Vue Component -->
@php
  $cart = \Webkul\Checkout\Facades\Cart::getCart();

  if ($cart) $cart["reward_points"] = null;

  $applyCheckCart = app('\Webkul\Rewards\Repositories\RedemptionSettingRepository')->first();
@endphp

@if (
    ((request()->is('checkout/cart') && ! empty($applyCheckCart->redemp_over_subtotal))
    || (request()->is('checkout/onepage') && ! empty($applyCheckCart->apply_points_checkout))) 
    &&  (! empty($applyCheckCart->points) && ! empty($applyCheckCart->conversion_rate))
)
    <!-- Reward summary -->
    @if (
        core()->getConfigData('reward.general.general.module-status')
        && auth()->guard('customer')->user()
        && $cart
    )
        @inject('rewardPointRepository', 'Webkul\Rewards\Repositories\RewardPointRepository')
        @inject('redemptionSettingRepository', 'Webkul\Rewards\Repositories\RedemptionSettingRepository')

        @php
            $totalrewardpoints = 0;
            
            if (
                core()->getConfigData('reward.general.general.module-status')
                && auth()->guard('customer')->user()
            ) {
                $totalrewardpoints = $rewardPointRepository->totalRewardPoints(auth()->guard('customer')->user()->id);
            }

            $redemptionSetting = '';
            
            if ($redemption = $redemptionSettingRepository->getData()) {
                if ($redemption->conversion_rate) {
                    $redemptionSetting = trans('rewards::app.checkout.onepage.redemption-setting', ['points' => $redemption->points, 'conversion_rate' => core()->formatPrice($redemption->conversion_rate)]);
                }
            }
        @endphp

        <v-points :sub-total="cart.base_grand_total"></v-points>

        @pushOnce('scripts')
            <script 
                type="text/x-template" 
                id="v-points-template" 
                v-if="{{ auth()->guard('customer')->user()->id }}"
            >
                <div class="flex justify-between text-right">
                    <p class="text-base max-md:font-normal max-sm:text-sm">
                        @{{ rewardApplied ? "@lang('rewards::app.checkout.total.redem-points')" : "@lang('rewards::app.shop.checkout.cart.points.reward-points')" }}
                    </p>

                    <p class="text-base font-medium max-sm:text-sm">
                        <!-- Apply points modal -->
                        <x-shop::form
                            v-slot="{ meta, errors, handleSubmit }"
                            as="div"
                        >
                            <form @submit="handleSubmit($event, applyPoints)">
                                <x-shop::modal ref="RewardModel">
                                    <!-- Modal Toggler -->
                                    <x-slot:toggle>
                                        <span
                                            class="cursor-pointer text-base text-blue-700 max-sm:text-sm"
                                            role="button"
                                            tabindex="0"
                                            v-if="! rewardApplied"
                                        >
                                            @lang('rewards::app.shop.checkout.cart.points.apply-points')
                                        </span>
                                    </x-slot:toggle>

                                    <!-- Modal Header -->
                                    <x-slot:header class="max-md:p-5">
                                        <h2 class="text-2xl font-medium max-md:text-base">
                                            @lang('rewards::app.shop.checkout.cart.points.apply-points')
                                        </h2>
                                    </x-slot:header>

                                    <!-- Modal Content -->
                                    <x-slot:content class="!px-4">
                                        <x-shop::form.control-group class="!mb-0">
                                            <x-shop::form.control-group.control
                                                type="text"
                                                name="points"
                                                class="px-6 py-4 max-md:!mb-0 max-md:!p-3 max-sm:!p-2"
                                                :placeholder="trans('rewards::app.shop.checkout.cart.points.enter-points')"
                                                v-model="inputPoints"
                                            >
                                            </x-shop::form.control-group.control>
                                        </x-shop::form.control-group>

                                        <p
                                            style="color:#f22020;"
                                            class="mt-1 text-sm italic"
                                        >
                                            @{{ errorMessage }}
                                        </p>

                                        <p class="mt-3 text-sm text-zinc-500">
                                            @lang('rewards::app.checkout.onepage.total-point', ['total_reward_points' => $totalrewardpoints ])
                                            <i>({{ $redemptionSetting }})</i>
                                        </p>
                                    </x-slot:content>

                                    <!-- Modal Footer -->
                                    <x-slot:footer>
                                        <div class="flex flex-wrap items-center gap-4 max-md:justify-between">
                                            <div class="flex items-center gap-4 max-md:block">
                                                <p class="text-sm font-medium text-zinc-500 max-md:text-left max-md:text-xs">
                                                    @lang('shop::app.checkout.coupon.subtotal')
                                                </p>

                                                <p class="text-3xl font-semibold max-md:text-lg" v-text="subTotal">
                                                </p>
                                            </div>

                                            <x-shop::button
                                                class="primary-button max-w-none flex-auto rounded-2xl px-11 py-3 max-md:max-w-[153px] max-md:rounded-lg max-md:py-2"
                                                :title="trans('shop::app.checkout.coupon.button-title')"
                                            />
                                        </div>
                                    </x-slot:footer>
                                </x-shop::modal>
                            </form>
                        </x-shop::form>

                        <!-- Applied Points Information Container -->
                        <span
                            class="inline-flex items-center gap-2"
                            v-if="rewardApplied"
                        >
                            <span
                                class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-50 px-3 py-0.5 text-sm font-semibold text-emerald-700 dark:border-emerald-800 dark:bg-emerald-950 dark:text-emerald-300 max-sm:text-xs"
                                :title="'@lang('shop::app.checkout.coupon.applied')'"
                            >
                                @{{ appliedPoints }}
                            </span>

                            <span
                                class="icon-cancel cursor-pointer text-xl text-gray-400 transition-colors hover:text-red-500 max-sm:text-base"
                                title="@lang('shop::app.checkout.coupon.remove')"
                                @click="removePoints"
                            >
                            </span>
                        </span>
                    </p>
                </div>
            </script>

            <script type="module">
                app.component('v-points', {
                    template: '#v-points-template',

                    props: ['subTotal'],

                    data() {
                        return {
                            isCartPage: Boolean("{{ request()->route()->getName() === 'shop.checkout.cart.index' }}"),
                            inputPoints: '',
                            errorMessage: '',
                            rewardApplied : "{{ $cart->points }}",
                            appliedPoints: "{{ $cart->points }}",
                            routeName: "{{ request()->route()->getName() }}",
                            cart: null,
                        }
                    },

                    watch: {
                            inputPoints: function (value) {
                                if (value != '') {
                                    this.errorMessage = '';
                                }
                            },
                        },

                    methods: {
                        applyPoints(params, { resetForm}) {
                            if (! this.inputPoints.length) {
                                this.errorMessage = `{{ trans('rewards::app.checkout.total.invalid-points') }}`;

                                return;
                            }

                            if (! this.validPoints(this.inputPoints)) {
                                this.errorMessage = `{{ trans('rewards::app.checkout.total.only-number') }}`;

                                return;
                            }

                            let points = parseInt(self.inputPoints);

                            this.$axios.post(`{{ route('rewards.checkout.cart.points.apply') }}`, params)
                                .then((response) => {   
                                    if (this.isCartPage) {
                                        this.$parent.$parent.$refs.vCart.getCart();
                                    } else {
                                        this.$parent.getCart();
                                    }       
                                                        
                                    if (response.data.success) {
                                        this.cart = response.data.data;

                                        this.$emit('onApplyPoints');

                                        this.appliedPoints = this.inputPoints;

                                        this.inputPoints = '';

                                        this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                        this.$refs.RewardModel.toggle();

                                        this.rewardApplied = true;

                                    } else {
                                        this.errorMessage = response.data.message;
                                    }

                                    self.disable_button = false;
                                }).catch(error => {
                                    self.errorMessage = error.response.data.message;

                                    self.disable_button = false;
                                });
                        },

                        removePoints() {
                            this.$axios.delete(`{{ route('rewards.checkout.points.remove.points') }}`, {
                                '_token': "{{ csrf_token() }}"
                            })

                            .then(response => {
                                if (this.isCartPage) {
                                    this.$parent.$parent.$refs.vCart.getCart();
                                } else {
                                    this.$parent.getCart();
                                }

                            this.rewardApplied = false;

                            this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });
                            })
                            .catch(error => console.log(error));
                        },
                        
                        validPoints: function (inputPoints) {
                            var reg = new RegExp(/^\d*\.?\d*$/);

                            return reg.test(inputPoints);
                        }
                    }
                })
            </script>
        @endPushOnce
    @endif
@endif