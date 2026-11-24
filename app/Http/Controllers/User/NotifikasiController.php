<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    /**
     * Daftar semua notifikasi user
     */
    public function index()
    {
        $user = Auth::user();

        $notifikasis = $user->notifikasis()
            ->with('transaksi')
            ->latest()
            ->paginate(15);

        // Tandai semua sebagai dibaca
        $user->notifikasis()->where('is_read', false)->update(['is_read' => true]);

        $unreadCount = 0; // sudah dibaca semuanya

        return view('user.notifikasi.index', compact('notifikasis', 'unreadCount'));
    }

    /**
     * Ambil unread count untuk navbar (AJAX)
     */
    public function unreadCount()
    {
        $count = Auth::user()->unreadNotifikasiCount();
        return response()->json(['count' => $count]);
    }

    /**
     * Ambil notifikasi terbaru untuk dropdown navbar (AJAX)
     */
    public function terbaru()
    {
        $notifikasis = Auth::user()
            ->notifikasis()
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($n) {
                return [
                    'id'         => $n->id,
                    'judul'      => $n->judul,
                    'pesan'      => $n->pesan,
                    'tipe'       => $n->tipe,
                    'is_read'    => $n->is_read,
                    'icon'       => $n->icon,
                    'created_at' => $n->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifikasis' => $notifikasis,
            'unread'      => Auth::user()->unreadNotifikasiCount(),
        ]);
    }

    /**
     * Tandai satu notifikasi sebagai dibaca
     */
    public function baca(Notifikasi $notifikasi)
    {
        if ($notifikasi->user_id !== Auth::id()) {
            abort(403);
        }

        $notifikasi->tandaiBaca();

        return response()->json(['success' => true]);
    }
}
