<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * LaporanUserController
 * Rijal: Laporan pribadi user — view + export PDF
 */
class LaporanUserController extends Controller
{
    /**
     * Halaman laporan pribadi muzakki
     */
    public function index(Request $request)
    {
        $user  = Auth::user();

        $query = $user->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->where('status', 'sukses');

        // Filter periode
        $dariTanggal   = $request->dari_tanggal ?? now()->startOfYear()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->format('Y-m-d');

        $query->whereDate('diverifikasi_at', '>=', $dariTanggal)
              ->whereDate('diverifikasi_at', '<=', $sampaiTanggal);

        // Filter jenis zakat
        if ($request->filled('jenis_zakat')) {
            $query->whereHas('donasiZakat', fn($q) =>
                $q->where('jenis_zakat', $request->jenis_zakat)
            );
        }

        $transaksis  = $query->orderBy('diverifikasi_at', 'desc')->get();
        $grandTotal  = $transaksis->sum('total_bayar');

        // Subtotal per jenis
        $subtotalPerJenis = $transaksis
            ->groupBy(fn($t) => $t->donasiZakat?->jenis_zakat ?? 'lainnya')
            ->map(fn($group) => [
                'label'  => $group->first()->donasiZakat?->label_jenis_zakat ?? 'Lainnya',
                'total'  => $group->sum('total_bayar'),
                'jumlah' => $group->count(),
            ]);

        // Total utang (belum bayar) — Dzaki integration
        $totalUtang  = $user->totalUtang();
        $daftarUtang = $user->donasiTidakLunas();

        return view('user.laporan.index', compact(
            'user', 'transaksis', 'grandTotal', 'subtotalPerJenis',
            'dariTanggal', 'sampaiTanggal',
            'totalUtang', 'daftarUtang'
        ));
    }

    /**
     * Export laporan pribadi ke PDF
     */
    public function exportPdf(Request $request)
    {
        $user  = Auth::user();

        $query = $user->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->where('status', 'sukses');

        $dariTanggal   = $request->dari_tanggal ?? now()->startOfYear()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->format('Y-m-d');

        $query->whereDate('diverifikasi_at', '>=', $dariTanggal)
              ->whereDate('diverifikasi_at', '<=', $sampaiTanggal);

        if ($request->filled('jenis_zakat')) {
            $query->whereHas('donasiZakat', fn($q) =>
                $q->where('jenis_zakat', $request->jenis_zakat)
            );
        }

        $transaksis = $query->orderBy('diverifikasi_at', 'desc')->get();
        $grandTotal = $transaksis->sum('total_bayar');

        $subtotalPerJenis = $transaksis
            ->groupBy(fn($t) => $t->donasiZakat?->jenis_zakat ?? 'lainnya')
            ->map(fn($group) => [
                'label'  => $group->first()->donasiZakat?->label_jenis_zakat ?? 'Lainnya',
                'total'  => $group->sum('total_bayar'),
                'jumlah' => $group->count(),
            ]);

        $pdf = Pdf::loadView('user.laporan.pdf', compact(
            'user', 'transaksis', 'grandTotal', 'subtotalPerJenis',
            'dariTanggal', 'sampaiTanggal'
        ))->setPaper('a4', 'portrait');

        $filename = 'laporan-zakat-' . $user->name . '-' . $dariTanggal . '-sd-' . $sampaiTanggal . '.pdf';

        return $pdf->download($filename);
    }
}
