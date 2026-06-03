<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tvd_submissions', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('chinese_name', 400);
            $table->string('english_name', 400);
            $table->string('rental_model', 400);
            $table->date('return_date');
            $table->string('contact_number', 400);
            $table->string('email', 400);
            $table->enum('refund_type', ['local', 'overseas']);
            $table->text('local_bank_info')->nullable();
            $table->text('overseas_bank_info')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tvd_submissions');
    }
};
