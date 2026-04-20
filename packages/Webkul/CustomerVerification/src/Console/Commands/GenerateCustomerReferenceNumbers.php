<?php

namespace Webkul\CustomerVerification\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Webkul\Customer\Models\Customer;

class GenerateCustomerReferenceNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'customer:generate-reference-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate reference numbers for customers that do not have one';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $customersWithoutReference = Customer::whereNull('reference_number')->get();

        if ($customersWithoutReference->isEmpty()) {
            $this->info('All customers already have reference numbers.');
            return Command::SUCCESS;
        }

        $this->info('Generating reference numbers for ' . $customersWithoutReference->count() . ' customers...');

        $bar = $this->output->createProgressBar($customersWithoutReference->count());
        $bar->start();

        foreach ($customersWithoutReference as $customer) {
            $customer->reference_number = $this->createUniqueReferenceNumber();
            $customer->save();
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
        $this->info('Reference numbers generated successfully!');

        return Command::SUCCESS;
    }

    /**
     * Create a unique reference number for customer verification.
     */
    protected function createUniqueReferenceNumber(): string
    {
        do {
            // Format: CV + Date(YYYYMMDD) + Random(6 chars)
            // Example: CV20260418ABC123
            $referenceNumber = 'CV' . now()->format('Ymd') . strtoupper(Str::random(6));
        } while (Customer::where('reference_number', $referenceNumber)->exists());

        return $referenceNumber;
    }
}
