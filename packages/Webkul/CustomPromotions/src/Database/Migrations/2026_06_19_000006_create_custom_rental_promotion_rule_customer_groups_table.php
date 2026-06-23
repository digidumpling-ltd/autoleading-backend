<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_rental_promo_rule_cgroups', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_promotion_rule_id');
            $table->unsignedInteger('customer_group_id');

            $table->foreign('rental_promotion_rule_id', 'crprcg_rule_id_fk')
                ->references('id')
                ->on('custom_rental_promotion_rules')
                ->onDelete('cascade');

            $table->foreign('customer_group_id', 'crprcg_cgroup_id_fk')
                ->references('id')
                ->on('customer_groups')
                ->onDelete('cascade');

            $table->primary(['rental_promotion_rule_id', 'customer_group_id'], 'crprcg_pk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_rental_promo_rule_cgroups');
    }
};
