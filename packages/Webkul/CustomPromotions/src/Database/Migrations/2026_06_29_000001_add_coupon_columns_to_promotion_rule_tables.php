<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('custom_wallet_promotion_rules', function (Blueprint $table) {
            $table->tinyInteger('coupon_type')->default(0)->after('sort_order');
            $table->unsignedInteger('uses_per_coupon')->default(0)->after('coupon_type');
            $table->unsignedInteger('usage_per_customer')->default(0)->after('uses_per_coupon');
            $table->boolean('end_other_rules')->default(0)->after('usage_per_customer');
        });

        Schema::table('custom_rental_promotion_rules', function (Blueprint $table) {
            $table->tinyInteger('coupon_type')->default(0)->after('sort_order');
            $table->unsignedInteger('uses_per_coupon')->default(0)->after('coupon_type');
            $table->unsignedInteger('usage_per_customer')->default(0)->after('uses_per_coupon');
            $table->boolean('end_other_rules')->default(0)->after('usage_per_customer');
        });
    }

    public function down(): void
    {
        Schema::table('custom_wallet_promotion_rules', function (Blueprint $table) {
            $table->dropColumn(['coupon_type', 'uses_per_coupon', 'usage_per_customer', 'end_other_rules']);
        });

        Schema::table('custom_rental_promotion_rules', function (Blueprint $table) {
            $table->dropColumn(['coupon_type', 'uses_per_coupon', 'usage_per_customer', 'end_other_rules']);
        });
    }
};
