<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('booking_product_day_pricings', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('booking_product_id');
            $table->unsignedInteger('min_days');
            $table->unsignedInteger('max_days')->nullable();
            $table->decimal('discount_value', 12, 4);
            $table->enum('discount_type', ['fixed', 'percentage']);
            $table->timestamps();

            $table->index('booking_product_id');
            $table->index('min_days');
            $table->index('max_days');

            $table->foreign('booking_product_id')
                ->references('id')
                ->on('booking_products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('booking_product_day_pricings');
    }
};
