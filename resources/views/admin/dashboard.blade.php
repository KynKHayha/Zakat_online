@extends('layouts.admin')
@section('title', 'Dashboard Admin')
@section('page-title', 'Dashboard Admin')
@section('page-subtitle', 'Ringkasan data zakat & transaksi')

@push('styles')
<style>
    /* Tile KPI & kotak jenis zakat: naik lembut saat disentuh/hover */
    .stat-card { transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease; cursor: pointer; }
    .stat-card:hover { transform: translateY(-6px); box-shadow: 0 20px 40px -12px rgba(5,150,105,.28); border-color: #a7f3d0; }
    .stat-card:active { transform: translateY(-2px) scale(.995); }

    .jenis-card { transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease; cursor: pointer; }
    .jenis-card:hover { transform: translateY(-5px); box-shadow: 0 18px 36px -14px rgba(15,23,42,.22); }
    .jenis-card:active { transform: translateY(-1px); }

    .quick-link { transition: transform .2s ease, background-color .2s ease, border-color .2s ease; }
    .quick-link:hover { background: rgba(255,255,255,.22); border-color: rgba(255,255,255,.45); transform: translateY(-2px); }

    /* Modal */
    .modal-bg { background: rgba(6,78,59,.55); backdrop-filter: blur(6px); }
    .modal-box { animation: modalIn 0.3s ease; max-height: 88vh; }
    @keyframes modalIn { from { opacity:0; transform:scale(0.92) translateY(16px); } to { opacity:1; transform:scale(1) translateY(0); } }

    [x-cloak] { display:none!important; }
    .table-row-hover:hover { background: #ecfdf5; }
</style>
@endpush

@section('content')

{{-- ══ MODAL DETAIL DAFTAR MUZAKKI ══ --}}
<div x-data="dashboardAdmin()" x-cloak>

    {{-- Modal --}}
    <div x-show="showModal"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 modal-bg"
         @click.self="showModal = false">

        <div class="modal-box bg-white rounded-t-4xl sm:rounded-4xl shadow-glow w-full max-w-4xl overflow-hidden flex flex-col border border-white/50">
            {{-- Modal Header --}}
            <div class="relative overflow-hidden bg-gradient-to-r from-brand-700 to-brand-600 hero-pattern px-5 sm:px-6 py-4 flex items-center justify-between gap-4">
                <div class="relative z-10 min-w-0">
                    <h3 class="text-white font-extrabold text-lg sm:text-xl truncate" x-text="modalJudul">Detail</h3>
                    <p class="text-brand-100 text-sm mt-0.5">
                        <span class="num font-bold" x-text="modalTotal"></span> data ditemukan
                    </p>
                </div>
                <button @click="showModal = false" class="relative z-10 p-2 -mr-2 rounded-xl text-white/70 hover:text-white hover:bg-white/15 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Search Bar di Modal --}}
            <div class="px-5 sm:px-6 py-3.5 border-b border-gray-100 bg-gray-50">
                <label class="sr-only" for="modal-search">Cari data</label>
                <input id="modal-search" type="text" x-model="modalSearch" placeholder="Cari nama / email..."
                    class="fld text-sm bg-white">
            </div>

            {{-- Modal Body --}}
            <div class="overflow-y-auto flex-1 p-4 sm:p-6">
                <template x-if="modalLoading">
                    <div class="flex flex-col items-center justify-center py-14 gap-3">
                        <svg class="animate-spin w-9 h-9 text-brand-500" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                        </svg>
                        <span class="text-gray-500 text-sm font-semibold">Memuat data...</span>
                    </div>
                </template>

                <template x-if="!modalLoading && filteredData.length > 0">
                    <div class="overflow-x-auto rounded-3xl border border-gray-100">
                        <table class="w-full text-sm min-w-[720px]">
                            <thead class="bg-gray-50 sticky top-0">
                                <tr>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">No</th>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Nama</th>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Jenis Zakat</th>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Jumlah</th>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3.5 text-left text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Tanggal</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 bg-white">
                                <template x-for="(row, idx) in filteredData" :key="idx">
                                    <tr class="table-row-hover transition-colors">
                                        <td class="num px-4 py-3.5 text-gray-400 text-xs font-bold" x-text="idx+1"></td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center gap-2.5">
                                                <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-extrabold text-xs flex-shrink-0"
                                                    x-text="row.nama.charAt(0).toUpperCase()"></div>
                                                <span class="font-bold text-gray-800" x-text="row.nama"></span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3.5 text-gray-500 text-xs" x-text="row.email"></td>
                                        <td class="px-4 py-3.5 text-gray-600 font-medium" x-text="row.jenis"></td>
                                        <td class="num px-4 py-3.5 font-extrabold text-brand-700" x-text="row.jumlah"></td>
                                        <td class="px-4 py-3.5">
                                            <span class="status-badge"
                                                :class="{
                                                    'bg-emerald-100 text-emerald-700': row.status.includes('Berhasil') || row.status.includes('sukses') || row.status.includes('transaksi'),
                                                    'bg-amber-100 text-amber-700': row.status.includes('Menunggu'),
                                                    'bg-red-100 text-red-700': row.status.includes('Ditolak'),
                                                    'bg-gray-100 text-gray-600': true
                                                }"
                                                x-text="row.status"></span>
                                        </td>
                                        <td class="num px-4 py-3.5 text-gray-500 text-xs" x-text="row.tanggal"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </template>

                <template x-if="!modalLoading && filteredData.length === 0">
                    <div class="text-center py-14 text-gray-400">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-9 h-9 opacity-60" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <p class="font-bold text-gray-500">Tidak ada data ditemukan.</p>
                        <p class="text-sm mt-1">Coba kata kunci lain atau tutup panel ini.</p>
                    </div>
                </template>
            </div>

            {{-- Footer Modal --}}
            <div class="px-5 sm:px-6 py-4 border-t border-gray-100 bg-gray-50 flex items-center justify-between gap-3">
                <p class="text-xs text-gray-500 font-medium">Menampilkan <span class="num font-bold" x-text="filteredData.length"></span> dari <span class="num font-bold" x-text="modalTotal"></span> data</p>
                <button @click="showModal = false" class="btn btn-ghost !py-2.5 !px-5 text-sm">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    {{-- ══ SUMMARY BAND ══ --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 hero-pattern p-6 sm:p-8 mb-6 shadow-glow fade-up">
        <div class="absolute -top-20 -right-12 w-72 h-72 bg-white/10 rounded-full blur-2xl pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-16 w-64 h-64 bg-brand-400/20 rounded-full blur-2xl pointer-events-none"></div>

        <div class="relative z-10 grid grid-cols-1 xl:grid-cols-3 gap-6 xl:gap-8">
            {{-- Sapaan + saldo --}}
            <div class="xl:col-span-2 min-w-0">
                <span class="chip bg-white/15 text-white border border-white/20">🕌 Panel Amil Zakat</span>
                <h2 class="text-white text-2xl sm:text-3xl font-extrabold tracking-tight mt-3">Ringkasan Operasional</h2>
                <p class="text-brand-200 text-sm mt-1.5">{{ now()->isoFormat('dddd, D MMMM Y') }} — data realtime transaksi & muzakki</p>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mt-6">
                    <div class="rounded-3xl bg-white/10 border border-white/15 p-5 backdrop-blur-sm">
                        <p class="text-xs text-brand-200 font-bold uppercase tracking-wider">Total Saldo Terkumpul</p>
                        <p class="num text-2xl sm:text-3xl font-extrabold text-white leading-tight mt-2">Rp {{ number_format($totalSaldo,0,',','.') }}</p>
                        <p class="text-brand-200 text-xs mt-1.5">Akumulasi transaksi berstatus sukses</p>
                    </div>
                    <div class="rounded-3xl bg-white/10 border border-white/15 p-5 backdrop-blur-sm">
                        <p class="text-xs text-brand-200 font-bold uppercase tracking-wider">Bulan Ini</p>
                        <p class="num text-2xl sm:text-3xl font-extrabold text-white leading-tight mt-2">Rp {{ number_format($saldoBulanIni,0,',','.') }}</p>
                        <p class="text-brand-200 text-xs mt-1.5">Terverifikasi pada {{ now()->isoFormat('MMMM Y') }}</p>
                    </div>
                </div>
            </div>

            {{-- Navigasi cepat --}}
            <div class="rounded-3xl bg-white/10 border border-white/15 p-5 flex flex-col justify-between backdrop-blur-sm">
                <div>
                    <p class="text-xs text-brand-200 font-bold uppercase tracking-wider">Aksi Cepat</p>
                    <p class="text-brand-100/70 text-xs mt-1">Pintas ke halaman administrasi</p>
                </div>
                <div class="mt-4 space-y-2.5">
                    <a href="{{ route('admin.transaksi.index') }}"
                       class="quick-link flex items-center gap-3 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-white">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                        <span class="text-sm font-bold flex-1">Verifikasi Transaksi</span>
                        <span class="num text-xs font-extrabold text-brand-100">{{ number_format($transaksiMenunggu) }}</span>
                    </a>
                    <a href="{{ route('admin.users.index') }}"
                       class="quick-link flex items-center gap-3 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-white">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        <span class="text-sm font-bold flex-1">Kelola Muzakki</span>
                        <span class="num text-xs font-extrabold text-brand-100">{{ number_format($totalUser) }}</span>
                    </a>
                    <a href="{{ route('admin.laporan.index') }}"
                       class="quick-link flex items-center gap-3 rounded-2xl border border-white/20 bg-white/10 px-4 py-3 text-white">
                        <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span class="text-sm font-bold flex-1">Laporan Keuangan</span>
                        <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ 4 STAT CARDS UTAMA (semua clickable) ══ --}}
    <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 sm:gap-5 mb-6">

        {{-- Total Muzakki --}}
        <div class="stat-card bg-white rounded-3xl p-5 shadow-soft border border-gray-100"
             @click="loadDetail('semua')"
             title="Klik untuk lihat daftar semua muzakki">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <span class="chip bg-blue-50 text-blue-700">Muzakki</span>
            </div>
            <p class="num text-3xl font-extrabold text-gray-900">{{ number_format($totalUser) }}</p>
            <p class="text-sm text-gray-500 font-medium mt-0.5">Total Muzakki</p>
            <p class="text-xs text-blue-600 font-bold mt-2">Klik untuk detail →</p>
        </div>

        {{-- Menunggu Verifikasi --}}
        <div class="stat-card bg-white rounded-3xl p-5 shadow-soft border border-gray-100 relative"
             @click="loadDetail('menunggu')"
             title="Klik untuk lihat transaksi menunggu">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-100 flex items-center justify-center flex-shrink-0 relative">
                    <svg class="w-6 h-6 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    @if($transaksiMenunggu > 0)
                    <span class="absolute -top-1.5 -right-1.5 min-w-[20px] h-5 px-1 bg-red-500 rounded-full flex items-center justify-center text-white text-[10px] font-extrabold animate-pulse">{{ $transaksiMenunggu }}</span>
                    @endif
                </div>
                <span class="chip bg-amber-50 text-amber-700">Perlu Cek</span>
            </div>
            <p class="num text-3xl font-extrabold text-amber-600">{{ number_format($transaksiMenunggu) }}</p>
            <p class="text-sm text-gray-500 font-medium mt-0.5">Menunggu Verifikasi</p>
            <p class="text-xs text-amber-600 font-bold mt-2">Klik untuk detail →</p>
        </div>

        {{-- Transaksi Sukses --}}
        <div class="stat-card bg-white rounded-3xl p-5 shadow-soft border border-gray-100"
             @click="loadDetail('sukses')"
             title="Klik untuk lihat transaksi sukses">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-brand-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="chip bg-brand-50 text-brand-700">✓ Lunas</span>
            </div>
            <p class="num text-3xl font-extrabold text-brand-700">{{ number_format($transaksiSukses) }}</p>
            <p class="text-sm text-gray-500 font-medium mt-0.5">Transaksi Sukses</p>
            <p class="text-xs text-brand-600 font-bold mt-2">Klik untuk detail →</p>
        </div>

        {{-- Total Transaksi --}}
        <div class="stat-card bg-white rounded-3xl p-5 shadow-soft border border-gray-100"
             @click="loadDetail('semua')"
             title="Klik untuk lihat daftar semua muzakki">
            <div class="flex items-center justify-between mb-4">
                <div class="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7-4h12M5 5h14a2 2 0 012 2v10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2z"/>
                    </svg>
                </div>
                <span class="chip bg-purple-50 text-purple-700">Semua</span>
            </div>
            <p class="num text-3xl font-extrabold text-gray-900">{{ number_format($totalTransaksi) }}</p>
            <p class="text-sm text-gray-500 font-medium mt-0.5">Total Transaksi</p>
            <p class="text-xs text-purple-600 font-bold mt-2">Klik untuk detail →</p>
        </div>
    </section>

    {{-- ══ 6 KOTAK JENIS ZAKAT (Dino) ══ --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-5">
            <div class="min-w-0">
                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 flex items-center gap-2">
                    <span class="text-xl">📊</span> Distribusi per Jenis Zakat
                </h2>
                <p class="text-gray-500 text-sm mt-1">Klik kotak untuk melihat daftar muzakki per jenis zakat</p>
            </div>
            {{-- Tombol Kirim Pengingat (Adirja) --}}
            <button @click="kirimPengingat()"
                    :disabled="pengingat.loading"
                    class="inline-flex items-center gap-2.5 px-5 py-3.5 min-h-[48px] rounded-2xl text-sm font-extrabold text-white shadow-soft transition-all bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 hover:shadow-glow disabled:from-amber-300 disabled:to-amber-300 disabled:hover:shadow-soft active:scale-[.99]">
                <svg x-show="pengingat.loading" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                </svg>
                <svg x-show="!pengingat.loading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <span x-text="pengingat.loading ? 'Mengirim...' : `Kirim Pengingat ({{ $userBelumBayar }} user)`"></span>
            </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 xl:grid-cols-6 gap-3 sm:gap-4">
            @foreach($statPerJenis as $jenis => $stat)
            @php
                $colorMap = [
                    'fitrah'     => ['bg'=>'bg-emerald-50','border'=>'border-emerald-200','text'=>'text-emerald-700','badge'=>'bg-emerald-100 text-emerald-800','icon_bg'=>'bg-emerald-100'],
                    'mal'        => ['bg'=>'bg-blue-50','border'=>'border-blue-200','text'=>'text-blue-700','badge'=>'bg-blue-100 text-blue-800','icon_bg'=>'bg-blue-100'],
                    'profesi'    => ['bg'=>'bg-purple-50','border'=>'border-purple-200','text'=>'text-purple-700','badge'=>'bg-purple-100 text-purple-800','icon_bg'=>'bg-purple-100'],
                    'perniagaan' => ['bg'=>'bg-orange-50','border'=>'border-orange-200','text'=>'text-orange-700','badge'=>'bg-orange-100 text-orange-800','icon_bg'=>'bg-orange-100'],
                    'pertanian'  => ['bg'=>'bg-green-50','border'=>'border-green-200','text'=>'text-green-700','badge'=>'bg-green-100 text-green-800','icon_bg'=>'bg-green-100'],
                    'emas_perak' => ['bg'=>'bg-yellow-50','border'=>'border-yellow-200','text'=>'text-yellow-700','badge'=>'bg-yellow-100 text-yellow-800','icon_bg'=>'bg-yellow-100'],
                ];
                $c = $colorMap[$jenis] ?? $colorMap['fitrah'];
            @endphp
            <div class="jenis-card {{ $c['bg'] }} border-2 {{ $c['border'] }} rounded-3xl p-4 sm:p-5 flex flex-col items-center text-center"
                 @click="loadDetail('{{ $jenis }}')"
                 title="Klik untuk detail {{ $stat['label'] }}">
                <div class="w-12 h-12 {{ $c['icon_bg'] }} rounded-2xl flex items-center justify-center text-2xl mb-2">{{ $stat['icon'] }}</div>
                <p class="text-[11px] sm:text-xs font-extrabold {{ $c['text'] }} mb-1 leading-tight">{{ $stat['label'] }}</p>
                <p class="num text-2xl sm:text-3xl font-black text-gray-900">{{ $stat['total_muzakki'] }}</p>
                <p class="text-xs text-gray-500 mt-0.5 font-medium">muzakki</p>
                <div class="mt-2 w-full rounded-xl bg-white/70 border border-white/80 px-2 py-1.5">
                    <p class="num text-xs {{ $c['text'] }} font-extrabold truncate">
                        Rp {{ number_format($stat['total_nominal'],0,',','.') }}
                    </p>
                </div>
                @if($stat['menunggu'] > 0)
                <span class="{{ $c['badge'] }} status-badge text-[10px] mt-2">
                    ⏳ {{ $stat['menunggu'] }} pending
                </span>
                @endif
                <p class="text-[10px] text-gray-400 mt-2 font-bold">Klik untuk detail</p>
            </div>
            @endforeach
        </div>
    </section>

    {{-- ══ NOTIFIKASI PENGINGAT STATUS ══ --}}
    <div x-show="pengingat.result" x-cloak x-transition
         class="mb-6 p-4 sm:p-5 rounded-3xl border flex items-center gap-3 shadow-card"
         :class="pengingat.success ? 'bg-emerald-50 border-emerald-200' : 'bg-red-50 border-red-200'">
        <span x-text="pengingat.result" :class="pengingat.success ? 'text-emerald-700' : 'text-red-700'" class="text-sm font-semibold"></span>
        <button @click="pengingat.result = ''" class="ml-auto p-2 -mr-2 rounded-xl text-gray-400 hover:text-gray-600 hover:bg-white/70 transition-colors">✕</button>
    </div>

    {{-- ══ CHARTS ROW ══ --}}
    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5 sm:gap-6 mb-6">
        <div class="xl:col-span-2 bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
                <div>
                    <h3 class="text-base sm:text-lg font-extrabold text-gray-900 flex items-center gap-2">
                        <span>📈</span> Pemasukan Zakat per Bulan ({{ date('Y') }})
                    </h3>
                    <p class="text-xs text-gray-500 mt-0.5">Total nominal transaksi sukses tiap bulan</p>
                </div>
                <span class="chip bg-brand-50 text-brand-700">Tahun {{ date('Y') }}</span>
            </div>
            <div class="relative w-full h-64 sm:h-80">
                <canvas id="chartBulanan" height="120"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-6">
            <div class="flex flex-wrap items-center justify-between gap-2 mb-5">
                <h3 class="text-base sm:text-lg font-extrabold text-gray-900 flex items-center gap-2">
                    <span>💳</span> Metode Pembayaran
                </h3>
                <span class="chip bg-gray-100 text-gray-600">Distribusi</span>
            </div>
            <div class="relative w-full h-64 sm:h-80">
                <canvas id="chartMetode" height="200"></canvas>
            </div>
            <div class="flex flex-wrap justify-center gap-3 sm:gap-6 mt-4">
                <div class="flex items-center gap-2 text-sm text-gray-600 font-semibold"><div class="w-3 h-3 rounded-full bg-blue-400"></div>Online <span class="num">({{ number_format($onlineCount) }})</span></div>
                <div class="flex items-center gap-2 text-sm text-gray-600 font-semibold"><div class="w-3 h-3 rounded-full bg-orange-400"></div>Di Tempat <span class="num">({{ number_format($dittempatCount) }})</span></div>
            </div>
        </div>
    </div>

    {{-- ══ TABEL TRANSAKSI MENUNGGU ══ --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 sm:py-5 border-b border-gray-100 flex flex-wrap items-center justify-between gap-3">
            <div class="min-w-0">
                <h3 class="text-base sm:text-lg font-extrabold text-gray-900 flex items-center gap-2 flex-wrap">
                    <span class="w-9 h-9 rounded-2xl bg-amber-100 flex items-center justify-center text-base flex-shrink-0">⏳</span>
                    Transaksi Menunggu Verifikasi
                    @if($transaksiMenunggu > 0)
                    <span class="num status-badge bg-red-100 text-red-700">{{ $transaksiMenunggu }}</span>
                    @endif
                </h3>
                <p class="text-xs text-gray-500 mt-1.5">10 pengajuan terbaru yang perlu diperiksa amil</p>
            </div>
            <a href="{{ route('admin.transaksi.index') }}" class="btn btn-outline !py-2.5 !px-4 text-sm">
                Lihat Semua
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        @if($transaksiTerbaru->count())
        {{-- Kartu (mobile) --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($transaksiTerbaru as $trx)
            <div class="p-4 bg-white">
                <div class="flex items-start gap-3">
                    <div class="w-11 h-11 rounded-2xl bg-brand-50 flex items-center justify-center text-brand-700 font-extrabold text-sm flex-shrink-0">
                        {{ strtoupper(substr($trx->user?->name ?? 'U', 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-extrabold text-gray-900 truncate">{{ $trx->user?->name ?? '-' }}</p>
                        <p class="text-xs text-gray-400 truncate">{{ $trx->user?->email ?? '' }}</p>
                        <p class="num font-mono text-[11px] font-bold text-gray-600 bg-gray-100 inline-block px-2 py-1 rounded-lg mt-1.5">{{ $trx->kode_transaksi }}</p>
                    </div>
                    <div class="text-right flex-shrink-0">
                        <p class="num text-sm font-extrabold text-gray-900 leading-tight">Rp {{ number_format($trx->total_bayar,0,',','.') }}</p>
                        @if($trx->metode_bayar === 'online')
                        <span class="status-badge bg-blue-50 text-blue-700 mt-1.5">Online</span>
                        @else
                        <span class="status-badge bg-orange-50 text-orange-700 mt-1.5">Di Tempat</span>
                        @endif
                    </div>
                </div>
                <div class="mt-3 flex items-center justify-between gap-3">
                    <span class="chip bg-gray-100 text-gray-600">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</span>
                    <a href="{{ route('admin.transaksi.show', $trx->id) }}"
                        class="btn btn-primary !py-2.5 !px-4 text-xs">
                        Verifikasi
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Tabel (desktop) --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm min-w-[840px]">
                <thead>
                    <tr class="bg-gray-50 text-left">
                        <th class="px-6 py-3.5 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3.5 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Muzakki</th>
                        <th class="px-6 py-3.5 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Jenis</th>
                        <th class="px-6 py-3.5 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3.5 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3.5 text-[11px] font-extrabold text-gray-500 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksiTerbaru as $trx)
                    <tr class="table-row-hover transition-colors">
                        <td class="px-6 py-4">
                            <span class="num font-mono text-xs font-bold text-brand-700 bg-brand-50 border border-brand-100 px-2.5 py-1.5 rounded-xl">{{ $trx->kode_transaksi }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-brand-100 flex items-center justify-center text-brand-700 font-extrabold text-xs flex-shrink-0">
                                    {{ strtoupper(substr($trx->user?->name ?? 'U', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="text-gray-800 font-bold">{{ $trx->user?->name ?? '-' }}</p>
                                    <p class="text-gray-400 text-xs">{{ $trx->user?->email ?? '' }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-gray-600 font-medium">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</td>
                        <td class="px-6 py-4">
                            @if($trx->metode_bayar === 'online')
                            <span class="status-badge bg-blue-50 text-blue-700">Online</span>
                            @else
                            <span class="status-badge bg-orange-50 text-orange-700">Di Tempat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="num font-extrabold text-gray-900">Rp {{ number_format($trx->total_bayar,0,',','.') }}</span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.transaksi.show', $trx->id) }}"
                                class="btn btn-primary !py-2.5 !px-4 text-xs">
                                Verifikasi
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="w-16 h-16 bg-brand-50 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                ✅
            </div>
            <p class="text-gray-800 font-extrabold mb-1">Semua transaksi sudah diverifikasi!</p>
            <p class="text-gray-400 text-sm">Tidak ada antrean menunggu tindakan amil saat ini.</p>
        </div>
        @endif
    </section>

</div>{{-- end x-data --}}
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Charts
const monthNames = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];
const chartData  = @json($chartData);
const labels     = monthNames;
const dataValues = Array.from({length:12}, (_, i) => {
    const f = chartData.find(d => parseInt(d.bulan) === i+1);
    return f ? parseFloat(f.total) : 0;
});

new Chart(document.getElementById('chartBulanan'), {
    type: 'bar',
    data: { labels, datasets: [{ label: 'Total Zakat (Rp)', data: dataValues, backgroundColor: 'rgba(5,150,105,0.85)', borderRadius: 8 }] },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { ticks: { callback: v => 'Rp ' + (v/1000000).toFixed(1) + 'jt' }, grid: { color: '#f1f5f9' } },
            x: { grid: { display: false } }
        }
    }
});

new Chart(document.getElementById('chartMetode'), {
    type: 'doughnut',
    data: { labels: ['Online','Di Tempat'], datasets: [{ data: [{{ $onlineCount }}, {{ $dittempatCount }}], backgroundColor: ['#60a5fa','#fb923c'], borderWidth: 0 }] },
    options: { responsive: true, maintainAspectRatio: false, cutout: '65%', plugins: { legend: { display: false } } }
});

// Tailwind CDN memberi tinggi wrapper setelah Chart.js mengukur, jadi ukur ulang saat load.
window.addEventListener('load', () => {
    ['chartBulanan', 'chartMetode'].forEach(id => Chart.getChart(id)?.resize());
});

// Alpine component
function dashboardAdmin() {
    return {
        showModal:   false,
        modalJudul:  '',
        modalTotal:  0,
        modalData:   [],
        modalSearch: '',
        modalLoading: false,
        pengingat: { loading: false, result: '', success: false },

        get filteredData() {
            if (!this.modalSearch) return this.modalData;
            const q = this.modalSearch.toLowerCase();
            return this.modalData.filter(r =>
                r.nama.toLowerCase().includes(q) ||
                r.email.toLowerCase().includes(q) ||
                r.jenis.toLowerCase().includes(q)
            );
        },

        async loadDetail(filter) {
            this.showModal   = true;
            this.modalLoading = true;
            this.modalSearch  = '';
            this.modalData   = [];

            try {
                const res  = await fetch(`{{ route('admin.dashboard.detail') }}?filter=${filter}`, {
                    headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, 'Accept': 'application/json' }
                });
                const json = await res.json();
                this.modalJudul  = json.judul;
                this.modalTotal  = json.total;
                this.modalData   = json.data;
            } catch(e) {
                this.modalData  = [];
                this.modalJudul = 'Error memuat data';
            } finally {
                this.modalLoading = false;
            }
        },

        async kirimPengingat() {
            if (!confirm('Kirim notifikasi pengingat ke semua user yang belum bayar zakat?')) return;
            this.pengingat.loading = true;
            this.pengingat.result  = '';
            try {
                const res  = await fetch('{{ route('admin.notifikasi.kirim-pengingat') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    }
                });
                const json = await res.json();
                this.pengingat.success = json.success;
                this.pengingat.result  = json.message;
            } catch(e) {
                this.pengingat.success = false;
                this.pengingat.result  = 'Gagal mengirim pengingat.';
            } finally {
                this.pengingat.loading = false;
            }
        }
    }
}
</script>
@endpush
