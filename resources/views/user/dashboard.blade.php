@extends('layouts.app')

@section('title', 'Dashboard — Muzakki')

@push('styles')
<style>
    .stat-card { transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; }
    .stat-card:hover { transform: translateY(-4px); box-shadow: 0 20px 40px -12px rgba(16,185,129,.22); border-color: #a7f3d0; }
    .skeleton { background: linear-gradient(90deg,#f1f5f9 25%,#e2e8f0 50%,#f1f5f9 75%); background-size: 200% 100%; animation: skel 1.4s infinite; }
    @keyframes skel { 0% { background-position: 200% 0 } 100% { background-position: -200% 0 } }
    .type-card { cursor: pointer; transition: all .22s ease; border: 2px solid #e5e7eb; background: #fff; }
    .type-card:hover { border-color: #6ee7b7; background: #ecfdf5; transform: translateY(-2px); }
    .type-card.selected { border-color: #059669; background: #ecfdf5; box-shadow: 0 8px 20px -10px rgba(5,150,105,.5); }
    .prefix { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-weight: 700; font-size: .9rem; pointer-events: none; }
    .suffix { position: absolute; right: 1rem; top: 50%; transform: translateY(-50%); color: #9ca3af; font-weight: 600; font-size: .8rem; pointer-events: none; }
    .b-sukses    { background: #d1fae5; color: #065f46; }
    .b-menunggu  { background: #fef3c7; color: #92400e; }
    .b-ditolak   { background: #fee2e2; color: #991b1b; }
    .b-pending   { background: #e0e7ff; color: #3730a3; }
</style>
@endpush

@php
    // Peta status → kelas badge + label (dipakai tabel & kartu riwayat)
    $statusBadge = function ($status) {
        $map = [
            'sukses'             => ['b-sukses',    '✓ Sukses'],
            'menunggu_verifikasi' => ['b-menunggu', '⏳ Menunggu'],
            'ditolak'            => ['b-ditolak',   '✗ Ditolak'],
        ];
        $s = $status ?? 'pending';
        return $map[$s] ?? ['b-pending', '📋 Pending'];
    };
    $jenisIcon = fn ($jenis) => match ($jenis) {
        'fitrah'      => '🌾',
        'mal'         => '💰',
        'profesi'     => '💼',
        'perniagaan'  => '🏪',
        'pertanian'   => '🌱',
        'emas', 'emas_perak' => '🥇',
        default       => '📦',
    };
@endphp

@section('content')
<div x-data="zakatDashboard()" x-init="init()">

    {{-- ===================== HERO ===================== --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 hero-pattern p-6 sm:p-8 mb-6 shadow-glow">
        <div class="absolute -top-16 -right-10 w-72 h-72 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-20 -left-12 w-56 h-56 bg-brand-400/20 rounded-full blur-2xl"></div>

        <div class="relative z-10 flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div class="min-w-0">
                <p class="text-brand-200 text-sm font-semibold mb-1">Assalamu'alaikum, selamat datang 👋</p>
                <h1 class="text-white text-2xl sm:text-4xl font-extrabold tracking-tight truncate">
                    {{ $user->name ?? 'Muzakki' }}
                </h1>
                <p class="text-brand-200 text-sm mt-2">{{ now()->isoFormat('dddd, D MMMM Y') }} — Semoga zakat Anda diterima Allah SWT</p>
                <p class="font-arabic text-brand-100/80 text-lg mt-3 leading-loose">خُذْ مِنْ أَمْوَالِهِمْ صَدَقَةً تُطَهِّرُهُمْ وَتُزَكِّيهِمْ بِهَا</p>
            </div>

            <div class="flex flex-col sm:flex-row gap-3 lg:justify-end">
                <a href="{{ route('user.donasi.create') }}"
                   class="btn bg-white text-brand-700 hover:bg-brand-50 shadow-lg px-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Bayar Zakat Sekarang
                </a>
                <a href="{{ route('user.riwayat') }}"
                   class="btn bg-white/10 text-white border border-white/25 hover:bg-white/20 px-6">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Riwayat Zakat
                </a>
            </div>
        </div>
    </section>

    {{-- ===================== STATISTIK ===================== --}}
    <section class="grid grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-8">
        <div class="stat-card bg-white rounded-3xl p-5 shadow-card border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl bg-blue-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                    </svg>
                </div>
                <span class="chip bg-blue-50 text-blue-600">Total</span>
            </div>
            <p class="num text-3xl font-extrabold text-gray-900">{{ $totalTransaksi ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Total Transaksi</p>
        </div>

        <div class="stat-card bg-white rounded-3xl p-5 shadow-card border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl bg-brand-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="chip bg-brand-50 text-brand-700">✓ Lunas</span>
            </div>
            <p class="num text-3xl font-extrabold text-gray-900">{{ $transaksiSukses ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Transaksi Sukses</p>
        </div>

        <div class="stat-card bg-white rounded-3xl p-5 shadow-card border border-gray-100">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl bg-amber-100 flex items-center justify-center">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="chip bg-amber-50 text-amber-700">Pending</span>
            </div>
            <p class="num text-3xl font-extrabold text-gray-900">{{ $transaksiMenunggu ?? 0 }}</p>
            <p class="text-sm text-gray-500 mt-1">Menunggu Verifikasi</p>
        </div>

        <div class="stat-card rounded-3xl p-5 bg-gradient-to-br from-brand-500 to-brand-700 shadow-glow border border-transparent">
            <div class="flex items-center justify-between mb-4">
                <div class="w-11 h-11 rounded-2xl bg-white/20 flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="chip bg-white/20 text-white">Akumulasi</span>
            </div>
            <p class="num text-2xl font-extrabold text-white leading-tight">Rp {{ number_format($totalZakatDibayar ?? 0, 0, ',', '.') }}</p>
            <p class="text-brand-100 text-sm mt-1">Total Zakat Dibayar</p>
        </div>
    </section>

    {{-- ===================== KALKULATOR + INFO ===================== --}}
    <section class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-8">

        {{-- Kalkulator --}}
        <div class="xl:col-span-2">
            <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-7 h-full">
                <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
                    <div>
                        <h2 class="text-xl sm:text-2xl font-extrabold text-gray-900 flex items-center gap-2">
                            <span class="text-2xl">🧮</span> Kalkulator Zakat
                        </h2>
                        <p class="text-gray-500 text-sm mt-1">Hitung kewajiban zakat Anda, lalu langsung lanjut ke pembayaran.</p>
                    </div>
                    <div x-show="hasilHitung" x-cloak class="chip bg-brand-100 text-brand-700 animate-pulse">✓ Siap Bayar</div>
                </div>

                {{-- Pilih jenis --}}
                <div class="mb-6">
                    <label class="fld-label">Pilih Jenis Zakat</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        <template x-for="jenis in jenisOptions" :key="jenis.value">
                            <button type="button" class="type-card rounded-2xl p-4 text-center"
                                    :class="{ 'selected': selectedJenis === jenis.value }"
                                    @click="selectJenis(jenis.value)">
                                <div class="text-2xl mb-1" x-text="jenis.icon"></div>
                                <p class="text-sm font-bold text-gray-800" x-text="'Zakat ' + jenis.label"></p>
                                <p class="text-xs text-gray-400 mt-0.5" x-text="jenis.rate"></p>
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Input --}}
                <div x-show="selectedJenis" x-cloak x-transition class="space-y-4 mb-6">
                    {{-- Fitrah --}}
                    <div x-show="selectedJenis === 'fitrah'" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="fld-label">Jumlah Jiwa (Tanggungan)</label>
                            <div class="relative">
                                <input type="number" x-model.number="form.jumlahJiwa" @input="hitungOtomatis()"
                                       min="1" placeholder="Contoh: 4" class="fld num pr-16">
                                <span class="suffix">jiwa</span>
                            </div>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <template x-for="n in [1,2,3,4,5,6]" :key="n">
                                    <button type="button" @click="form.jumlahJiwa = n; hitungOtomatis()"
                                            class="chip border-2"
                                            :class="form.jumlahJiwa === n ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-500 border-gray-200 hover:border-brand-300'"
                                            x-text="n + ' jiwa'"></button>
                                </template>
                            </div>
                        </div>
                        <div>
                            <label class="fld-label">Harga Beras per Kg</label>
                            <div class="relative">
                                <span class="prefix">Rp</span>
                                <input type="number" x-model.number="form.hargaBeras" @input="hitungOtomatis()"
                                       class="fld num pl-16" :value="hargaBerasDefault">
                            </div>
                            <p class="text-xs text-gray-400 mt-1.5">Acuan kadar: 2,5 kg beras per jiwa</p>
                        </div>
                    </div>

                    {{-- Selain fitrah --}}
                    <div x-show="selectedJenis !== 'fitrah' && selectedJenis !== ''" class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div x-show="selectedJenis === 'profesi' || selectedJenis === 'mal'">
                            <label class="fld-label" x-text="selectedJenis === 'profesi' ? 'Penghasilan Per Bulan' : 'Total Harta (1 Tahun)'"></label>
                            <div class="relative">
                                <span class="prefix">Rp</span>
                                <input type="number" x-model.number="form.penghasilan" @input="hitungOtomatis()"
                                       placeholder="0" class="fld num pl-16">
                            </div>
                        </div>
                        <div x-show="selectedJenis === 'perniagaan' || selectedJenis === 'pertanian'">
                            <label class="fld-label" x-text="selectedJenis === 'perniagaan' ? 'Total Nilai Perdagangan' : 'Hasil Pertanian'"></label>
                            <div class="relative">
                                <span class="prefix">Rp</span>
                                <input type="number" x-model.number="form.nilaiPerniagaan" @input="hitungOtomatis()"
                                       placeholder="0" class="fld num pl-16">
                            </div>
                        </div>
                        <div x-show="selectedJenis === 'emas'">
                            <label class="fld-label">Berat Emas (gram)</label>
                            <div class="relative">
                                <input type="number" x-model.number="form.beratEmas" @input="hitungOtomatis()"
                                       placeholder="0" step="0.1" class="fld num pr-16">
                                <span class="suffix">gram</span>
                            </div>
                        </div>
                        <div x-show="selectedJenis === 'emas'">
                            <label class="fld-label">Harga Emas per Gram</label>
                            <div class="relative">
                                <span class="prefix">Rp</span>
                                <input type="number" x-model.number="form.hargaEmas" @input="hitungOtomatis()"
                                       placeholder="1000000" class="fld num pl-16">
                            </div>
                        </div>
                        <div x-show="selectedJenis !== 'emas'">
                            <label class="fld-label">Utang / Kewajiban <span class="text-gray-400 font-medium">(opsional)</span></label>
                            <div class="relative">
                                <span class="prefix">Rp</span>
                                <input type="number" x-model.number="form.utang" @input="hitungOtomatis()"
                                       placeholder="0" class="fld num pl-16">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Aksi --}}
                <div x-show="selectedJenis" x-cloak class="flex gap-3 mb-6">
                    <button type="button" @click="hitungViaAjax()" :disabled="isCalculating"
                            class="btn btn-primary flex-1 disabled:opacity-60">
                        <svg x-show="isCalculating" class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <svg x-show="!isCalculating" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 7h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1z"/>
                        </svg>
                        <span x-text="isCalculating ? 'Menghitung...' : 'Hitung Zakat'"></span>
                    </button>
                    <button type="button" @click="resetKalkulator()" class="btn btn-ghost">Reset</button>
                </div>

                {{-- Hasil --}}
                <div x-show="hasilHitung" x-cloak x-transition
                     class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-600 to-brand-800 hero-pattern p-6 text-white shadow-glow mb-4">
                    <div class="relative z-10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="min-w-0">
                                <p class="text-brand-200 text-sm mb-1">Estimasi zakat yang harus dibayar</p>
                                <p class="num text-3xl sm:text-4xl font-extrabold" x-text="'Rp ' + formatRupiah(hasilHitung)"></p>
                                <p class="text-brand-200 text-xs mt-2 leading-relaxed" x-text="keteranganHitung"></p>
                            </div>
                            <div class="w-12 h-12 rounded-2xl bg-white/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-5 pt-5 border-t border-white/20 flex flex-col sm:flex-row gap-3">
                            <a :href="'{{ route('user.donasi.create') }}?jenis=' + selectedJenis + '&jumlah=' + hasilHitung"
                               class="btn bg-white text-brand-700 hover:bg-brand-50 flex-1">Bayar Sekarang →</a>
                            <button type="button" @click="hasilHitung = null" class="btn bg-white/15 hover:bg-white/25 text-white border border-white/25">Ubah</button>
                        </div>
                    </div>
                </div>

                {{-- Error --}}
                <div x-show="kalkulatorError" x-cloak x-transition
                     class="rounded-2xl bg-red-50 border border-red-200 p-4 text-red-600 text-sm flex items-center gap-2">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-text="kalkulatorError"></span>
                </div>
            </div>
        </div>

        {{-- Info ringkas --}}
        <div class="space-y-5">
            <div class="bg-white rounded-4xl shadow-card border border-gray-100 p-5">
                <h3 class="font-extrabold text-gray-900 mb-4 flex items-center gap-2">
                    <span class="text-lg">📋</span> Info Nisab & Kadar
                </h3>
                <div class="space-y-3">
                    <div class="flex items-center justify-between p-3.5 bg-amber-50 rounded-2xl">
                        <div>
                            <p class="text-xs text-amber-600 font-bold">Zakat Mal</p>
                            <p class="text-sm font-bold text-gray-700">85 gram emas</p>
                        </div>
                        <span class="text-2xl">🥇</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-brand-50 rounded-2xl">
                        <div>
                            <p class="text-xs text-brand-600 font-bold">Zakat Fitrah</p>
                            <p class="text-sm font-bold text-gray-700">2,5 kg beras/jiwa</p>
                        </div>
                        <span class="text-2xl">🌾</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-blue-50 rounded-2xl">
                        <div>
                            <p class="text-xs text-blue-600 font-bold">Kadar Zakat</p>
                            <p class="text-sm font-bold text-gray-700">2,5% (Mal/Profesi)</p>
                        </div>
                        <span class="text-2xl">💹</span>
                    </div>
                    <div class="flex items-center justify-between p-3.5 bg-purple-50 rounded-2xl">
                        <div>
                            <p class="text-xs text-purple-600 font-bold">Haul</p>
                            <p class="text-sm font-bold text-gray-700">1 tahun kepemilikan</p>
                        </div>
                        <span class="text-2xl">📅</span>
                    </div>
                </div>
            </div>

            <div class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 to-brand-800 hero-pattern p-5 text-white shadow-glow">
                <div class="relative z-10">
                    <p class="text-brand-200 text-xs font-semibold mb-1">Harga Beras Referensi</p>
                    <p class="num text-2xl font-extrabold">Rp {{ number_format($hargaBeras ?? 14000, 0, ',', '.') }}</p>
                    <p class="text-brand-200 text-xs mt-1">per kilogram (diperbarui otomatis)</p>
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <p class="text-xs text-brand-200">Zakat Fitrah per jiwa</p>
                        <p class="num text-xl font-extrabold mt-1">Rp {{ number_format(($hargaBeras ?? 14000) * 2.5, 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-4xl shadow-card border border-gray-100 p-5 text-center">
                <div class="w-14 h-14 bg-brand-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-7 h-7 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                    </svg>
                </div>
                <h4 class="font-extrabold text-gray-900 mb-1">Tunaikan Zakat Anda</h4>
                <p class="text-xs text-gray-500 mb-4 leading-relaxed">Aman, transparan, dan disalurkan langsung oleh amil terpercaya.</p>
                <a href="{{ route('user.donasi.create') }}" class="btn btn-primary w-full">Mulai Bayar Zakat →</a>
            </div>
        </div>
    </section>

    {{-- ===================== TRANSAKSI TERBARU ===================== --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div>
                <h2 class="text-lg font-extrabold text-gray-900">Transaksi Terbaru</h2>
                <p class="text-sm text-gray-500 mt-0.5">5 transaksi terakhir Anda</p>
            </div>
            <a href="{{ route('user.riwayat') }}" class="btn btn-outline !py-2.5 !px-4 text-sm">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        {{-- Skeleton loading --}}
        <div x-show="tableLoading" class="p-5 sm:p-6 space-y-3">
            <template x-for="i in 3" :key="i">
                <div class="flex items-center gap-4">
                    <div class="skeleton w-11 h-11 rounded-2xl flex-shrink-0"></div>
                    <div class="flex-1 space-y-2">
                        <div class="skeleton h-4 w-2/5 rounded-lg"></div>
                        <div class="skeleton h-3 w-1/4 rounded-lg"></div>
                    </div>
                    <div class="skeleton h-6 w-20 rounded-full"></div>
                </div>
            </template>
        </div>

        {{-- Data --}}
        @php $rows = collect($transaksiTerbaru ?? []); @endphp
        <div x-show="!tableLoading" x-cloak>
            @if($rows->isEmpty())
                <div class="py-16 px-6 text-center">
                    <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                        </svg>
                    </div>
                    <p class="text-gray-800 font-bold mb-1">Belum ada transaksi</p>
                    <p class="text-gray-400 text-sm mb-5">Mulai tunaikan zakat Anda sekarang.</p>
                    <a href="{{ route('user.donasi.create') }}" class="btn btn-primary">Bayar Zakat Pertama</a>
                </div>
            @else
                {{-- Kartu (mobile) --}}
                <div class="lg:hidden divide-y divide-gray-100">
                    @foreach($rows as $transaksi)
                        @php
                            [$badgeClass, $badgeLabel] = $statusBadge($transaksi->status ?? 'pending');
                            $jenis = $transaksi->donasiZakat->jenis_zakat ?? '';
                        @endphp
                        <div class="p-4 flex items-center gap-3.5">
                            <div class="w-11 h-11 rounded-2xl bg-brand-50 flex items-center justify-center text-xl flex-shrink-0">
                                {{ $jenisIcon($jenis) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="num text-sm font-bold text-gray-900">
                                    Rp {{ number_format($transaksi->donasiZakat->jumlah_zakat ?? 0, 0, ',', '.') }}
                                </p>
                                <p class="text-xs text-gray-500 truncate">
                                    {{ $transaksi->donasiZakat?->label_jenis_zakat ?: '-' }} · {{ $transaksi->pembayaran->metode_pembayaran ?? '-' }}
                                </p>
                                <p class="num text-xs text-gray-400 mt-0.5">
                                    {{ $transaksi->created_at ? $transaksi->created_at->format('d M Y') : '-' }}
                                </p>
                            </div>
                            <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                                <span class="status-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                <a href="{{ route('user.transaksi.show', $transaksi->id) }}"
                                   class="text-xs font-bold text-brand-600 hover:text-brand-700">Detail →</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- Tabel (desktop) --}}
                <div class="hidden lg:block overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="bg-gray-50 text-left">
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Kode</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Zakat</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Jumlah</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($rows as $transaksi)
                                @php
                                    [$badgeClass, $badgeLabel] = $statusBadge($transaksi->status ?? 'pending');
                                    $jenis = $transaksi->donasiZakat->jenis_zakat ?? '';
                                @endphp
                                <tr class="hover:bg-brand-50/40 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="num text-sm font-bold text-brand-700">{{ $transaksi->kode_transaksi ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">{{ $jenisIcon($jenis) }}</span>
                                            <span class="text-sm font-semibold text-gray-700">{{ $transaksi->donasiZakat?->label_jenis_zakat ?: '-' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-600 capitalize">{{ $transaksi->pembayaran->metode_pembayaran ?? '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="num text-sm text-gray-600">{{ $transaksi->created_at ? $transaksi->created_at->format('d M Y') : '-' }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <span class="num text-sm font-bold text-gray-900">
                                            Rp {{ number_format($transaksi->donasiZakat->jumlah_zakat ?? 0, 0, ',', '.') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="status-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('user.transaksi.show', $transaksi->id) }}"
                                           class="text-brand-600 hover:text-brand-700 text-sm font-bold">Detail →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection

@push('scripts')
<script>
function zakatDashboard() {
    return {
        selectedJenis: '',
        hasilHitung: null,
        keteranganHitung: '',
        isCalculating: false,
        tableLoading: true,
        kalkulatorError: null,
        hargaBerasDefault: {{ $hargaBeras ?? 14000 }},
        form: {
            jumlahJiwa: 1,
            hargaBeras: {{ $hargaBeras ?? 14000 }},
            penghasilan: 0,
            nilaiPerniagaan: 0,
            beratEmas: 0,
            hargaEmas: 1000000,
            utang: 0,
        },
        jenisOptions: [
            { value: 'fitrah', label: 'Fitrah', icon: '🌾', rate: '2.5kg/jiwa' },
            { value: 'mal', label: 'Mal', icon: '💰', rate: '2.5%' },
            { value: 'profesi', label: 'Profesi', icon: '💼', rate: '2.5%' },
            { value: 'perniagaan', label: 'Perniagaan', icon: '🏪', rate: '2.5%' },
            { value: 'pertanian', label: 'Pertanian', icon: '🌱', rate: '5-10%' },
            { value: 'emas', label: 'Emas & Perak', icon: '🥇', rate: '2.5%' },
        ],

        init() {
            setTimeout(() => { this.tableLoading = false; }, 800);
        },

        selectJenis(jenis) {
            this.selectedJenis = jenis;
            this.hasilHitung = null;
            this.kalkulatorError = null;
        },

        hitungOtomatis() {
            clearTimeout(this._debounce);
            this._debounce = setTimeout(() => { this.hitungViaAjax(); }, 600);
        },

        hitungLocal() {
            const f = this.form;
            if (this.selectedJenis === 'fitrah') {
                return (f.jumlahJiwa || 1) * 2.5 * (f.hargaBeras || this.hargaBerasDefault);
            } else if (this.selectedJenis === 'profesi' || this.selectedJenis === 'mal') {
                return ((f.penghasilan || 0) - (f.utang || 0)) * 0.025;
            } else if (this.selectedJenis === 'perniagaan') {
                return ((f.nilaiPerniagaan || 0) - (f.utang || 0)) * 0.025;
            } else if (this.selectedJenis === 'pertanian') {
                return (f.nilaiPerniagaan || 0) * 0.05;
            } else if (this.selectedJenis === 'emas') {
                return (f.beratEmas || 0) * (f.hargaEmas || 0) * 0.025;
            }
            return 0;
        },

        async hitungViaAjax() {
            if (!this.selectedJenis) return;
            this.isCalculating = true;
            this.kalkulatorError = null;

            // Map field names ke yang diexpect controller
            const f = this.form;
            const payload = {
                jenis_zakat:  this.selectedJenis,
                jumlah_jiwa:  f.jumlahJiwa  || 1,
                harga_beras:  f.hargaBeras  || this.hargaBerasDefault,
                penghasilan:  f.penghasilan || 0,
                nilai_aset:   (this.selectedJenis === 'emas')
                              ? (f.beratEmas || 0) * (f.hargaEmas || 0)
                              : (f.nilaiPerniagaan || 0),
            };

            try {
                const response = await fetch('{{ route('user.donasi.hitung') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    this.hasilHitung = data.jumlah;
                    this.keteranganHitung = data.keterangan || '';
                } else if (response.ok && data.jumlah_zakat !== undefined) {
                    // Fallback jika response lama
                    this.hasilHitung = data.jumlah_zakat;
                    this.keteranganHitung = '';
                } else {
                    this.hasilHitung = this.hitungLocal();
                    this.keteranganHitung = 'Estimasi lokal.';
                }
            } catch (e) {
                const localResult = this.hitungLocal();
                if (localResult > 0) {
                    this.hasilHitung = localResult;
                    this.keteranganHitung = 'Estimasi lokal (offline).';
                } else {
                    this.kalkulatorError = 'Pastikan semua data sudah diisi dengan benar.';
                }
            } finally {
                this.isCalculating = false;
            }
        },

        resetKalkulator() {
            this.selectedJenis = '';
            this.hasilHitung = null;
            this.kalkulatorError = null;
            this.form = {
                jumlahJiwa: 1,
                hargaBeras: this.hargaBerasDefault,
                penghasilan: 0,
                nilaiPerniagaan: 0,
                beratEmas: 0,
                hargaEmas: 1000000,
                utang: 0,
            };
        },

        formatRupiah(val) {
            if (!val) return '0';
            return Number(Math.round(val)).toLocaleString('id-ID');
        }
    }
}
</script>
@endpush
