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
        Schema::create('pembayarans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaksi_id')->constrained()->onDelete('cascade');
            $table->string('metode_pembayaran', 100)->comment('e.g. BRI, BNI, OVO, Tunai, Transfer');
            $table->string('nama_bank')->nullable();
            $table->string('nomor_rekening')->nullable();
            $table->dateTime('tanggal_pembayaran')->nullable();
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->string('bukti_transfer')->nullable()->comment('Path file bukti transfer');
            $table->enum('status', ['pending', 'dikonfirmasi', 'ditolak'])->default('pending');
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
