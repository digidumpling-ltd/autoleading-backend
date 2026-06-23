<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_wallet_promotion_rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->date('starts_from')->nullable();
            $table->date('ends_till')->nullable();
            $table->boolean('status')->default(false);
            $table->tinyInteger('condition_type')->default(1);
            $table->json('conditions')->nullable();
            $table->string('action_type');
            $table->string('reward_mode')->nullable();
            $table->text('reward_value')->nullable();
            $table->integer('sort_order')->unsigned()->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_wallet_promotion_rules');
    }
};
