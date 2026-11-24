<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DonasiZakat;
use App\Models\Pembayaran;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DonasiController extends Controller
{
    public function hitung(Request $request)
    {
        $request->validate([
            'jenis_zakat' => ['required', 'string'],
        ]);

        $jenis = $request->jenis_zakat;
        if ($jenis === 'emas') $jenis = 'emas_perak';

        $jumlah = DonasiZakat::hitungJumlahZakat($jenis, $request->all());

        return response()->json([
            'success' => true,
            'data' => [
                'jumlah' => $jumlah,
                'jumlah_format' => 'Rp ' . number_format($jumlah, 0, ',', '.')
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'jenis_zakat'   => ['required', 'in:fitrah,mal,profesi,perniagaan,pertanian,emas_perak'],
            'nama_muzakki'  => ['required', 'string', 'max:255'],
            'jumlah_zakat'  => ['required', 'numeric', 'min:10000'],
            'jumlah_jiwa'   => ['nullable', 'integer'],
            'harga_beras'   => ['nullable', 'numeric'],
            'penghasilan'   => ['nullable', 'numeric'],
            'nilai_aset'    => ['nullable', 'numeric'],
            'keterangan'    => ['nullable', 'string'],
        ]);

        $validated['user_id'] = $request->user()->id;
        $validated['status']  = 'draft';

        $donasi = DonasiZakat::buatDonasi($validated);

        return response()->json([
            'success' => true,
            'message' => 'Data zakat berhasil disimpan',
            'data' => $donasi
        ]);
    }

    public function bayar(Request $request, $donasiId)
    {
        $user = $request->user();
        $donasi = DonasiZakat::where('id', $donasiId)->where('user_id', $user->id)->firstOrFail();

        $validated = $request->validate([
            'metode_pembayaran'     => ['required', 'string'], // 'online' or 'ditempat'
            'nama_bank'             => ['nullable', 'string'],
            'tanggal_rencana_bayar' => ['required', 'date'],
        ]);

        DB::beginTransaction();
        try {
            $metode = $validated['metode_pembayaran'] === 'ditempat' ? 'ditempat' : 'online';

            $transaksi = Transaksi::buatTransaksi([
                'donasi_zakat_id'       => $donasi->id,
                'user_id'               => $user->id,
                'metode_bayar'          => $metode,
                'bank_tujuan'           => $validated['nama_bank'] ?? 'Tunai',
                'total_bayar'           => $donasi->jumlah_zakat,
                'tanggal_rencana_bayar' => $validated['tanggal_rencana_bayar'],
                'batas_waktu_bayar'     => date('Y-m-d 23:59:59', strtotime($validated['tanggal_rencana_bayar'])),
            ]);

            Pembayaran::prosesPembayaran([
                'transaksi_id'      => $transaksi->id,
                'metode_pembayaran' => $validated['nama_bank'] ?? 'Tunai',
                'nama_bank'         => $validated['nama_bank'] ?? null,
                'total_bayar'       => $donasi->jumlah_zakat,
            ]);

            $donasi->ubahStatusDonasi('menunggu');
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Pembayaran berhasil dikonfirmasi',
                'data' => $transaksi
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }
}
