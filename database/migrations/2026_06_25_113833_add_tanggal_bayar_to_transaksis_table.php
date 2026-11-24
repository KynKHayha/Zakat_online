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
        Schema::table('transaksis', function (Blueprint $table) {
            $table->date('tanggal_rencana_bayar')->nullable()->after('tanggal_transaksi');
            $table->dateTime('batas_waktu_bayar')->nullable()->after('tanggal_rencana_bayar');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaksis', function (Blueprint $table) {
            $table->dropColumn(['tanggal_rencana_bayar', 'batas_waktu_bayar']);
        });
    }
};
