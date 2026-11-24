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
        Schema::create('transaksis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donasi_zakat_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('kode_transaksi', 50)->unique();
            $table->dateTime('tanggal_transaksi');
            $table->enum('metode_bayar', ['online', 'ditempat'])->default('online');
            $table->string('bank_tujuan')->nullable()->comment('Nama bank tujuan untuk online');
            $table->decimal('total_bayar', 15, 2)->default(0);
            $table->enum('status', ['menunggu', 'sukses', 'ditolak', 'dibatalkan'])->default('menunggu');
            $table->text('alasan_tolak')->nullable();
            $table->dateTime('diverifikasi_at')->nullable();
            $table->foreignId('diverifikasi_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaksis');
    }
};
