<?php

namespace Webkul\MobilePass\Console;

use Illuminate\Console\Command;
use Spatie\LaravelMobilePass\Support\Google\GoogleCredentials;
use Spatie\LaravelMobilePass\Support\Google\GoogleWalletClient;
use Webkul\MobilePass\Services\MobilePassService;

class SetupGoogleLoyaltyClass extends Command
{
    protected $signature = 'mobile-pass:setup-google-class
                            {--program-name= : Loyalty program name shown on the pass}
                            {--issuer-name= : Issuer name shown on the pass}';

    protected $description = 'Create or update the Google Wallet LoyaltyClass for this store';

    public function handle(GoogleWalletClient $client, MobilePassService $mobilePassService): int
    {
        $issuerId = GoogleCredentials::issuerId();
        $classSuffix = $mobilePassService->getClassSuffix();

        if (! $issuerId || ! $classSuffix) {
            $this->error('Configure Issuer ID and Service Account Key in Admin → Configuration → Sales → Mobile Pass → Google Wallet.');

            return self::FAILURE;
        }

        $classId = "{$issuerId}.{$classSuffix}";

        $programName = $this->option('program-name')
            ?? $this->ask('Loyalty program name (shown on pass)', config('app.name').' Wallet');

        $issuerName = $this->option('issuer-name')
            ?? $this->ask('Issuer name (shown on pass)', config('app.name'));

        $logo = [
            'sourceUri' => [
                'uri' => 'https://pub-6c55787da303455183f50f449d55c75e.r2.dev/channel/1/C7B7HvIW24weCKLhjN0yZdkSMXxfJcivlDfBfTT3.png',
            ],
            'contentDescription' => [
                'defaultValue' => [
                    'language' => 'en-US',
                    'value' => $programName.' logo',
                ],
            ],
        ];

        $this->info("Creating/updating LoyaltyClass: {$classId}");

        try {
            $exists = true;
            try {
                $client->getClass('loyaltyClass', $classId);
            } catch (\Exception) {
                $exists = false;
            }

            $classPayload = [
                'issuerName'  => $issuerName,
                'programName' => $programName,
                'reviewStatus' => 'UNDER_REVIEW',
                'programLogo' => $logo,
                // Google sets the background on the (shared) LoyaltyClass, so it
                // is one colour for every customer rather than per-tier like
                // Apple. Use the AutoLeading brand black.
                'hexBackgroundColor' => '#0E0D0C',
            ];

            if ($exists) {
                $result = $client->patchClass('loyaltyClass', $classId, $classPayload);
            } else {
                $result = $client->insertClass('loyaltyClass', $classId, $classPayload);
            }

            $this->info('Done. Status: '.($result['reviewStatus'] ?? 'unknown'));
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
