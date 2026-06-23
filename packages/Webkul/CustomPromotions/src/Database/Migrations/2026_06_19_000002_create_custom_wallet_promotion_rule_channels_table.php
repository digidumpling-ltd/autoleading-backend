<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('custom_wallet_promo_rule_channels', function (Blueprint $table) {
            $table->unsignedBigInteger('wallet_promotion_rule_id');
            $table->unsignedInteger('channel_id');

            $table->foreign('wallet_promotion_rule_id', 'cwprc_rule_id_fk')
                ->references('id')
                ->on('custom_wallet_promotion_rules')
                ->onDelete('cascade');

            $table->foreign('channel_id', 'cwprc_channel_id_fk')
                ->references('id')
                ->on('channels')
                ->onDelete('cascade');

            $table->primary(['wallet_promotion_rule_id', 'channel_id'], 'cwprc_pk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('custom_wallet_promo_rule_channels');
    }
};
