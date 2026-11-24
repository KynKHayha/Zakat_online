<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Model Transaksi
 *
 * Atribut PBO:
 * - idTransaksi (id)
 * - idDonasi (donasi_zakat_id)
 * - idPengguna (user_id)
 * - tanggal (tanggal_transaksi)
 * - status
 * - totalBayar (total_bayar)
 * - metodeBayar (metode_bayar)
 */
class Transaksi extends Model
{
    use HasFactory;

    protected $fillable = [
        'donasi_zakat_id',
        'user_id',
        'kode_transaksi',
        'tanggal_transaksi',
        'tanggal_rencana_bayar',
        'batas_waktu_bayar',
        'metode_bayar',
        'bank_tujuan',
        'total_bayar',
        'status',
        'alasan_tolak',
        'diverifikasi_at',
        'diverifikasi_oleh',
    ];

    protected $casts = [
        'tanggal_transaksi'     => 'datetime',
        'tanggal_rencana_bayar' => 'date',
        'batas_waktu_bayar'     => 'datetime',
        'diverifikasi_at'       => 'datetime',
        'total_bayar'           => 'decimal:2',
    ];

    // ═══════════════════════════════════
    // RELASI
    // ═══════════════════════════════════

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function donasiZakat()
    {
        return $this->belongsTo(DonasiZakat::class);
    }

    public function pembayaran()
    {
        return $this->hasOne(Pembayaran::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    public function diverifikasiOleh()
    {
        return $this->belongsTo(User::class, 'diverifikasi_oleh');
    }

    // ═══════════════════════════════════
    // METHOD PBO
    // ═══════════════════════════════════

    /**
     * Generate kode transaksi unik
     */
    public static function generateKode(): string
    {
        do {
            $kode = 'ZKT-' . strtoupper(Str::random(8));
        } while (self::where('kode_transaksi', $kode)->exists());

        return $kode;
    }

    /**
     * Buat transaksi baru
     */
    public static function buatTransaksi(array $data): self
    {
        $data['kode_transaksi']    = self::generateKode();
        $data['tanggal_transaksi'] = now();
        $data['status']            = 'menunggu';

        return self::create($data);
    }

    /**
     * Ubah status transaksi
     */
    public function ubahStatus(string $status, ?string $alasan = null): bool
    {
        $update = ['status' => $status];
        if ($alasan) {
            $update['alasan_tolak'] = $alasan;
        }
        if (in_array($status, ['sukses', 'ditolak'])) {
            $update['diverifikasi_at'] = now();
        }
        return $this->update($update);
    }

    /**
     * Batalkan transaksi
     */
    public function batalTransaksi(): bool
    {
        if ($this->status === 'menunggu') {
            $this->donasiZakat?->ubahStatusDonasi('draft');
            return $this->update(['status' => 'dibatalkan']);
        }
        return false;
    }

    /**
     * Lihat detail transaksi dengan semua relasi
     */
    public function lihatDetailTransaksi(): self
    {
        return $this->load(['donasiZakat', 'user', 'pembayaran', 'diverifikasiOleh']);
    }

    /**
     * Get status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'yellow',
            'sukses'     => 'green',
            'ditolak'    => 'red',
            'dibatalkan' => 'gray',
            default      => 'gray',
        };
    }

    /**
     * Get label status bahasa Indonesia
     */
    public function getLabelStatusAttribute(): string
    {
        return match ($this->status) {
            'menunggu'   => 'Menunggu Verifikasi',
            'sukses'     => 'Berhasil',
            'ditolak'    => 'Ditolak',
            'dibatalkan' => 'Dibatalkan',
            default      => ucfirst($this->status),
        };
    }

    /**
     * Get label metode bayar
     */
    public function getLabelMetodeBayarAttribute(): string
    {
        return match ($this->metode_bayar) {
            'online'   => 'Bayar Online',
            'ditempat' => 'Bayar di Tempat',
            default    => ucfirst($this->metode_bayar),
        };
    }
}
