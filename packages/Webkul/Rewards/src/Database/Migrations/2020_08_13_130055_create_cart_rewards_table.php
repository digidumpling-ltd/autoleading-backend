<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up()
    {
        Schema::create('cart_rewards', function (Blueprint $table) {
            $table->increments('id');
            $table->double('amount_from', 8, 2);
            $table->double('amount_to', 8, 2);
            $table->integer('reward_points')->default(0);
            $table->dateTime('start_date', 0)->default(NULL);
            $table->dateTime('end_date', 0)->default(NULL);
            $table->tinyInteger('status')->default(1);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('product_rewards');
    }
};
