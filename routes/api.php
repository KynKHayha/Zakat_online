<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;

use App\Http\Controllers\Api\RiwayatController;
use App\Http\Controllers\Api\DonasiController;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // User Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Riwayat
    Route::get('/riwayat', [RiwayatController::class, 'index']);
    
    // Donasi
    Route::post('/donasi/hitung', [DonasiController::class, 'hitung']);
    Route::post('/donasi', [DonasiController::class, 'store']);
    Route::post('/donasi/{donasi}/bayar', [DonasiController::class, 'bayar']);
});
