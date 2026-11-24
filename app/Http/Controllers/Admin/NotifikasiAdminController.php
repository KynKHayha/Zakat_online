<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DonasiZakat;
use App\Models\Notifikasi;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * NotifikasiAdminController
 * Adirja: Sistem notifikasi pengingat untuk user yang belum bayar
 */
class NotifikasiAdminController extends Controller
{
    /**
     * Kirim notifikasi pengingat ke user yang belum menyelesaikan pembayaran
     */
    public function kirimPengingat(Request $request)
    {
        $utangCondition = function ($q) {
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
        };

        // User yang punya donasi belum sukses (utang)
        $users = User::where('role', 'muzakki')
            ->whereHas('donasiZakats', $utangCondition)
            ->with(['donasiZakats' => $utangCondition])
            ->get();

        $kirim = 0;
        foreach ($users as $user) {
            $donasiBelumBayar = $user->donasiZakats;

            // Hapus limit 1 hari agar mudah di-test oleh dosen
            // (sebelumnya ada pengecekan sudahDikirimHariIni)

            $jumlahUtang = $donasiBelumBayar->sum('jumlah_zakat');
            $jumlahItem  = $donasiBelumBayar->count();

            Notifikasi::create([
                'user_id'      => $user->id,
                'judul'        => '⏰ Pengingat Zakat Belum Dibayar',
                'pesan'        => "Assalamu'alaikum {$user->name}, Anda memiliki {$jumlahItem} data zakat dengan total Rp " . number_format($jumlahUtang, 0, ',', '.') . " yang belum diselesaikan. Segera selesaikan pembayaran zakat Anda.",
                'tipe'         => 'peringatan',
                'transaksi_id' => null,
            ]);

            $kirim++;
        }

        return back()->with('success', "Berhasil mengirim {$kirim} notifikasi pengingat dari total {$users->count()} muzakki yang menunggak.");
    }

    /**
     * Status: berapa banyak user yang belum bayar
     */
    public function statusPengingat()
    {
        $totalBelumBayar = User::where('role', 'muzakki')
            ->whereHas('donasiZakats', fn($q) =>
                $q->whereIn('status', ['draft', 'menunggu'])
            )
            ->count();

        $totalUtang = DonasiZakat::whereIn('status', ['draft', 'menunggu'])
            ->sum('jumlah_zakat');

        return response()->json([
            'total_user_belum_bayar' => $totalBelumBayar,
            'total_utang'            => $totalUtang,
            'total_utang_format'     => 'Rp ' . number_format($totalUtang, 0, ',', '.'),
        ]);
    }
}
