<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model DonasiZakat
 *
 * Atribut PBO:
 * - idDonasi (id)
 * - idPengguna (user_id)
 * - nama (nama_muzakki)
 * - tanggalDiZakat (created_at)
 * - jumlahZakat (jumlah_zakat)
 * - status
 */
class DonasiZakat extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'jenis_zakat',
        'nama_muzakki',
        'jumlah_jiwa',
        'harga_beras',
        'penghasilan',
        'nilai_aset',
        'jumlah_zakat',
        'keterangan',
        'status',
    ];

    protected $casts = [
        'jumlah_jiwa'  => 'integer',
        'harga_beras'  => 'decimal:2',
        'penghasilan'  => 'decimal:2',
        'nilai_aset'   => 'decimal:2',
        'jumlah_zakat' => 'decimal:2',
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
        return $this->hasOne(Transaksi::class);
    }

    // ═══════════════════════════════════
    // METHOD PBO
    // ═══════════════════════════════════

    /**
     * Buat donasi baru
     */
    public static function buatDonasi(array $data): self
    {
        return self::create($data);
    }

    /**
     * Hitung jumlah zakat berdasarkan jenis
     * Nisab emas 85 gram = ~Rp 85.000.000 (harga emas ~1jt/gram)
     */
    public static function hitungJumlahZakat(string $jenis, array $params): float
    {
        $jumlah = match ($jenis) {
            'fitrah' => self::hitungZakatFitrah(
                $params['jumlah_jiwa'] ?? 1,
                $params['harga_beras'] ?? 15000
            ),
            'mal' => self::hitungZakatMal(
                max(0, ($params['total_harta'] ?? 0) - ($params['total_utang'] ?? 0))
            ),
            'profesi' => self::hitungZakatProfesi(
                max(0, ($params['penghasilan_bruto'] ?? 0) - ($params['pengeluaran_pokok'] ?? 0))
            ),
            'perniagaan' => self::hitungZakatPerniagaan(
                max(0, ($params['nilai_aset'] ?? 0) + ($params['piutang'] ?? 0) - ($params['utang_jatuh_tempo'] ?? 0))
            ),
            'pertanian' => self::hitungZakatPertanian(
                $params['hasil_panen'] ?? 0,
                $params['irigasi'] ?? 'irigasi'
            ),
            'emas_perak' => self::hitungZakatEmas(
                ($params['berat_emas'] ?? 0) * ($params['harga_emas'] ?? 0)
            ),
            default => 0,
        };

        return round($jumlah);
    }

    private static function hitungZakatFitrah(int $jiwa, float $hargaBeras): float
    {
        // 2.5 kg beras per jiwa
        return $jiwa * 2.5 * $hargaBeras;
    }

    private static function hitungZakatMal(float $hartaBersih): float
    {
        // 2.5% dari harta bersih (asumsi sudah dicek nisab)
        return $hartaBersih * 0.025;
    }

    private static function hitungZakatProfesi(float $penghasilanBersih): float
    {
        // 2.5% dari penghasilan bersih
        return $penghasilanBersih * 0.025;
    }

    private static function hitungZakatPerniagaan(float $asetBersih): float
    {
        // 2.5% dari nilai aset dagang bersih
        return $asetBersih * 0.025;
    }

    private static function hitungZakatPertanian(float $hasilPanen, string $irigasi): float
    {
        // 5% jika menggunakan irigasi, 10% jika tadah hujan
        return $hasilPanen * ($irigasi === 'tadah_hujan' ? 0.10 : 0.05);
    }

    private static function hitungZakatEmas(float $totalNilaiEmas): float
    {
        // 2.5% dari nilai emas/perak
        return $totalNilaiEmas * 0.025;
    }

    /**
     * Ubah status donasi
     */
    public function ubahStatusDonasi(string $status): bool
    {
        $validStatus = ['draft', 'menunggu', 'sukses', 'ditolak'];
        if (!in_array($status, $validStatus)) {
            return false;
        }
        return $this->update(['status' => $status]);
    }

    /**
     * Lihat detail donasi dengan relasi
     */
    public function lihatDetailDonasi(): self
    {
        return $this->load(['user', 'transaksi.pembayaran']);
    }

    /**
     * Get label jenis zakat
     */
    public function getLabelJenisZakatAttribute(): string
    {
        return match ($this->jenis_zakat) {
            'fitrah'      => 'Zakat Fitrah',
            'mal'         => 'Zakat Mal',
            'profesi'     => 'Zakat Profesi',
            'perniagaan'  => 'Zakat Perniagaan',
            'pertanian'   => 'Zakat Pertanian',
            'emas_perak'  => 'Zakat Emas & Perak',
            default       => ucfirst($this->jenis_zakat),
        };
    }

    /**
     * Get badge color untuk status
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'    => 'gray',
            'menunggu' => 'yellow',
            'sukses'   => 'green',
            'ditolak'  => 'red',
            default    => 'gray',
        };
    }
}
