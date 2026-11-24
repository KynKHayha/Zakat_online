<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    /**
     * Daftar semua user (muzakki)
     */
    public function index(Request $request)
    {
        $query = User::where('role', 'muzakki')
            ->withCount('transaksis')
            ->withSum(['transaksis' => fn ($q) => $q->where('status', 'sukses')], 'total_bayar')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                    ->orWhere('email', 'LIKE', "%{$search}%");
            });
        }

        if ($request->filled('jenis_zakat') && $request->jenis_zakat !== 'semua') {
            $query->whereHas('transaksis.donasiZakat', function ($q) use ($request) {
                $q->where('jenis_zakat', $request->jenis_zakat);
            });
        }

        // Get unique zakat categories per user
        $query->with(['transaksis.donasiZakat' => function ($q) {
            $q->select('id', 'jenis_zakat');
        }]);

        $users = $query->paginate(15);

        return view('admin.users.index', compact('users'));
    }

    /**
     * Riwayat aktivitas dan transaksi user tertentu
     */
    public function riwayat(User $user)
    {
        $transaksis = $user->transaksis()
            ->with(['donasiZakat', 'pembayaran'])
            ->latest()
            ->paginate(10);

        $totalDibayar = $user->transaksis()->where('status', 'sukses')->sum('total_bayar');
        $totalTransaksi = $user->transaksis()->count();

        $utangs = $user->donasiZakats()
            ->where(function($q) {
                $q->where('status', 'draft')
                  ->orWhere(function ($q2) {
                      $q2->where('status', 'menunggu')
                         ->whereHas('transaksi', function ($t) {
                             $t->where('status', 'menunggu')
                               ->where(function ($t2) {
                                   $t2->where('metode_bayar', 'ditempat')
                                      ->orWhere(function ($t3) {
                                          $t3->where('metode_bayar', 'online')
                                             ->whereHas('pembayaran', function ($p) {
                                                 $p->whereNull('bukti_transfer');
                                             });
                                      });
                               });
                         });
                  })
                  ->orWhereHas('transaksi', function ($t) {
                      $t->where('status', 'dibatalkan');
                  });
            })
            ->latest()
            ->get();

        return view('admin.users.riwayat', compact('user', 'transaksis', 'totalDibayar', 'totalTransaksi', 'utangs'));
    }

    public function utang(Request $request)
    {
        $utangCondition = function ($q) {
            $q->where('status', 'draft')
              ->orWhere(function ($q2) {
                  $q2->where('status', 'menunggu')
                     ->whereHas('transaksi', function ($t) {
                         $t->where('status', 'menunggu')
                           ->where(function ($t2) {
                               $t2->where('metode_bayar', 'ditempat')
                                  ->orWhere(function ($t3) {
                                      $t3->where('metode_bayar', 'online')
                                         ->whereHas('pembayaran', function ($p) {
                                             $p->whereNull('bukti_transfer');
                                         });
                                  });
                           });
                     });
              })
              ->orWhereHas('transaksi', function ($t) {
                  $t->where('status', 'dibatalkan');
              });
        };

        $query = User::where('role', 'muzakki')
            ->whereHas('donasiZakats')
            ->with('donasiZakats');

        if ($request->get('filter') === 'utang') {
            $query->whereHas('donasiZakats', $utangCondition);
        } elseif ($request->get('filter') === 'lunas') {
            $query->whereDoesntHave('donasiZakats', $utangCondition);
        }

        $users = $query->paginate(15);
        $totalUtangKeseluruhan = User::where('role', 'muzakki')->whereHas('donasiZakats', $utangCondition)->count();

        return view('admin.users.utang', compact('users', 'totalUtangKeseluruhan'));
    }
}
