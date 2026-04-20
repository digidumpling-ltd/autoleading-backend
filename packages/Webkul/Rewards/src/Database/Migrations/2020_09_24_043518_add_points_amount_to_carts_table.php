<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPointsAmountToCartsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
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
    
    /**
     * Reverse the migrations.
     *
     * @return void
     */
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
}
