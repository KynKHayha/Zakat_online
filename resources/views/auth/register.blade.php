@extends('layouts.guest')
@section('title', 'Daftar — Zakat Online')

@section('content')
<div x-data="{
    loading: false,
    showPass: false,
    showConfirm: false,
    password: '',
    strength: 0,
    strengthColor: '#e5e7eb',
    strengthText: '',
    checkStrength(val) {
        this.password = val;
        let s = 0;
        if (val.length >= 8) s++;
        if (/[A-Z]/.test(val)) s++;
        if (/[0-9]/.test(val)) s++;
        if (/[^a-zA-Z0-9]/.test(val)) s++;
        this.strength = s;
        const colors = ['#e5e7eb','#ef4444','#f97316','#eab308','#22c55e'];
        const texts = ['','Lemah','Sedang','Kuat','Sangat Kuat'];
        this.strengthColor = colors[s] || '#e5e7eb';
        this.strengthText = texts[s] || '';
    }
}">

    {{-- Header --}}
    <div class="mb-7">
        <span class="chip bg-brand-50 text-brand-700 border border-brand-100 mb-4">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            Akun Baru
        </span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Mulai tunaikan zakat ✨</h2>
        <p class="text-sm text-gray-500 mt-2 leading-relaxed">Gratis selamanya. Cukup nama, email, dan password — selesai dalam 1 menit.</p>
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

    <form action="{{ route('register.submit') }}" method="POST" @submit="loading = true" class="space-y-5">
        @csrf

        <div>
            <label for="name" class="fld-label">Nama Lengkap</label>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autocomplete="name"
                       placeholder="Masukkan nama lengkap"
                       class="fld pl-12 {{ $errors->has('name') ? 'fld-error' : '' }}">
            </div>
            @error('name')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="email" class="fld-label">Alamat Email</label>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="email"
                       inputmode="email" placeholder="nama@email.com"
                       class="fld pl-12 {{ $errors->has('email') ? 'fld-error' : '' }}">
            </div>
            @error('email')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <label for="phone" class="fld-label">Nomor HP <span class="text-gray-400 font-medium">(opsional)</span></label>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" autocomplete="tel"
                       inputmode="numeric" placeholder="08xxxxxxxxxx"
                       class="fld pl-12">
            </div>
            <p class="text-[11px] text-gray-400 mt-1.5">Untuk notifikasi status verifikasi zakat Anda.</p>
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="fld-label !mb-0">Password</label>
                <button type="button" @click="showPass = !showPass" class="text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">
                    <span x-text="showPass ? 'Sembunyikan' : 'Lihat'">Lihat</span>
                </button>
            </div>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                       autocomplete="new-password" placeholder="Minimal 8 karakter"
                       @input="checkStrength($event.target.value)"
                       class="fld pl-12 {{ $errors->has('password') ? 'fld-error' : '' }}">
            </div>
            <div class="mt-2.5 h-1.5 rounded-full bg-gray-100 overflow-hidden" x-show="password.length > 0" x-cloak>
                <div class="h-full rounded-full transition-all duration-300"
                     :style="'width:' + (strength / 4 * 100) + '%; background:' + strengthColor"></div>
            </div>
            <p class="text-[11px] font-bold mt-1.5" x-show="password.length > 0" x-cloak
               x-text="strengthText" :style="'color:' + strengthColor"></p>
            @error('password')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password_confirmation" class="fld-label !mb-0">Konfirmasi Password</label>
                <button type="button" @click="showConfirm = !showConfirm" class="text-xs font-bold text-brand-600 hover:text-brand-700 transition-colors">
                    <span x-text="showConfirm ? 'Sembunyikan' : 'Lihat'">Lihat</span>
                </button>
            </div>
            <div class="relative">
                <svg class="w-5 h-5 text-gray-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <input id="password_confirmation" :type="showConfirm ? 'text' : 'password'"
                       name="password_confirmation" required autocomplete="new-password"
                       placeholder="Ulangi password Anda"
                       class="fld pl-12">
            </div>
        </div>

        <label for="terms" class="flex items-start gap-3 cursor-pointer select-none rounded-2xl border border-gray-100 bg-gray-50/70 px-4 py-3.5">
            <input type="checkbox" id="terms" name="terms" required
                   class="w-[18px] h-[18px] rounded-md accent-emerald-600 cursor-pointer mt-0.5 flex-shrink-0">
            <span class="text-xs text-gray-600 leading-relaxed">
                Saya menyetujui <a href="#" class="font-bold text-brand-600 hover:underline">Syarat &amp; Ketentuan</a> dan <a href="#" class="font-bold text-brand-600 hover:underline">Kebijakan Privasi</a> Zakat Online.
            </span>
        </label>

        <button type="submit" :disabled="loading" class="btn btn-primary w-full !py-4 text-base">
            <svg x-show="loading" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
            <svg x-show="!loading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
            <span x-text="loading ? 'Membuat akun...' : 'Buat Akun Sekarang'">Buat Akun Sekarang</span>
        </button>
    </form>

    <div class="relative text-center my-6">
        <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-gray-200"></div></div>
        <span class="relative bg-white px-3 text-xs font-semibold text-gray-400">atau</span>
    </div>

    <p class="text-center text-sm text-gray-500">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-extrabold text-brand-600 hover:text-brand-700 transition-colors">Masuk di sini &rarr;</a>
    </p>
</div>
@endsection
