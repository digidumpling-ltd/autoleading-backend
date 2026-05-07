<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add rejection_reason to customers table if not exists
        if (Schema::hasTable('customers') && ! Schema::hasColumn('customers', 'rejection_reason')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->text('rejection_reason')->nullable()->after('verification_status');
            });
        }

        if (! Schema::hasTable('verification_audit_logs')) Schema::create('verification_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('admin_id')->nullable();
            $table->unsignedInteger('customer_id');
            $table->string('action', 50); // approved, rejected, viewed
            $table->text('reason')->nullable();
            $table->timestamps();

            $table->foreign('admin_id')->references('id')->on('admins')->onDelete('set null');
            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('cascade');
            $table->index('customer_id');
            $table->index('admin_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('verification_audit_logs');

        if (Schema::hasTable('customers') && Schema::hasColumn('customers', 'rejection_reason')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('rejection_reason');
            });
        }
    }
};
