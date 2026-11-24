<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $totalDibayar = $user->totalZakatDibayar();
        $totalUtang = $user->totalUtang();
        $transaksiTerbaru = $user->lihatStatusPembayaran();
        $unreadNotifCount = $user->unreadNotifikasiCount();

        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
                'total_dibayar' => $totalDibayar,
                'total_utang' => $totalUtang,
                'unread_notif_count' => $unreadNotifCount,
                'transaksi_terbaru' => $transaksiTerbaru ? [
                    'id' => $transaksiTerbaru->id,
                    'kode_transaksi' => $transaksiTerbaru->kode_transaksi,
                    'status' => $transaksiTerbaru->status,
                    'total_bayar' => $transaksiTerbaru->total_bayar,
                    'tanggal' => $transaksiTerbaru->created_at->format('Y-m-d H:i:s')
                ] : null
            ]
        ]);
    }
}
