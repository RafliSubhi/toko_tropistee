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
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('status', 'status_pesanan');
            $table->renameColumn('payment_status', 'status_pembayaran');
        });

        // Change the default value of the status_pesanan column
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status_pesanan')->default('Belum Diterima')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('status_pesanan', 'status');
            $table->renameColumn('status_pembayaran', 'payment_status');
        });

        // Revert the default value change
        Schema::table('orders', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }
};