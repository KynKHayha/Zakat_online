<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class RiwayatController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // ─── Tab 1: Sudah Dibayar (status sukses) ─────
        $querySudah = $user->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->where('status', 'sukses')
            ->orderBy('diverifikasi_at', 'desc')
            ->get();

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
            ->where($utangCondition)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'sudah_bayar' => $querySudah,
                'belum_bayar' => $queryBelum,
            ]
        ]);
    }
}
