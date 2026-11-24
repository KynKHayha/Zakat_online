<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Notifikasi
 *
 * In-app notification system untuk Muzakki
 */
class Notifikasi extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'judul',
        'pesan',
        'tipe',
        'is_read',
        'transaksi_id',
    ];

    protected $casts = [
        'is_read' => 'boolean',
    ];

    // ═══════════════════════════════════
    // RELASI
    // ═══════════════════════════════════

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    // ═══════════════════════════════════
    // HELPER METHODS
    // ═══════════════════════════════════

    /**
     * Buat notifikasi sukses pembayaran
     */
    public static function buatNotifikasiSukses(int $userId, int $transaksiId, string $kodeTransaksi): self
    {
        return self::create([
            'user_id'      => $userId,
            'judul'        => 'Pembayaran Zakat Diterima ✓',
            'pesan'        => "Alhamdulillah! Pembayaran zakat Anda dengan kode transaksi {$kodeTransaksi} telah dikonfirmasi dan diterima oleh amil. Semoga zakat Anda diterima oleh Allah SWT.",
            'tipe'         => 'sukses',
            'transaksi_id' => $transaksiId,
        ]);
    }

    /**
     * Buat notifikasi penolakan pembayaran
     */
    public static function buatNotifikasiTolak(int $userId, int $transaksiId, string $kodeTransaksi, string $alasan): self
    {
        return self::create([
            'user_id'      => $userId,
            'judul'        => 'Pembayaran Zakat Ditolak',
            'pesan'        => "Mohon maaf, pembayaran zakat Anda dengan kode {$kodeTransaksi} tidak dapat diproses. Alasan: {$alasan}. Silakan hubungi kami untuk informasi lebih lanjut.",
            'tipe'         => 'ditolak',
            'transaksi_id' => $transaksiId,
        ]);
    }

    /**
     * Tandai sebagai sudah dibaca
     */
    public function tandaiBaca(): bool
    {
        return $this->update(['is_read' => true]);
    }

    /**
     * Get icon berdasarkan tipe
     */
    public function getIconAttribute(): string
    {
        return match ($this->tipe) {
            'sukses'    => '✅',
            'ditolak'   => '❌',
            'peringatan' => '⚠️',
            default     => 'ℹ️',
        };
    }

    /**
     * Get color berdasarkan tipe
     */
    public function getColorAttribute(): string
    {
        return match ($this->tipe) {
            'sukses'    => 'emerald',
            'ditolak'   => 'red',
            'peringatan' => 'yellow',
            default     => 'blue',
        };
    }
}
