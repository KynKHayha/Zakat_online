<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\DonasiZakat;
use App\Models\Notifikasi;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Mail\PembayaranDiterimaMail;
use App\Mail\PembayaranDitolakMail;
use Illuminate\Support\Facades\Mail;

class DonasiController extends Controller
{
    /**
     * Tampilkan form buat donasi
     */
    public function create()
    {
        $user = Auth::user();
        return view('user.donasi.create', compact('user'));
    }

    /**
     * Hitung zakat via AJAX
     */
    public function hitungZakat(Request $request)
    {
        $request->validate([
            'jenis_zakat' => ['required', 'string'],
        ]);

        $jenis = $request->jenis_zakat;
        // Normalize: frontend bisa kirim 'emas', model expect 'emas_perak'
        if ($jenis === 'emas') $jenis = 'emas_perak';

        $jumlah = DonasiZakat::hitungJumlahZakat($jenis, $request->all());

        return response()->json([
            'success'             => true,
            'jumlah'              => $jumlah,
            'jumlah_zakat'        => $jumlah,
            'jumlah_zakat_format' => 'Rp ' . number_format($jumlah, 0, ',', '.'),
            'keterangan'          => 'Dihitung sesuai ketentuan syariah.',
        ]);
    }

    /**
     * Simpan donasi dan lanjut ke pembayaran
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_zakat'   => ['required', 'in:fitrah,mal,profesi,perniagaan,pertanian,emas_perak'],
            'nama_muzakki'  => ['required', 'string', 'max:255'],
            'jumlah_zakat'  => ['required', 'numeric', 'min:10000', 'max:1000000000'],
            'jumlah_jiwa'   => ['nullable', 'integer', 'min:1', 'max:100'],
            'harga_beras'   => ['nullable', 'numeric', 'min:0'],
            'penghasilan'   => ['nullable', 'numeric', 'min:0'],
            'nilai_aset'    => ['nullable', 'numeric', 'min:0'],
            'keterangan'    => ['nullable', 'string', 'max:500'],
        ], [
            'jenis_zakat.required'  => 'Jenis zakat wajib dipilih.',
            'jenis_zakat.in'        => 'Jenis zakat tidak valid.',
            'nama_muzakki.required' => 'Nama muzakki wajib diisi.',
            'jumlah_zakat.required' => 'Jumlah zakat wajib diisi.',
            'jumlah_zakat.numeric'  => 'Jumlah zakat harus berupa angka.',
            'jumlah_zakat.min'      => 'Jumlah zakat minimal Rp 10.000. (Input Anda terlalu kecil)',
            'jumlah_zakat.max'      => 'Jumlah zakat maksimal Rp 1.000.000.000 per transaksi.',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status']  = 'draft';

        $donasi = DonasiZakat::buatDonasi($validated);

        return redirect()->route('user.donasi.bayar', $donasi->id)
            ->with('success', 'Data zakat berhasil disimpan. Silakan pilih metode pembayaran.');
    }

    /**
     * Halaman pilih metode pembayaran
     */
    public function bayar(DonasiZakat $donasi)
    {
        $this->authorize('view', $donasi);

        $bankList = [
            ['nama' => 'Bank BRI',    'norek' => '0123-01-456789-10-9', 'atas_nama' => 'Yayasan Zakat Online'],
            ['nama' => 'Bank BNI',    'norek' => '0123456789',          'atas_nama' => 'Yayasan Zakat Online'],
            ['nama' => 'Bank Mandiri','norek' => '123-00-1234567-8',    'atas_nama' => 'Yayasan Zakat Online'],
            ['nama' => 'Bank BSI',    'norek' => '7123456789',          'atas_nama' => 'Yayasan Zakat Online'],
        ];

        return view('user.donasi.bayar', compact('donasi', 'bankList'));
    }

    /**
     * Proses pembayaran online (upload bukti)
     */
    public function bayarOnline(Request $request, DonasiZakat $donasi)
    {
        $this->authorize('view', $donasi);

        $validated = $request->validate([
            'metode_pembayaran'     => ['required', 'string'],
            'nama_bank'             => ['nullable', 'string'],
            'bukti_transfer'        => ['nullable', 'file', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
            'tanggal_rencana_bayar' => ['required', 'date', 'after_or_equal:today'],
        ], [
            'metode_pembayaran.required' => 'Metode pembayaran wajib dipilih.',
            'bukti_transfer.mimes'       => 'File harus berupa gambar (JPG, PNG, JPEG).',
            'bukti_transfer.max'         => 'Ukuran file maksimal 2MB.',
        ]);

        DB::beginTransaction();
        try {
            // Upload bukti transfer jika ada
            $path = null;
            if ($request->hasFile('bukti_transfer')) {
                $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');
            }

            $transaksi = Transaksi::buatTransaksi([
                'donasi_zakat_id'       => $donasi->id,
                'user_id'               => Auth::id(),
                'metode_bayar'          => 'online',
                'bank_tujuan'           => $validated['nama_bank'] ?? $validated['metode_pembayaran'],
                'total_bayar'           => $donasi->jumlah_zakat,
                'tanggal_rencana_bayar' => $validated['tanggal_rencana_bayar'],
                'batas_waktu_bayar'     => date('Y-m-d 23:59:59', strtotime($validated['tanggal_rencana_bayar'])),
            ]);

            // Buat pembayaran
            Pembayaran::prosesPembayaran([
                'transaksi_id'      => $transaksi->id,
                'metode_pembayaran' => $validated['metode_pembayaran'],
                'nama_bank'         => $validated['nama_bank'] ?? null,
                'total_bayar'       => $donasi->jumlah_zakat,
                'bukti_transfer'    => $path,
            ]);

            // Update status donasi
            $donasi->ubahStatusDonasi('menunggu');

            DB::commit();

            if ($path) {
                return redirect()->route('user.transaksi.show', $transaksi->id)
                    ->with('success', 'Pembayaran berhasil dikirim! Menunggu verifikasi dari admin.');
            } else {
                return redirect()->route('user.transaksi.show', $transaksi->id)
                    ->with('success', 'Rencana pembayaran berhasil disimpan. Jangan lupa untuk mengunggah bukti transfer sebelum batas waktu.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Proses pembayaran di tempat
     */
    public function bayarDitempat(Request $request, DonasiZakat $donasi)
    {
        $this->authorize('view', $donasi);

        $validated = $request->validate([
            'tanggal_rencana_bayar' => ['required', 'date', 'after_or_equal:today'],
        ]);

        DB::beginTransaction();
        try {
            // Buat transaksi
            $transaksi = Transaksi::buatTransaksi([
                'donasi_zakat_id'       => $donasi->id,
                'user_id'               => Auth::id(),
                'metode_bayar'          => 'ditempat',
                'total_bayar'           => $donasi->jumlah_zakat,
                'tanggal_rencana_bayar' => $validated['tanggal_rencana_bayar'],
                'batas_waktu_bayar'     => date('Y-m-d 23:59:59', strtotime($validated['tanggal_rencana_bayar'])),
            ]);

            // Buat pembayaran placeholder
            Pembayaran::prosesPembayaran([
                'transaksi_id'      => $transaksi->id,
                'metode_pembayaran' => 'Tunai',
                'total_bayar'       => $donasi->jumlah_zakat,
            ]);

            // Update status donasi
            $donasi->ubahStatusDonasi('menunggu');

            DB::commit();

            return redirect()->route('user.donasi.tiket', $transaksi->id);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman tiket pembayaran di tempat
     */
    public function tiket(Transaksi $transaksi)
    {
        $transaksi->load(['donasiZakat', 'user', 'pembayaran']);
        return view('user.donasi.tiket', compact('transaksi'));
    }

    /**
     * Upload bukti pembayaran yang tertunda
     */
    public function uploadBukti(Request $request, Transaksi $transaksi)
    {
        $this->authorize('view', $transaksi->donasiZakat);

        if ($transaksi->status !== 'menunggu') {
            return back()->with('error', 'Transaksi sudah tidak dapat diubah.');
        }

        $request->validate([
            'bukti_transfer' => ['required', 'file', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ], [
            'bukti_transfer.required' => 'Bukti transfer wajib diunggah.',
            'bukti_transfer.mimes'    => 'File harus berupa gambar (JPG, PNG, JPEG).',
            'bukti_transfer.max'      => 'Ukuran file maksimal 2MB.',
        ]);

        $path = $request->file('bukti_transfer')->store('bukti-transfer', 'public');

        if ($transaksi->pembayaran) {
            $transaksi->pembayaran->update([
                'bukti_transfer' => $path
            ]);
        }

        return back()->with('success', 'Bukti transfer berhasil diunggah! Menunggu verifikasi admin.');
    }
}
