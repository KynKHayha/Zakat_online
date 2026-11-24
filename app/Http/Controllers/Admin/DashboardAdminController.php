<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use App\Models\User;
use App\Models\DonasiZakat;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    /**
     * Dashboard Admin (Amil)
     * Dino: Tambah data per jenis zakat
     */
    public function index()
    {
        // ─── Metrik Utama ─────────────────────────────
        $totalUser         = User::where('role', 'muzakki')->count();
        $totalTransaksi    = Transaksi::count();
        $transaksiMenunggu = Transaksi::where('status', 'menunggu')->count();
        $transaksiSukses   = Transaksi::where('status', 'sukses')->count();

        // Total saldo & saldo bulan ini
        $totalSaldo    = Transaksi::where('status', 'sukses')->sum('total_bayar');
        $saldoBulanIni = Transaksi::where('status', 'sukses')
            ->whereMonth('diverifikasi_at', now()->month)
            ->whereYear('diverifikasi_at', now()->year)
            ->sum('total_bayar');

        // ─── 6 Kotak Jenis Zakat (Dino) ──────────────
        $jenisZakatList = ['fitrah', 'mal', 'profesi', 'perniagaan', 'pertanian', 'emas_perak'];

        $statPerJenis = [];
        foreach ($jenisZakatList as $jenis) {
            $statPerJenis[$jenis] = [
                'jenis'       => $jenis,
                'label'       => $this->labelJenis($jenis),
                'icon'        => $this->iconJenis($jenis),
                'color'       => $this->colorJenis($jenis),
                'total_muzakki' => DonasiZakat::where('jenis_zakat', $jenis)
                    ->where('status', 'sukses')->count(),
                'total_nominal' => DonasiZakat::where('jenis_zakat', $jenis)
                    ->where('status', 'sukses')->sum('jumlah_zakat'),
                'menunggu'    => DonasiZakat::where('jenis_zakat', $jenis)
                    ->where('status', 'menunggu')->count(),
            ];
        }

        // ─── Transaksi Terbaru (menunggu) ─────────────
        $transaksiTerbaru = Transaksi::with(['user', 'donasiZakat', 'pembayaran'])
            ->where('status', 'menunggu')
            ->latest()
            ->take(10)
            ->get();

        // ─── Chart Bulanan ─────────────────────────────
        $chartData = Transaksi::where('status', 'sukses')
            ->selectRaw('MONTH(diverifikasi_at) as bulan, YEAR(diverifikasi_at) as tahun, SUM(total_bayar) as total')
            ->whereYear('diverifikasi_at', now()->year)
            ->groupBy('bulan', 'tahun')
            ->orderBy('bulan')
            ->get();

        // ─── Distribusi Jenis Zakat ────────────────────
        $distribusiZakat = DonasiZakat::where('status', 'sukses')
            ->selectRaw('jenis_zakat, COUNT(*) as jumlah, SUM(jumlah_zakat) as total')
            ->groupBy('jenis_zakat')
            ->get();

        // ─── Online vs Ditempat ────────────────────────
        $onlineCount    = Transaksi::where('metode_bayar', 'online')->where('status', 'sukses')->count();
        $dittempatCount = Transaksi::where('metode_bayar', 'ditempat')->where('status', 'sukses')->count();

        // ─── User belum bayar (untuk pengingat) ────────
        $userBelumBayar = User::where('role', 'muzakki')
            ->whereHas('donasiZakats', fn($q) => $q->whereNotIn('status', ['sukses']))
            ->count();

        return view('admin.dashboard', compact(
            'totalUser', 'totalTransaksi', 'transaksiMenunggu', 'transaksiSukses',
            'totalSaldo', 'saldoBulanIni', 'transaksiTerbaru', 'chartData',
            'distribusiZakat', 'onlineCount', 'dittempatCount',
            'statPerJenis', 'userBelumBayar'
        ));
    }

    /**
     * AJAX: Detail daftar muzakki berdasarkan filter (Dino)
     * GET /admin/dashboard/detail?filter=semua|menunggu|sukses|fitrah|mal|...
     */
    public function detail(Request $request)
    {
        $filter = $request->get('filter', 'semua');

        $query = DonasiZakat::with(['user', 'transaksi'])->latest();

        switch ($filter) {
            case 'semua':
                // Semua muzakki terdaftar
                $data = User::where('role', 'muzakki')
                    ->with(['transaksis' => fn($q) => $q->latest()->take(1)])
                    ->withCount('transaksis')
                    ->withSum(['transaksis' => fn($q) => $q->where('status', 'sukses')], 'total_bayar')
                    ->latest()
                    ->get()
                    ->map(fn($u) => [
                        'nama'   => $u->name,
                        'email'  => $u->email,
                        'phone'  => $u->phone ?? '-',
                        'jenis'  => '-',
                        'jumlah' => 'Rp ' . number_format($u->transaksis_sum_total_bayar ?? 0, 0, ',', '.'),
                        'status' => $u->transaksis_count . ' transaksi',
                        'tanggal' => $u->created_at->format('d/m/Y'),
                    ]);

                return response()->json([
                    'judul' => 'Semua Muzakki Terdaftar',
                    'total' => $data->count(),
                    'data'  => $data,
                ]);

            case 'menunggu':
                $rows = Transaksi::with(['user', 'donasiZakat'])
                    ->where('status', 'menunggu')->latest()->get();
                break;

            case 'sukses':
                $rows = Transaksi::with(['user', 'donasiZakat'])
                    ->where('status', 'sukses')->latest()->get();
                break;

            default:
                // Jenis zakat spesifik
                $rows = DonasiZakat::with(['user', 'transaksi'])
                    ->where('jenis_zakat', $filter)
                    ->where('status', 'sukses')
                    ->latest()
                    ->get()
                    ->map(fn($d) => [
                        'nama'    => $d->user?->name ?? '-',
                        'email'   => $d->user?->email ?? '-',
                        'phone'   => $d->user?->phone ?? '-',
                        'jenis'   => $this->labelJenis($d->jenis_zakat),
                        'jumlah'  => 'Rp ' . number_format($d->jumlah_zakat, 0, ',', '.'),
                        'status'  => ucfirst($d->status),
                        'tanggal' => $d->created_at->format('d/m/Y'),
                    ]);

                $labelJenis = [
                    'fitrah' => 'Zakat Fitrah', 'mal' => 'Zakat Mal',
                    'profesi' => 'Zakat Profesi', 'perniagaan' => 'Zakat Perniagaan',
                    'pertanian' => 'Zakat Pertanian', 'emas_perak' => 'Zakat Emas & Perak',
                ];

                return response()->json([
                    'judul' => 'Detail ' . ($labelJenis[$filter] ?? $filter),
                    'total' => $rows->count(),
                    'data'  => $rows,
                ]);
        }

        // Untuk menunggu / sukses
        $data = $rows->map(fn($t) => [
            'nama'    => $t->user?->name ?? '-',
            'email'   => $t->user?->email ?? '-',
            'phone'   => $t->user?->phone ?? '-',
            'jenis'   => $t->donasiZakat?->label_jenis_zakat ?? '-',
            'jumlah'  => 'Rp ' . number_format($t->total_bayar, 0, ',', '.'),
            'status'  => $t->label_status,
            'tanggal' => $t->tanggal_transaksi?->format('d/m/Y') ?? '-',
        ]);

        $labelFilter = ['menunggu' => 'Menunggu Verifikasi', 'sukses' => 'Transaksi Sukses'];

        return response()->json([
            'judul' => $labelFilter[$filter] ?? $filter,
            'total' => $data->count(),
            'data'  => $data,
        ]);
    }

    // ─── Helpers ───────────────────────────────────────────
    private function labelJenis(string $jenis): string
    {
        return match ($jenis) {
            'fitrah'     => 'Zakat Fitrah',
            'mal'        => 'Zakat Mal',
            'profesi'    => 'Zakat Profesi',
            'perniagaan' => 'Zakat Perniagaan',
            'pertanian'  => 'Zakat Pertanian',
            'emas_perak' => 'Zakat Emas & Perak',
            default      => ucfirst($jenis),
        };
    }

    private function iconJenis(string $jenis): string
    {
        return match ($jenis) {
            'fitrah'     => '🌾',
            'mal'        => '💰',
            'profesi'    => '💼',
            'perniagaan' => '🏪',
            'pertanian'  => '🌱',
            'emas_perak' => '🥇',
            default      => '📦',
        };
    }

    private function colorJenis(string $jenis): string
    {
        return match ($jenis) {
            'fitrah'     => 'emerald',
            'mal'        => 'blue',
            'profesi'    => 'purple',
            'perniagaan' => 'orange',
            'pertanian'  => 'green',
            'emas_perak' => 'yellow',
            default      => 'gray',
        };
    }
}
