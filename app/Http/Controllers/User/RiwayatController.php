<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * RiwayatController
 * Adirja: Tabel terpisah — Sudah Bayar vs Belum/Utang
 */
class RiwayatController extends Controller
{
    /**
     * Halaman riwayat zakat dengan tab terpisah
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $tab  = $request->get('tab', 'sudah_bayar'); // sudah_bayar | belum_bayar

        // ─── Tab 1: Sudah Dibayar (status sukses) ─────
        $querySudah = $user->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->where('status', 'sukses');

        if ($request->filled('jenis_zakat')) {
            $querySudah->whereHas('donasiZakat', fn($q) =>
                $q->where('jenis_zakat', $request->jenis_zakat)
            );
        }
        if ($request->filled('dari_tanggal')) {
            $querySudah->whereDate('diverifikasi_at', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $querySudah->whereDate('diverifikasi_at', '<=', $request->sampai_tanggal);
        }

        $transaksiSudahBayar = $querySudah->orderBy('diverifikasi_at', 'desc')->paginate(10, ['*'], 'sudah_page');
        $totalSudahBayar     = $user->transaksis()->where('status', 'sukses')->sum('total_bayar');
        $countSudahBayar     = $user->transaksis()->where('status', 'sukses')->count();

        // ─── Tab 2: Belum/Masih Utang ───
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

        $queryBelum = $user->donasiZakats()
            ->with(['transaksi'])
            ->where($utangCondition);

        if ($request->filled('jenis_zakat')) {
            $queryBelum->where('jenis_zakat', $request->jenis_zakat);
        }
        if ($request->filled('status_belum')) {
            $queryBelum->where('status', $request->status_belum);
        }

        $donasiUtang   = $queryBelum->orderBy('created_at', 'desc')->paginate(10, ['*'], 'belum_page');
        $totalUtang    = $user->totalUtang();
        $countUtang    = $user->donasiZakats()->where($utangCondition)->count();

        // ─── Summary total ─────────────────────────────
        $totalKeseluruhan = $totalSudahBayar;

        return view('user.riwayat.index', compact(
            'tab',
            'transaksiSudahBayar',
            'totalSudahBayar',
            'countSudahBayar',
            'donasiUtang',
            'totalUtang',
            'countUtang',
            'totalKeseluruhan'
        ));
    }
}
