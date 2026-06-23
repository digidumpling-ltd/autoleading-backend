<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_promotion_rule_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_promotion_rule_id');
            $table->string('rule_type');
            $table->unsignedInteger('customer_id');
            $table->timestamp('used_at')->nullable();
            $table->timestamps();

            $table->foreign('customer_id', 'cpru_customer_id_fk')
                ->references('id')
                ->on('customers')
                ->onDelete('cascade');

            $table->unique(['custom_promotion_rule_id', 'rule_type', 'customer_id'], 'cpru_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_promotion_rule_usages');
    }
};
