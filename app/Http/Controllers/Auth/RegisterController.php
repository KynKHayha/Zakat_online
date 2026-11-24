<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    /**
     * Tampilkan form register
     */
    public function showRegistrationForm()
    {
        if (Auth::check()) {
            return redirect()->route('user.dashboard');
        }
        return view('auth.register');
    }

    /**
     * Proses registrasi muzakki baru
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'unique:users,email'],
            'password'              => ['required', 'min:8', 'confirmed'],
            'phone'                 => ['nullable', 'string', 'max:20'],
            'address'               => ['nullable', 'string', 'max:500'],
            'terms'                 => ['accepted'],
        ], [
            'name.required'              => 'Nama lengkap wajib diisi.',
            'email.required'             => 'Email wajib diisi.',
            'email.email'                => 'Format email tidak valid.',
            'email.unique'               => 'Email sudah terdaftar. Silakan gunakan email lain.',
            'password.required'          => 'Password wajib diisi.',
            'password.min'               => 'Password minimal 8 karakter.',
            'password.confirmed'         => 'Konfirmasi password tidak cocok.',
            'terms.accepted'             => 'Anda harus menyetujui syarat & ketentuan.',
        ]);

        // Gunakan method registrasi dari model User (PBO)
        $user = User::registrasi([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone'    => $validated['phone'] ?? null,
            'address'  => $validated['address'] ?? null,
        ]);

        Auth::login($user);

        return redirect()->route('user.dashboard')
            ->with('success', "Selamat datang, {$user->name}! Akun Anda berhasil dibuat.");
    }
}
