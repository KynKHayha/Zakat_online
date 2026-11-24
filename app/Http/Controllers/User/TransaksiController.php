<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Transaksi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransaksiController extends Controller
{
    /**
     * Detail transaksi user
     */
    public function show(Transaksi $transaksi)
    {
        // Pastikan transaksi milik user yang login
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $transaksi->lihatDetailTransaksi();

        return view('user.transaksi.show', compact('transaksi'));
    }

    /**
     * Batalkan transaksi
     */
    public function batal(Transaksi $transaksi)
    {
        if ($transaksi->user_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        if ($transaksi->batalTransaksi()) {
            return redirect()->route('user.riwayat')
                ->with('success', 'Transaksi berhasil dibatalkan.');
        }

        return back()->with('error', 'Transaksi tidak dapat dibatalkan karena statusnya bukan "menunggu".');
    }
}
