<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->integer('points_amount')->nullable();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('points_amount')->nullable();
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->integer('points_amount')->nullable();
        });
    }

    public function down()
    {
        Schema::table('cart', function (Blueprint $table) {
            $table->dropColumn('points_amount');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('points_amount');
        });

        Schema::table('order_items', function (Blueprint $table) {
            $table->dropColumn('points_amount');
        });
    }
};
