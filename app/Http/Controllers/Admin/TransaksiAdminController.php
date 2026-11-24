<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Transaksi;
use App\Mail\PembayaranDiterimaMail;
use App\Mail\PembayaranDitolakMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class TransaksiAdminController extends Controller
{
    /**
     * Daftar semua transaksi
     */
    public function index(Request $request)
    {
        $query = Transaksi::with(['user', 'donasiZakat', 'pembayaran'])
            ->latest();

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis zakat
        if ($request->filled('jenis_zakat')) {
            $query->whereHas('donasiZakat', function ($q) use ($request) {
                $q->where('jenis_zakat', $request->jenis_zakat);
            });
        }

        // Filter metode bayar
        if ($request->filled('metode_bayar')) {
            $query->where('metode_bayar', $request->metode_bayar);
        }

        // Filter tanggal
        if ($request->filled('dari_tanggal')) {
            $query->whereDate('tanggal_transaksi', '>=', $request->dari_tanggal);
        }
        if ($request->filled('sampai_tanggal')) {
            $query->whereDate('tanggal_transaksi', '<=', $request->sampai_tanggal);
        }

        // Pencarian nama/kode
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('kode_transaksi', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', fn ($u) => $u->where('name', 'LIKE', "%{$search}%"))
                    ->orWhereHas('donasiZakat', fn ($d) => $d->where('nama_muzakki', 'LIKE', "%{$search}%"));
            });
        }

        $transaksis        = $query->paginate(15);
        $totalMenunggu     = Transaksi::where('status', 'menunggu')->count();
        $totalSaldo        = Transaksi::where('status', 'sukses')->sum('total_bayar');

        return view('admin.transaksi.index', compact('transaksis', 'totalMenunggu', 'totalSaldo'));
    }

    /**
     * Detail transaksi
     */
    public function show(Transaksi $transaksi)
    {
        $transaksi->lihatDetailTransaksi();
        return view('admin.transaksi.show', compact('transaksi'));
    }

    /**
     * Terima (verifikasi) transaksi
     */
    public function terima(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->status !== 'menunggu') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        DB::beginTransaction();
        try {
            // Ubah status transaksi
            $transaksi->update([
                'status'            => 'sukses',
                'diverifikasi_at'   => now(),
                'diverifikasi_oleh' => Auth::id(),
            ]);

            // Update status donasi
            $transaksi->donasiZakat->ubahStatusDonasi('sukses');

            // Update status pembayaran
            $transaksi->pembayaran?->konfirmasiPembayaran();

            // Buat notifikasi in-app
            Notifikasi::buatNotifikasiSukses(
                $transaksi->user_id,
                $transaksi->id,
                $transaksi->kode_transaksi
            );

            // Kirim email
            try {
                Mail::to($transaksi->user->email)
                    ->send(new PembayaranDiterimaMail($transaksi));
            } catch (\Exception $e) {
                // Log error email tapi jangan gagalkan transaksi
                \Log::warning('Gagal kirim email terima: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('admin.transaksi.index')
                ->with('success', "Transaksi {$transaksi->kode_transaksi} berhasil diverifikasi.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memverifikasi transaksi: ' . $e->getMessage());
        }
    }

    /**
     * Tolak transaksi
     */
    public function tolak(Request $request, Transaksi $transaksi)
    {
        if ($transaksi->status !== 'menunggu') {
            return back()->with('error', 'Transaksi ini sudah diproses sebelumnya.');
        }

        $request->validate([
            'alasan_tolak' => ['required', 'string', 'min:10', 'max:500'],
        ], [
            'alasan_tolak.required' => 'Alasan penolakan wajib diisi.',
            'alasan_tolak.min'      => 'Alasan minimal 10 karakter.',
        ]);

        DB::beginTransaction();
        try {
            // Ubah status transaksi
            $transaksi->update([
                'status'            => 'ditolak',
                'alasan_tolak'      => $request->alasan_tolak,
                'diverifikasi_at'   => now(),
                'diverifikasi_oleh' => Auth::id(),
            ]);

            // Update status donasi
            $transaksi->donasiZakat->ubahStatusDonasi('ditolak');

            // Update status pembayaran
            $transaksi->pembayaran?->updateStatusPembayaran('ditolak');

            // Buat notifikasi in-app
            Notifikasi::buatNotifikasiTolak(
                $transaksi->user_id,
                $transaksi->id,
                $transaksi->kode_transaksi,
                $request->alasan_tolak
            );

            // Kirim email
            try {
                Mail::to($transaksi->user->email)
                    ->send(new PembayaranDitolakMail($transaksi, $request->alasan_tolak));
            } catch (\Exception $e) {
                \Log::warning('Gagal kirim email tolak: ' . $e->getMessage());
            }

            DB::commit();

            return redirect()->route('admin.transaksi.index')
                ->with('warning', "Transaksi {$transaksi->kode_transaksi} telah ditolak.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menolak transaksi: ' . $e->getMessage());
        }
    }
}
