<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\User\DashboardController;
use App\Http\Controllers\User\DonasiController;
use App\Http\Controllers\User\TransaksiController;
use App\Http\Controllers\User\RiwayatController;
use App\Http\Controllers\User\NotifikasiController;
use App\Http\Controllers\User\LaporanUserController;
use App\Http\Controllers\Admin\DashboardAdminController;
use App\Http\Controllers\Admin\TransaksiAdminController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\Admin\LaporanController;
use App\Http\Controllers\Admin\NotifikasiAdminController;
use Illuminate\Support\Facades\Route;

// ══════════════════════════════════════════════════════════════
// HALAMAN UTAMA → landing page publik (guest), redirect jika sudah login
// ══════════════════════════════════════════════════════════════
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->isAdmin()
            ? redirect()->route('admin.dashboard')
            : redirect()->route('user.dashboard');
    }
    return view('welcome');
})->name('home');

// ══════════════════════════════════════════════════════════════
// AUTH ROUTES (Guest only)
// ══════════════════════════════════════════════════════════════
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register'])->name('register.submit');
});

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ══════════════════════════════════════════════════════════════
// USER / MUZAKKI ROUTES
// ══════════════════════════════════════════════════════════════
Route::prefix('user')->name('user.')->middleware(['auth', 'muzakki'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profil', [DashboardController::class, 'profil'])->name('profil');
    Route::post('/profil', [DashboardController::class, 'updateProfil'])->name('profil.update');

    // Donasi Zakat
    Route::get('/donasi/buat', [DonasiController::class, 'create'])->name('donasi.create');
    Route::post('/donasi/hitung', [DonasiController::class, 'hitungZakat'])->name('donasi.hitung');
    Route::post('/donasi', [DonasiController::class, 'store'])->name('donasi.store');
    Route::get('/donasi/{donasi}/bayar', [DonasiController::class, 'bayar'])->name('donasi.bayar');
    Route::post('/donasi/{donasi}/bayar-online', [DonasiController::class, 'bayarOnline'])->name('donasi.bayar-online');
    Route::post('/donasi/{donasi}/bayar-ditempat', [DonasiController::class, 'bayarDitempat'])->name('donasi.bayar-ditempat');
    Route::get('/tiket/{transaksi}', [DonasiController::class, 'tiket'])->name('donasi.tiket');
    Route::post('/transaksi/{transaksi}/upload-bukti', [DonasiController::class, 'uploadBukti'])->name('transaksi.upload-bukti');

    // Transaksi
    Route::get('/transaksi/{transaksi}', [TransaksiController::class, 'show'])->name('transaksi.show');
    Route::post('/transaksi/{transaksi}/batal', [TransaksiController::class, 'batal'])->name('transaksi.batal');

    // Riwayat (dengan tab sudah bayar / belum bayar)
    Route::get('/riwayat', [RiwayatController::class, 'index'])->name('riwayat');

    // Laporan Pribadi (Rijal)
    Route::get('/laporan', [LaporanUserController::class, 'index'])->name('laporan');
    Route::get('/laporan/pdf', [LaporanUserController::class, 'exportPdf'])->name('laporan.pdf');

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index'])->name('notifikasi');
    Route::get('/notifikasi/unread-count', [NotifikasiController::class, 'unreadCount'])->name('notifikasi.unread-count');
    Route::get('/notifikasi/terbaru', [NotifikasiController::class, 'terbaru'])->name('notifikasi.terbaru');
    Route::post('/notifikasi/{notifikasi}/baca', [NotifikasiController::class, 'baca'])->name('notifikasi.baca');
});

// ══════════════════════════════════════════════════════════════
// ADMIN / AMIL ROUTES
// ══════════════════════════════════════════════════════════════
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {

    // Dashboard (Dino)
    Route::get('/dashboard', [DashboardAdminController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/detail', [DashboardAdminController::class, 'detail'])->name('dashboard.detail');

    // Manajemen Transaksi
    Route::get('/transaksi', [TransaksiAdminController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaksi}', [TransaksiAdminController::class, 'show'])->name('transaksi.show');
    Route::post('/transaksi/{transaksi}/terima', [TransaksiAdminController::class, 'terima'])->name('transaksi.terima');
    Route::post('/transaksi/{transaksi}/tolak', [TransaksiAdminController::class, 'tolak'])->name('transaksi.tolak');

    // Manajemen User
    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/utang', [UserManagementController::class, 'utang'])->name('users.utang');
    Route::get('/users/{user}/riwayat', [UserManagementController::class, 'riwayat'])->name('users.riwayat');
    Route::get('/users/{user}/laporan-pdf', [LaporanController::class, 'cetakMuzakkiPdf'])->name('users.laporan-pdf');

    // Laporan Keuangan
    Route::get('/laporan', [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/export-pdf', [LaporanController::class, 'exportPdf'])->name('laporan.pdf');

    // Notifikasi Pengingat (Adirja)
    Route::post('/notifikasi/kirim-pengingat', [NotifikasiAdminController::class, 'kirimPengingat'])->name('notifikasi.kirim-pengingat');
    Route::get('/notifikasi/status-pengingat', [NotifikasiAdminController::class, 'statusPengingat'])->name('notifikasi.status-pengingat');
});
