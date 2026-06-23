<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_rental_promo_rule_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('rental_promotion_rule_id');
            $table->unsignedInteger('channel_id');

            $table->foreign('rental_promotion_rule_id', 'crprc_rule_id_fk')
                ->references('id')
                ->on('custom_rental_promotion_rules')
                ->onDelete('cascade');

            $table->foreign('channel_id', 'crprc_channel_id_fk')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');

            $table->primary(['rental_promotion_rule_id', 'channel_id'], 'crprc_pk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_rental_promo_rule_channels');
    }
};
