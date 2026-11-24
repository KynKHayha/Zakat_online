<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * Model User (Muzakki / Admin)
 *
 * Atribut PBO:
 * - idPengguna (id)
 * - nama (name)
 * - email
 * - password
 * - role (muzakki | admin)
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'phone',
        'address',
        'avatar',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    // ═══════════════════════════════════
    // RELASI
    // ═══════════════════════════════════

    public function donasiZakats()
    {
        return $this->hasMany(DonasiZakat::class);
    }

    public function transaksis()
    {
        return $this->hasMany(Transaksi::class);
    }

    public function notifikasis()
    {
        return $this->hasMany(Notifikasi::class);
    }

    // ═══════════════════════════════════
    // METHOD PBO
    // ═══════════════════════════════════

    /**
     * Registrasi user baru (muzakki)
     */
    public static function registrasi(array $data): self
    {
        return self::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => $data['password'],
            'phone'    => $data['phone'] ?? null,
            'address'  => $data['address'] ?? null,
            'role'     => 'muzakki',
        ]);
    }

    /**
     * Update profil user
     */
    public function ubahProfil(array $data): bool
    {
        return $this->update([
            'name'    => $data['name'] ?? $this->name,
            'phone'   => $data['phone'] ?? $this->phone,
            'address' => $data['address'] ?? $this->address,
            'avatar'  => $data['avatar'] ?? $this->avatar,
        ]);
    }

    /**
     * Buat donasi zakat baru
     */
    public function buatDonasiZakat(array $data): DonasiZakat
    {
        return $this->donasiZakats()->create($data);
    }

    /**
     * Lihat status pembayaran terbaru
     */
    public function lihatStatusPembayaran(): ?Transaksi
    {
        return $this->transaksis()->latest()->first();
    }

    /**
     * Lihat riwayat zakat (semua transaksi)
     */
    public function lihatRiwayatZakat()
    {
        return $this->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->latest()
            ->get();
    }

    /**
     * Cek notifikasi yang belum dibaca
     */
    public function cekNotifikasi()
    {
        return $this->notifikasis()
            ->where('is_read', false)
            ->latest()
            ->get();
    }

    /**
     * Hitung total unread notifications
     */
    public function unreadNotifikasiCount(): int
    {
        return $this->notifikasis()->where('is_read', false)->count();
    }

    /**
     * Hitung total zakat yang sudah dibayar (sukses)
     * Method PBO: totalZakatDibayar
     */
    public function totalZakatDibayar(): float
    {
        return (float) $this->transaksis()->where('status', 'sukses')->sum('total_bayar');
    }

    /**
     * Hitung total utang / zakat belum dibayar (draft + menunggu + ditolak)
     * Method PBO: totalUtang (Dzaki)
     */
    public function totalUtang(): float
    {
        return (float) $this->donasiZakats()
            ->where(function ($q) {
                $q->where('status', 'draft')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'menunggu')
                         ->whereHas('transaksi', function ($t) {
                             $t->where('status', 'menunggu')
                               ->where(function ($t2) {
                                   $t2->where('metode_bayar', 'ditempat')
                                      ->orWhere(function ($t3) {
                                          $t3->where('metode_bayar', 'online')
                                             ->whereHas('pembayaran', function ($p) {
                                                 $p->whereNull('bukti_transfer');
                                             });
                                      });
                               });
                         });
                  })
                  ->orWhereHas('transaksi', function ($t) {
                      $t->where('status', 'dibatalkan');
                  });
            })
            ->sum('jumlah_zakat');
    }

    /**
     * Daftar donasi yang belum selesai dibayar
     * Method PBO: donasiTidakLunas (Dzaki)
     */
    public function donasiTidakLunas()
    {
        return $this->donasiZakats()
            ->where(function ($q) {
                $q->where('status', 'draft')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'menunggu')
                         ->whereHas('transaksi', function ($t) {
                             $t->where('status', 'menunggu')
                               ->where(function ($t2) {
                                   $t2->where('metode_bayar', 'ditempat')
                                      ->orWhere(function ($t3) {
                                          $t3->where('metode_bayar', 'online')
                                             ->whereHas('pembayaran', function ($p) {
                                                 $p->whereNull('bukti_transfer');
                                             });
                                      });
                               });
                         });
                  })
                  ->orWhereHas('transaksi', function ($t) {
                      $t->where('status', 'dibatalkan');
                  });
            })
            ->with(['transaksi'])
            ->latest()
            ->get();
    }

    /**
     * Cek apakah user adalah admin
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah user adalah muzakki
     */
    public function isMuzakki(): bool
    {
        return $this->role === 'muzakki';
    }
}
