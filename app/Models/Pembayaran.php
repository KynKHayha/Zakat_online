<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model Pembayaran
 *
 * Atribut PBO:
 * - idPembayaran (id)
 * - idTransaksi (transaksi_id)
 * - metodePembayaran (metode_pembayaran)
 * - tanggalPembayaran (tanggal_pembayaran)
 * - totalBayar (total_bayar)
 */
class Pembayaran extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaksi_id',
        'metode_pembayaran',
        'nama_bank',
        'nomor_rekening',
        'tanggal_pembayaran',
        'total_bayar',
        'bukti_transfer',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'datetime',
        'total_bayar'        => 'decimal:2',
    ];

    // ═══════════════════════════════════
    // RELASI
    // ═══════════════════════════════════

    public function transaksi()
    {
        return $this->belongsTo(Transaksi::class);
    }

    // ═══════════════════════════════════
    // METHOD PBO
    // ═══════════════════════════════════

    /**
     * Proses pembayaran baru
     */
    public static function prosesPembayaran(array $data): self
    {
        $data['tanggal_pembayaran'] = now();
        $data['status']             = 'pending';

        return self::create($data);
    }

    /**
     * Konfirmasi pembayaran (admin)
     */
    public function konfirmasiPembayaran(): bool
    {
        return $this->updateStatusPembayaran('dikonfirmasi');
    }

    /**
     * Update status pembayaran
     */
    public function updateStatusPembayaran(string $status): bool
    {
        return $this->update(['status' => $status]);
    }

    /**
     * Lihat detail pembayaran dengan relasi
     */
    public function lihatDetailPembayaran(): self
    {
        return $this->load(['transaksi.donasiZakat', 'transaksi.user']);
    }

    /**
     * Get URL bukti transfer
     */
    public function getBuktiTransferUrlAttribute(): ?string
    {
        return $this->bukti_transfer
            ? asset('storage/' . $this->bukti_transfer)
            : null;
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'      => 'yellow',
            'dikonfirmasi' => 'green',
            'ditolak'      => 'red',
            default        => 'gray',
        };
    }
}
