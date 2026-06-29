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
        $tier = $customer->group?->name ?? 'Standard';

        $organization = (string) (config('mobile-pass.apple.organization_name') ?: 'Auto Leading Limited');

        $pass = StoreCardPassBuilder::make()
            ->setSerialNumber('WALLET-'.$customer->id)
            ->setOrganizationName($organization)
            ->setDescription($organization.' Membership')
            ->setBackgroundColor('#0E0D0C')
            ->setForegroundColor('#FFFFFF')
            ->setLabelColor('#E2620A')
            ->addHeaderField('points', (string) ($rewardPoints ?? 0), 'Points')
            ->addSecondaryField('credit', core()->formatPrice($walletBalance), 'Credit')
            ->addAuxiliaryField('tier', $tier, 'Tier')
            ->addBackField('name', trim($customer->first_name.' '.$customer->last_name), 'Member')
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

        $pass->updateField('points', (string) ($rewardPoints ?? 0));
        $pass->updateField('credit', core()->formatPrice($walletBalance));
        $pass->updateField('tier', $tier);
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
