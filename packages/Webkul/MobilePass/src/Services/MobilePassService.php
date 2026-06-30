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

        $theme = $this->appleTierTheme($customer->group?->id);

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
            ->setIconImage($assets.'/'.$theme['icon'].'.png', $assets.'/'.$theme['icon'].'@2x.png', $assets.'/'.$theme['icon'].'@3x.png')
            ->setLogoImage($assets.'/'.$theme['logo'].'.png', $assets.'/'.$theme['logo'].'@2x.png', $assets.'/'.$theme['logo'].'@3x.png')
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
     * Resolve the Apple-pass colour scheme for a customer group from the
     * membership tier rules managed in admin/membership/tiers. The admin sets
     * background + text colours per tier; the logo/icon variant is derived from
     * the background brightness (light background -> black-wordmark logo +
     * white-background icon; dark background -> white logo + dark icon). Groups
     * with no configured tier colour fall back to the white default.
     *
     * @return array{background:string,foreground:string,label:string,logo:string,icon:string}
     */
    protected function appleTierTheme(?int $groupId): array
    {
        $defaultBg = '#FFFFFF';
        $defaultText = '#0E0D0C';

        $bg = $defaultBg;
        $text = $defaultText;

        if ($groupId) {
            $rule = \Webkul\Membership\Models\TierRule::where('customer_group_id', $groupId)->first();
            if ($rule && $rule->background_color) {
                $bg = $rule->background_color;
                $text = $rule->text_color ?: $this->contrastColor($bg);
            }
        }

        $isLight = $this->isLightColor($bg);

        return [
            'background' => $bg,
            'foreground' => $text,
            // Field captions (NAME / UPDATED / WALLET BALANCE / POINTS) always use
            // the AutoLeading brand orange across every tier.
            'label'      => '#E2620A',
            'logo'       => $isLight ? 'logo-dark' : 'logo',
            'icon'       => $isLight ? 'icon-light' : 'icon',
        ];
    }

    /**
     * Whether a #RRGGBB colour is "light" (per relative luminance), used to pick
     * the logo/icon variant and a contrasting text colour.
     */
    protected function isLightColor(string $hex): bool
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) !== 6) {
            return true;
        }
        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));
        // Rec. 601 luma; > 150 reads as light.
        return (0.299 * $r + 0.587 * $g + 0.114 * $b) > 150;
    }

    protected function contrastColor(string $bgHex): string
    {
        return $this->isLightColor($bgHex) ? '#0E0D0C' : '#FFFFFF';
    }

    /**
     * The pass background colour (hex) a given customer-group code resolves to.
     * Exposed so the tier-change listener can compare against a stored pass.
     */
    public function appleThemeBackgroundFor(?int $groupId): string
    {
        return $this->appleTierTheme($groupId)['background'];
    }

    /**
     * Convert a #RRGGBB hex string to the "rgb(r, g, b)" form the pkpass
     * library stores in pass content, so the two can be compared directly.
     */
    public function hexToRgbString(string $hex): string
    {
        $hex = ltrim($hex, '#');

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        return "rgb({$r}, {$g}, {$b})";
    }

    public function deleteApplePass(int $customerId): bool
    {
        $pass = $this->getCustomerApplePass($customerId);

        if (! $pass) {
            return false;
        }

        // A pass installed on a device has rows in apple_mobile_pass_registrations
        // that FK-reference it; remove them first so the delete does not fail.
        \DB::table('apple_mobile_pass_registrations')->where('mobile_pass_id', $pass->id)->delete();

        $pass->delete();

        return true;
    }

    /**
     * Re-theme a customer's existing Apple pass in place (colours + logo/icon)
     * to match their current membership tier. Updates the SAME pass record so
     * its serial and device registrations are preserved — the model's updated()
     * hook then fires an APNs push so the installed pass refreshes on-device.
     * (A delete + recreate would orphan the copy already on the customer's
     * phone, leaving it stuck on the old colour forever.) No-ops when the
     * customer has no Apple pass. Returns true when a re-theme happened.
     */
    public function rebuildApplePass(int $customerId): bool
    {
        $pass = $this->getCustomerApplePass($customerId);

        if (! $pass) {
            return false;
        }

        $customer = Customer::find($customerId);

        if (! $customer) {
            return false;
        }

        $assets = dirname(__DIR__).'/Resources/assets/images/apple-pass';
        $theme = $this->appleTierTheme($customer->group?->id);

        // Hydrate the builder from the stored pass, re-apply the tier theme,
        // and save() back onto the same record (triggers the push).
        $pass->builder()
            ->setBackgroundColor($theme['background'])
            ->setForegroundColor($theme['foreground'])
            ->setLabelColor($theme['label'])
            ->setIconImage($assets.'/'.$theme['icon'].'.png', $assets.'/'.$theme['icon'].'@2x.png', $assets.'/'.$theme['icon'].'@3x.png')
            ->setLogoImage($assets.'/'.$theme['logo'].'.png', $assets.'/'.$theme['logo'].'@2x.png', $assets.'/'.$theme['logo'].'@3x.png')
            ->save();

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
