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
        Schema::table('settings', function (Blueprint $table) {
            $table->string('dana_qr_code_path')->nullable();
            $table->string('ovo_qr_code_path')->nullable();
            $table->string('gopay_qr_code_path')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(['dana_qr_code_path', 'ovo_qr_code_path', 'gopay_qr_code_path']);
        });
    }
};