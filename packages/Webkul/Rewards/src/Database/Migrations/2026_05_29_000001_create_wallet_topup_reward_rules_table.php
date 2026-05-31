<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wallet_topup_reward_rules', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('customer_group_id')->nullable();
            $table->foreign('customer_group_id')->references('id')->on('customer_groups')->onDelete('cascade');
            $table->enum('mode', ['fixed', 'percent']);
            $table->decimal('value', 10, 2);
            $table->decimal('min_topup_amount', 12, 2)->nullable();
            $table->decimal('max_topup_amount', 12, 2)->nullable();
            $table->integer('priority')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wallet_topup_reward_rules');
    }
};
