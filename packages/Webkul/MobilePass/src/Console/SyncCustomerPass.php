<?php

namespace Webkul\MobilePass\Console;

use Illuminate\Console\Command;
use Spatie\LaravelMobilePass\Models\MobilePass;
use Webkul\Customer\Models\Customer;
use Webkul\MobilePass\Services\MobilePassService;

class SyncCustomerPass extends Command
{
    protected $signature = 'mobile-pass:sync {customerId? : Customer ID (omit to sync all)}
                            {--recreate : Delete and recreate the pass instead of syncing}';

    protected $description = 'Sync Google Wallet pass data (points, credit, tier) for a customer';

    public function handle(MobilePassService $service): int
    {
        $customerId = $this->argument('customerId');
        $recreate = $this->option('recreate');

        if ($customerId) {
            return $this->processCustomer((int) $customerId, $service, $recreate);
        }

        $passes = MobilePass::whereNotNull('model_id')->get();

        if ($passes->isEmpty()) {
            $this->info('No passes found.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($passes->count());
        $bar->start();

        foreach ($passes as $pass) {
            $this->processCustomer((int) $pass->model_id, $service, $recreate);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done.');

        return self::SUCCESS;
    }

    private function processCustomer(int $customerId, MobilePassService $service, bool $recreate): int
    {
        $customer = Customer::find($customerId);

        if (! $customer) {
            $this->error("Customer #{$customerId} not found.");

            return self::FAILURE;
        }

        if ($recreate) {
            MobilePass::where('model_id', $customerId)->delete();
            $service->createOrGetGooglePass($customer);
            $this->info("Recreated pass for customer #{$customerId} ({$customer->email}).");

            return self::SUCCESS;
        }

        $pass = $service->getCustomerGooglePass($customerId);

        if (! $pass) {
            $this->warn("No pass found for customer #{$customerId}. Use --recreate to create one.");

            return self::FAILURE;
        }

        $service->syncPassContent($pass, $customerId);
        $this->info("Synced pass for customer #{$customerId} ({$customer->email}).");

        return self::SUCCESS;
    }
}
