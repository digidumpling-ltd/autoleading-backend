<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('membership_tier_rules', function (Blueprint $table) {
            $table->id();
            $table->decimal('min_balance', 12, 4);
            $table->decimal('max_balance', 12, 4)->nullable();
            $table->unsignedInteger('customer_group_id');
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->index('sort_order');
            $table->index('customer_group_id');

            $table->foreign('customer_group_id')
                ->references('id')
                ->on('customer_groups')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('membership_tier_rules');
    }
};
