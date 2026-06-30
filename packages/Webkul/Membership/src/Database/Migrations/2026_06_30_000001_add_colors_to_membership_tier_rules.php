<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('membership_tier_rules', function (Blueprint $table) {
            // Wallet-pass colours managed per tier in admin/membership/tiers.
            // Hex strings (e.g. "#0E6B4F"); nullable so existing rows and
            // unconfigured tiers fall back to the default white theme.
            $table->string('background_color', 7)->nullable()->after('customer_group_id');
            $table->string('text_color', 7)->nullable()->after('background_color');
        });
    }

    public function down(): void
    {
        Schema::table('membership_tier_rules', function (Blueprint $table) {
            $table->dropColumn(['background_color', 'text_color']);
        });
    }
};
