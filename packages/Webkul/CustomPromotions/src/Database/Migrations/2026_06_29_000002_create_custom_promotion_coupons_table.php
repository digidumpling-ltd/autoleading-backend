<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_promotion_coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotion_id');
            $table->string('promotion_type');
            $table->string('code')->nullable();
            $table->unsignedInteger('usage_limit')->default(0);
            $table->unsignedInteger('usage_per_customer')->default(0);
            $table->unsignedInteger('times_used')->default(0);
            $table->boolean('is_primary')->default(0);
            $table->date('expired_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_promotion_coupons');
    }
};
