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
        Schema::create('donasi_zakats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('jenis_zakat', ['fitrah', 'mal', 'profesi', 'perniagaan', 'pertanian', 'emas_perak'])->default('fitrah');
            $table->string('nama_muzakki');
            $table->integer('jumlah_jiwa')->nullable()->comment('Untuk zakat fitrah');
            $table->decimal('harga_beras', 10, 2)->nullable()->comment('Harga beras per kg untuk fitrah');
            $table->decimal('penghasilan', 15, 2)->nullable()->comment('Untuk zakat mal/profesi');
            $table->decimal('nilai_aset', 15, 2)->nullable()->comment('Untuk zakat perniagaan/emas');
            $table->decimal('jumlah_zakat', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->enum('status', ['draft', 'menunggu', 'sukses', 'ditolak'])->default('draft');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('donasi_zakats');
    }
};
