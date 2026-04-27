<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('wallet_top_ups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->decimal('amount', 12, 4);
            $table->string('currency', 10);
            $table->string('payment_method', 64);
            $table->string('status', 20)->default('pending');
            $table->string('reference', 128)->nullable();
            $table->string('transaction_id', 128)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('customer_id');
            $table->index('status');
            $table->index('reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallet_top_ups');
    }
};
