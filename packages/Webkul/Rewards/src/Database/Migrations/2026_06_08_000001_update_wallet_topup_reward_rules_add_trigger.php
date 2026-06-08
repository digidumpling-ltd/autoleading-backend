<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wallet_topup_reward_rules', function (Blueprint $table) {
            $table->renameColumn('min_topup_amount', 'min_amount');
            $table->renameColumn('max_topup_amount', 'max_amount');
        });

        Schema::table('wallet_topup_reward_rules', function (Blueprint $table) {
            $table->enum('trigger', ['wallet_topup', 'wallet_spend'])
                ->default('wallet_topup')
                ->after('customer_group_id');
        });

        DB::table('wallet_topup_reward_rules')
            ->whereNull('trigger')
            ->update(['trigger' => 'wallet_topup']);
    }

    public function down(): void
    {
        Schema::table('wallet_topup_reward_rules', function (Blueprint $table) {
            $table->dropColumn('trigger');
        });

        Schema::table('wallet_topup_reward_rules', function (Blueprint $table) {
            $table->renameColumn('min_amount', 'min_topup_amount');
            $table->renameColumn('max_amount', 'max_topup_amount');
        });
    }
};
