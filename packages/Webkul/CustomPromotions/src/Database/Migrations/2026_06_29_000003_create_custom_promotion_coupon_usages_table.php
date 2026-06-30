<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_promotion_coupon_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_promotion_coupon_id');
            $table->unsignedInteger('customer_id');
            $table->integer('times_used')->default(0);
            $table->timestamps();

            $table->foreign('custom_promotion_coupon_id', 'cpcu_coupon_id_fk')
                ->references('id')
                ->on('custom_promotion_coupons')
                ->onDelete('cascade');

            $table->foreign('customer_id', 'cpcu_customer_id_fk')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->unique(['custom_promotion_coupon_id', 'customer_id'], 'cpcu_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_promotion_coupon_usages');
    }
};
