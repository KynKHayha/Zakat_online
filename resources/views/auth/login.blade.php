@extends('layouts.guest')
@section('title', 'Masuk — Zakat Online')

@section('content')
<div x-data="{ loading: false, showPass: false }">

    {{-- Header --}}
    <div class="mb-7">
        <span class="chip bg-brand-50 text-brand-700 border border-brand-100 mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            Portal Muzakki
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Selamat datang kembali 👋</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">Masuk untuk menghitung, menunaikan, dan memantau zakat Anda.</p>
    </div>

    {{-- Error global --}}
    @if($errors->any())
    <div class="flex items-start gap-3 mb-6 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <div>
            <p class="text-xs font-extrabold text-red-800 uppercase tracking-wide">Perlu diperbaiki</p>
            <ul class="mt-1.5 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-xs text-red-700 flex items-center gap-1.5">
                        <span class="w-1 h-1 rounded-full bg-red-500 flex-shrink-0"></span>{{ $error }}
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- Form --}}
    <form action="{{ route('login.submit') }}" method="POST" @submit="loading = true" class="space-y-5">
        @csrf

        <div>
            <label for="email" class="fld-label">Alamat Email</label>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                       autocomplete="email" inputmode="email" placeholder="nama@email.com"
                       class="fld pl-12 {{ $errors->has('email') ? 'fld-error' : '' }}">
            </div>
            @error('email')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="fld-label !mb-0">Password</label>
                <button type="button" @click="showPass = !showPass"
                        class="text-xs font-bold text-brand-600 hover:text-brand-700 inline-flex items-center gap-1.5 transition-colors">
                    <svg x-show="!showPass" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    <svg x-show="showPass" x-cloak class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/></svg>
                    <span x-text="showPass ? 'Sembunyikan' : 'Lihat'">Lihat</span>
                </button>
            </div>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                       autocomplete="current-password" placeholder="••••••••"
                       class="fld pl-12 {{ $errors->has('password') ? 'fld-error' : '' }}">
            </div>
            @error('password')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div class="flex items-center justify-between">
            <label for="remember" class="flex items-center gap-2.5 cursor-pointer select-none">
                <input type="checkbox" name="remember" id="remember"
                       class="w-4.5 h-4.5 w-[18px] h-[18px] rounded-md accent-emerald-600 cursor-pointer">
                <span class="text-sm font-medium text-gray-600">Ingat saya</span>
            </label>
            <a href="{{ url('/') }}" class="text-xs font-bold text-gray-400 hover:text-brand-600 transition-colors">Butuh bantuan?</a>
        </div>

        <button type="submit" :disabled="loading" class="btn btn-primary w-full !py-4 text-base">
            <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/></svg>
            <span x-text="loading ? 'Sedang masuk...' : 'Masuk ke Akun'">Masuk ke Akun</span>
        </button>
    </form>

    {{-- Divider --}}
    <div class="relative text-center my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
        <span class="relative bg-white px-3 text-xs font-semibold text-gray-400">atau</span>
    </div>

    <p class="text-center text-sm text-gray-500">
        Belum punya akun?
        <a href="{{ route('register') }}" class="font-extrabold text-brand-600 hover:text-brand-700 transition-colors">Daftar gratis &rarr;</a>
    </p>

    {{-- Trust badge --}}
    <div class="mt-7 flex items-start gap-3 rounded-2xl border border-emerald-100 bg-emerald-50/70 px-4 py-3.5">
        <div class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
            <svg class="w-4.5 h-4.5 w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
        </div>
        <div>
            <p class="text-xs font-extrabold text-emerald-800">Data Anda aman</p>
            <p class="text-xs text-emerald-700 leading-relaxed mt-0.5">Kredensial dienkripsi dan hanya dipakai untuk mencatat transaksi zakat Anda.</p>
        </div>
    </div>
</div>
@endsection
