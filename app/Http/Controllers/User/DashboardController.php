<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    /**
     * Tampilkan dashboard user (muzakki)
     */
    public function index()
    {
        $user = Auth::user();

        $totalTransaksi = $user->transaksis()->count();
        $transaksiSukses = $user->transaksis()->where('status', 'sukses')->count();
        $transaksiMenunggu = $user->transaksis()->where('status', 'menunggu')->count();
        $totalZakatDibayar = $user->transaksis()
            ->where('status', 'sukses')
            ->sum('total_bayar');

        $transaksiTerbaru = $user->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->latest()
            ->take(5)
            ->get();

        $unreadNotifikasi = $user->unreadNotifikasiCount();

        // Dzaki: Data utang / zakat belum dibayar
        $totalUtang  = $user->totalUtang();
        $daftarUtang = $user->donasiTidakLunas();

        // Info nisab
        $nisabMal    = 85 * 1000000;
        $hargaBeras  = 15000;

        return view('user.dashboard', compact(
            'user',
            'totalTransaksi',
            'transaksiSukses',
            'transaksiMenunggu',
            'totalZakatDibayar',
            'transaksiTerbaru',
            'unreadNotifikasi',
            'nisabMal',
            'hargaBeras',
            'totalUtang',
            'daftarUtang'
        ));
    }

    /**
     * Halaman profil user
     */
    public function profil()
    {
        $user = Auth::user();
        return view('user.profil', compact('user'));
    }

    /**
     * Update profil user
     */
    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'    => ['required', 'string', 'max:255'],
            'phone'   => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        $user->ubahProfil($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }
}
