@extends('layouts.app')
@section('title', 'Profil Saya')
@section('page-title', 'Profil Saya')
@section('page-subtitle', 'Kelola informasi akun Anda')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">

    <div class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-500 hero-pattern px-6 sm:px-8 py-8">
            <div class="relative z-10 flex items-center gap-5">
                <div class="w-20 h-20 rounded-3xl bg-white/20 border border-white/30 flex items-center justify-center text-white font-extrabold text-3xl shadow-lg flex-shrink-0">
                    {{ substr(auth()->user()->name, 0, 1) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-white font-extrabold text-xl sm:text-2xl truncate">{{ auth()->user()->name }}</h2>
                    <p class="text-brand-100 text-sm mt-1 truncate">{{ auth()->user()->email }}</p>
                    <span class="inline-flex items-center gap-1.5 mt-2.5 bg-white/20 text-white text-xs font-bold px-3 py-1 rounded-full border border-white/25">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                        </svg>
                        Muzakki
                    </span>
                </div>
            </div>
        </div>

        <!-- Form -->
        <div class="p-6 sm:p-8">
            @if(session('success'))
                <div class="mb-6 bg-brand-50 border border-brand-200 text-brand-800 px-4 py-3 rounded-2xl flex items-center gap-2.5">
                    <svg class="w-5 h-5 text-brand-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    <p class="text-sm font-semibold">{{ session('success') }}</p>
                </div>
            @endif

            <form action="{{ route('user.profil.update') }}" method="POST" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="fld-label">Nama Lengkap</label>
                    <input id="name" type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                           class="fld {{ $errors->has('name') ? 'fld-error' : '' }}">
                    @error('name') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="email" class="fld-label">Email</label>
                    <input id="email" type="email" value="{{ auth()->user()->email }}" disabled
                           class="fld bg-gray-100 text-gray-400 cursor-not-allowed">
                    <p class="text-xs text-gray-400 mt-1.5">Email tidak dapat diubah.</p>
                </div>

                <div>
                    <label for="phone" class="fld-label">Nomor Telepon</label>
                    <input id="phone" type="tel" name="phone" value="{{ old('phone', auth()->user()->phone) }}"
                           placeholder="08xxxxxxxxxx"
                           class="fld {{ $errors->has('phone') ? 'fld-error' : '' }}">
                    @error('phone') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="address" class="fld-label">Alamat</label>
                    <textarea id="address" name="address" rows="3" placeholder="Alamat lengkap Anda"
                              class="fld resize-none {{ $errors->has('address') ? 'fld-error' : '' }}">{{ old('address', auth()->user()->address) }}</textarea>
                    @error('address') <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p> @enderror
                </div>

                <button type="submit" class="btn btn-primary w-full">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Simpan Perubahan
                </button>
            </form>
        </div>
    </div>

    <!-- Statistik akun -->
    <div class="grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-card border border-gray-100 text-center">
            <p class="num text-2xl font-extrabold text-brand-700">{{ auth()->user()->transaksis()->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Total Transaksi</p>
        </div>
        <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-card border border-gray-100 text-center">
            <p class="num text-2xl font-extrabold text-brand-700">{{ auth()->user()->transaksis()->where('status', 'sukses')->count() }}</p>
            <p class="text-xs text-gray-500 mt-1">Transaksi Sukses</p>
        </div>
        <div class="bg-white rounded-3xl p-4 sm:p-5 shadow-card border border-gray-100 text-center">
            <p class="num text-sm sm:text-lg font-extrabold text-brand-700">
                Rp {{ number_format(auth()->user()->transaksis()->where('status', 'sukses')->sum('total_bayar'), 0, ',', '.') }}
            </p>
            <p class="text-xs text-gray-500 mt-1">Total Dibayar</p>
        </div>
    </div>
</div>
@endsection
