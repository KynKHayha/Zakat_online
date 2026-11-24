<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    /**
     * Halaman laporan keuangan dengan filter date range
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'donasiZakat', 'pembayaran'])
            ->where('status', 'sukses');

        // Filter tanggal (Date Range Picker)
        $dariTanggal   = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->format('Y-m-d');

        $query->whereDate('diverifikasi_at', '>=', $dariTanggal)
              ->whereDate('diverifikasi_at', '<=', $sampaiTanggal);

        // Filter jenis zakat
        if ($request->filled('jenis_zakat')) {
            $query->whereHas('donasiZakat', fn ($q) => $q->where('jenis_zakat', $request->jenis_zakat));
        }

        // Filter metode bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $transaksis = $query->orderBy('diverifikasi_at', 'desc')->get();

        // Grand total dari hasil filter
        $grandTotal = $transaksis->sum('total_bayar');

        // Subtotal per metode bayar
        $totalOnline   = $transaksis->where('metode_bayar', 'online')->sum('total_bayar');
        $totalDitempat = $transaksis->where('metode_bayar', 'ditempat')->sum('total_bayar');

        // Subtotal per jenis zakat
        $subtotalPerJenis = $transaksis->groupBy(fn ($t) => $t->donasiZakat?->jenis_zakat ?? 'unknown')
            ->map(fn ($group) => [
                'jenis'  => $group->first()->donasiZakat?->label_jenis_zakat ?? 'Unknown',
                'total'  => $group->sum('total_bayar'),
                'jumlah' => $group->count(),
            ]);

        return view('admin.laporan.index', compact(
            'transaksis',
            'grandTotal',
            'totalOnline',
            'totalDitempat',
            'subtotalPerJenis',
            'dariTanggal',
            'sampaiTanggal'
        ));
    }

    /**
     * Export laporan ke PDF
     */
    public function exportPdf(Request $request)
    {
        $query = Transaksi::with(['user', 'donasiZakat', 'pembayaran'])
            ->where('status', 'sukses');

        $dariTanggal   = $request->dari_tanggal ?? now()->startOfMonth()->format('Y-m-d');
        $sampaiTanggal = $request->sampai_tanggal ?? now()->format('Y-m-d');

        $query->whereDate('diverifikasi_at', '>=', $dariTanggal)
              ->whereDate('diverifikasi_at', '<=', $sampaiTanggal);

        if ($request->filled('jenis_zakat')) {
            $query->whereHas('donasiZakat', fn ($q) => $q->where('jenis_zakat', $request->jenis_zakat));
        }

        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        $transaksis    = $query->orderBy('diverifikasi_at', 'desc')->get();
        $grandTotal    = $transaksis->sum('total_bayar');
        $totalOnline   = $transaksis->where('metode_bayar', 'online')->sum('total_bayar');
        $totalDitempat = $transaksis->where('metode_bayar', 'ditempat')->sum('total_bayar');

        $pdf = Pdf::loadView('admin.laporan.pdf', compact(
            'transaksis',
            'grandTotal',
            'totalOnline',
            'totalDitempat',
            'dariTanggal',
            'sampaiTanggal'
        ))->setPaper('a4', 'landscape');

        $filename = 'laporan-zakat-' . $dariTanggal . '-sd-' . $sampaiTanggal . '.pdf';

        return $pdf->download($filename);
    }

    /**
     * Cetak Laporan PDF per Muzakki
     */
    public function cetakMuzakkiPdf(\App\Models\User $user)
    {
        $transaksis = Transaksi::with(['donasiZakat', 'pembayaran'])
            ->where('user_id', $user->id)
            ->where('status', 'sukses')
            ->orderBy('diverifikasi_at', 'desc')
            ->get();

        $totalDibayar   = $transaksis->sum('total_bayar');
        $totalTransaksi = $transaksis->count();

        // Variabel yang dipakai view user.laporan.pdf
        $subtotalPerJenis = $transaksis
            ->groupBy(fn ($t) => $t->donasiZakat?->jenis_zakat ?? 'lainnya')
            ->map(fn ($group) => [
                'label'  => $group->first()->donasiZakat?->label_jenis_zakat ?? 'Lainnya',
                'total'  => $group->sum('total_bayar'),
                'jumlah' => $group->count(),
            ]);

        $pdf = Pdf::loadView('user.laporan.pdf', [
            'user'             => $user,
            'transaksis'       => $transaksis,
            'grandTotal'       => $totalDibayar,
            'subtotalPerJenis' => $subtotalPerJenis,
            'dariTanggal'      => $transaksis->last()?->diverifikasi_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'sampaiTanggal'    => $transaksis->first()?->diverifikasi_at?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'totalTransaksi'   => $totalTransaksi,
        ])->setPaper('a4', 'portrait');

        $filename = 'laporan-riwayat-zakat-' . \Illuminate\Support\Str::slug($user->name) . '.pdf';

        return $pdf->download($filename);
    }
}
