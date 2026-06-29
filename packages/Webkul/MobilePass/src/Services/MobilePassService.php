<?php

namespace Webkul\MobilePass\Services;

use Spatie\LaravelMobilePass\Builders\Apple\StoreCardPassBuilder;
use Spatie\LaravelMobilePass\Builders\Google\LoyaltyPassBuilder;
use Spatie\LaravelMobilePass\Enums\BarcodeType;
use Spatie\LaravelMobilePass\Enums\Platform;
use Spatie\LaravelMobilePass\Models\MobilePass;
use Webkul\Customer\Models\Customer;
use Webkul\Rewards\Repositories\RewardPointRepository;

class MobilePassService
{
    public function getCustomerGooglePass(int $customerId): ?MobilePass
    {
        return MobilePass::where('model_type', Customer::class)
            ->where('model_id', $customerId)
            ->where('platform', Platform::Google)
            ->first();
    }

    public function createOrGetGooglePass(Customer $customer): MobilePass
    {
        $existing = $this->getCustomerGooglePass($customer->id);

        if ($existing) {
            return $existing;
        }

        $rewardPoints = app(RewardPointRepository::class)->totalRewardPoints($customer->id);

        $pass = LoyaltyPassBuilder::make()
            ->setClass($this->getClassSuffix())
            ->setObjectSuffix('WALLET-'.$customer->id)
            ->setAccountId((string) $customer->id)
            ->setAccountName($customer->first_name.' '.$customer->last_name)
            ->setBalanceString((string) ($rewardPoints ?? 0))
            ->setBarcode(BarcodeType::Qr, (string) $customer->id)
            ->save();

        $pass->update([
            'model_type' => Customer::class,
            'model_id' => $customer->id,
        ]);

        $this->syncPassContent($pass, $customer->id);

        return $pass->fresh();
    }

    public function getCustomerApplePass(int $customerId): ?MobilePass
    {
        return MobilePass::where('model_type', Customer::class)
            ->where('model_id', $customerId)
            ->where('platform', Platform::Apple)
            ->first();
    }

    public function createOrGetApplePass(Customer $customer): MobilePass
    {
        $existing = $this->getCustomerApplePass($customer->id);

        if ($existing) {
            return $existing;
        }

        $rewardPoints = app(RewardPointRepository::class)->totalRewardPoints($customer->id);
        $walletBalance = \Webkul\Wallet\Models\Customer::find($customer->id)?->balanceFloatNum ?? 0;
        $tier = $customer->group?->name ?: 'Standard';
        $memberName = trim($customer->first_name.' '.$customer->last_name) ?: 'Member';
        $updated = now()->format('n/j/y');

        $organization = (string) (config('mobile-pass.apple.organization_name') ?: 'Auto Leading Limited');

        $assets = dirname(__DIR__).'/Resources/assets/images/apple-pass';

        $theme = $this->appleTierTheme($customer->group?->code);

        // Layout mirrors the reference design: logo + Points in the header, a
        // full-width strip banner, then a row of three secondary fields
        // (Name / Updated / Reward Value). Tier lives on the back of the card.
        $pass = StoreCardPassBuilder::make()
            ->setSerialNumber('WALLET-'.$customer->id)
            ->setOrganizationName($organization)
            ->setDescription($organization.' Membership')
            ->setBackgroundColor($theme['background'])
            ->setForegroundColor($theme['foreground'])
            ->setLabelColor($theme['label'])
            ->setIconImage($assets.'/icon.png', $assets.'/icon@2x.png', $assets.'/icon@3x.png')
            ->setLogoImage($assets.'/logo.png', $assets.'/logo@2x.png', $assets.'/logo@3x.png')
            ->setStripImage($assets.'/strip.png', $assets.'/strip@2x.png', $assets.'/strip@3x.png')
            ->addHeaderField('points', $this->applePointsValue($rewardPoints), 'Points')
            ->addSecondaryField('name', $memberName, 'Name')
            ->addSecondaryField('updated', $updated, 'Updated')
            ->addSecondaryField('credit', core()->formatPrice($walletBalance), 'Wallet Balance')
            ->addBackField('tier', $tier, 'Membership Tier')
            ->setBarcode(BarcodeType::Qr, (string) $customer->id)
            ->save();

        $pass->update([
            'model_type' => Customer::class,
            'model_id' => $customer->id,
        ]);

        return $pass->fresh();
    }

    public function syncApplePassContent(int $customerId): void
    {
        $pass = $this->getCustomerApplePass($customerId);

        if (! $pass) {
            return;
        }

        $rewardPoints = app(RewardPointRepository::class)->totalRewardPoints($customerId);
        $walletBalance = \Webkul\Wallet\Models\Customer::find($customerId)?->balanceFloatNum ?? 0;
        $tier = Customer::find($customerId)?->group?->name ?? 'Standard';

        $pass->updateField('points', $this->applePointsValue($rewardPoints));
        $pass->updateField('credit', core()->formatPrice($walletBalance));
        $pass->updateField('tier', $tier ?: 'Standard');
        $pass->updateField('updated', now()->format('n/j/y'));
    }

    /**
     * Format the reward-points value for an Apple pass field. The value must
     * never be a "falsy" string: the underlying pkpass field serializer runs
     * array_filter() over each field, which silently drops a bare "0" or empty
     * string, leaving a field with a label and no value. Apple rejects such a
     * pass on-device (it opens then immediately closes). Appending a unit makes
     * the value non-falsy and reads better than a lone number.
     */
    protected function applePointsValue($rewardPoints): string
    {
        return ((int) ($rewardPoints ?? 0)).' pts';
    }

    /**
     * Per-membership-tier colour scheme for the Apple pass, keyed by customer
     * group code. Labels stay AutoLeading orange where it reads well; on the
     * lighter gold tier the label switches to white for contrast. Unknown or
     * non-member groups fall back to the brand black theme.
     *
     * @return array{background:string,foreground:string,label:string}
     */
    protected function appleTierTheme(?string $groupCode): array
    {
        $themes = [
            'member1' => ['background' => '#0E0D0C', 'foreground' => '#FFFFFF', 'label' => '#E2620A'], // Regular  - brand black
            'member2' => ['background' => '#9A7B1F', 'foreground' => '#FFFFFF', 'label' => '#FFFFFF'], // Gold
            'member3' => ['background' => '#3A3F44', 'foreground' => '#FFFFFF', 'label' => '#E2620A'], // Platinum - graphite
            'member4' => ['background' => '#1E3A5F', 'foreground' => '#FFFFFF', 'label' => '#E2620A'], // Diamond  - deep blue
            'member5' => ['background' => '#0E6B4F', 'foreground' => '#FFFFFF', 'label' => '#E2620A'], // Jadeite  - jade green
        ];

        return $themes[$groupCode] ?? ['background' => '#0E0D0C', 'foreground' => '#FFFFFF', 'label' => '#E2620A'];
    }

    public function deleteApplePass(int $customerId): bool
    {
        $pass = $this->getCustomerApplePass($customerId);

        if (! $pass) {
            return false;
        }

        $pass->delete();

        return true;
    }

    public function hasAppleCredentials(): bool
    {
        $hasCert = ! empty(config('mobile-pass.apple.certificate'))
            || ! empty(config('mobile-pass.apple.certificate_path'));

        return $hasCert
            && ! empty(config('mobile-pass.apple.type_identifier'))
            && ! empty(config('mobile-pass.apple.team_identifier'));
    }

    public function syncPassContent(MobilePass $pass, int $customerId): void
    {
        $rewardPoints = app(RewardPointRepository::class)->totalRewardPoints($customerId);

        $walletBalance = \Webkul\Wallet\Models\Customer::find($customerId)?->balanceFloatNum ?? 0;

        $customer = Customer::find($customerId);
        $tier = $customer?->group?->name ?? 'Standard';

        $content = $pass->content ?? [];
        $content['googleObjectPayload']['loyaltyPoints'] = [
            'label' => 'Points',
            'balance' => ['string' => (string) ($rewardPoints ?? 0)],
        ];
        $content['googleObjectPayload']['secondaryLoyaltyPoints'] = [
            'label' => 'Credit',
            'balance' => ['string' => core()->formatPrice($walletBalance)],
        ];
        $content['googleObjectPayload']['textModulesData'] = [
            ['id' => 'tier', 'header' => 'Tier', 'body' => $tier],
        ];

        $pass->update(['content' => $content]);
    }

    public function getClassSuffix(): string
    {
        return (string) (core()->getConfigData('sales.mobile_pass.google.class_suffix') ?: 'loyalty_class');
    }

    public function deleteGooglePass(int $customerId): bool
    {
        $pass = $this->getCustomerGooglePass($customerId);

        if (! $pass) {
            return false;
        }

        $pass->delete();

        return true;
    }

    public function isEnabled(): bool
    {
        return (bool) core()->getConfigData('sales.mobile_pass.settings.enabled');
    }

    public function hasGoogleCredentials(): bool
    {
        return ! empty(config('mobile-pass.google.issuer_id'))
            && (! empty(config('mobile-pass.google.service_account_key')) || ! empty(config('mobile-pass.google.service_account_key_path')));
    }
}
