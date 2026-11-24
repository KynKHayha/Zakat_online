@extends('layouts.admin')
@section('title', 'Riwayat User')
@section('page-title', 'Riwayat Transaksi User')
@section('page-subtitle', 'Detail riwayat zakat muzakki')

@php
    $jenisIcon = fn ($jenis) => match ($jenis) {
        'fitrah'               => '🌾',
        'mal'                  => '💰',
        'profesi'              => '💼',
        'perniagaan'           => '🏪',
        'pertanian'            => '🌱',
        'emas', 'emas_perak'   => '🥇',
        default                => '📦',
    };
    $statusStyle = [
        'menunggu'   => 'bg-amber-100 text-amber-700',
        'sukses'     => 'bg-brand-100 text-brand-700',
        'ditolak'    => 'bg-red-100 text-red-700',
        'dibatalkan' => 'bg-gray-100 text-gray-600',
    ];
@endphp

@section('content')
<div x-data="{ tab: 'transaksi' }" class="space-y-5">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-brand-600 transition-colors group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Daftar User
        </a>
        <a href="{{ route('admin.users.laporan-pdf', $user->id) }}" class="btn btn-primary self-start sm:self-auto shadow-glow">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 012 2v2a2 2 0 01-2 2H5a2 2 0 01-2-2v-2a2 2 0 012-2h2m6-9V3m0 7l3-3m-3 3l-3-3m3 7v7m0 0l3-3m-3 3l-3-3"/></svg>
            🖨️ Cetak Laporan PDF
        </a>
    </div>

    {{-- ══ PROFILE + KPI ══ --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 hero-pattern p-5 sm:p-7 text-white shadow-glow">
        <div class="relative z-10">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-4xl bg-white/20 border border-white/25 flex items-center justify-center text-white font-extrabold text-2xl sm:text-3xl flex-shrink-0">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-lg sm:text-2xl font-extrabold truncate">{{ $user->name }}</h2>
                    <p class="text-brand-100/90 text-sm truncate">{{ $user->email }}</p>
                    @if($user->phone) <p class="num text-brand-200/80 text-xs mt-1">{{ $user->phone }}</p> @endif
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6">
                <div class="bg-white/10 border border-white/15 rounded-3xl p-4">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Total Transaksi</p>
                    <p class="num text-3xl font-extrabold mt-1.5">{{ $totalTransaksi }}</p>
                    <p class="text-brand-200/80 text-xs mt-1">seluruh periode</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-3xl p-4">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Total Dibayar</p>
                    <p class="num text-xl sm:text-2xl font-extrabold mt-2.5">Rp {{ number_format($totalDibayar,0,',','.') }}</p>
                    <p class="text-brand-200/80 text-xs mt-1">status sukses</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-3xl p-4">
                    <p class="text-amber-200 text-[11px] font-bold uppercase tracking-wider">Total Utang</p>
                    <p class="num text-xl sm:text-2xl font-extrabold mt-2.5">Rp {{ number_format($utangs->sum('jumlah_zakat'),0,',','.') }}</p>
                    <p class="text-amber-200/80 text-xs mt-1">belum diselesaikan</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ TAB PILIH ══ --}}
    <div class="grid grid-cols-2 gap-2 p-1.5 bg-white rounded-4xl shadow-soft border border-gray-100">
        <button @click="tab = 'transaksi'"
            :class="tab === 'transaksi' ? 'bg-brand-600 text-white shadow-glow' : 'text-gray-500 hover:text-gray-700'"
            class="flex items-center justify-center gap-2 rounded-3xl px-3 py-3 text-sm font-bold transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
            <span class="truncate">Riwayat Transaksi</span>
        </button>
        <button @click="tab = 'utang'"
            :class="tab === 'utang' ? 'bg-red-500 text-white shadow-lg shadow-red-200/70' : 'text-gray-500 hover:text-gray-700'"
            class="flex items-center justify-center gap-2 rounded-3xl px-3 py-3 text-sm font-bold transition-all">
            <span class="truncate">Belum Bayar / Utang</span>
            @if($utangs->count()) <span class="chip" :class="tab === 'utang' ? 'bg-white/25 text-white' : 'bg-red-100 text-red-600'">{{ $utangs->count() }}</span> @endif
        </button>
    </div>

    <!-- TAB: TRANSAKSI -->
    <div x-show="tab === 'transaksi'" class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-gray-900 text-sm sm:text-base">Semua Transaksi Zakat</h3>
            <span class="chip bg-brand-50 text-brand-700"><span class="num">{{ $transaksis->total() }}</span> data</span>
        </div>

        @if($transaksis->count())
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50 text-left">
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Jenis Zakat</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Jumlah</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksis as $trx)
                    <tr class="hover:bg-brand-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <span class="num text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1.5 rounded-lg">{{ $trx->kode_transaksi }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                                <span class="text-gray-600 font-semibold">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            @if($trx->metode_bayar==='online')
                            <span class="chip bg-blue-50 text-blue-700">🌐 Online</span>
                            @else
                            <span class="chip bg-orange-50 text-orange-700">📍 Di Tempat</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="num font-extrabold text-gray-900">Rp {{ number_format($trx->total_bayar,0,',','.') }}</span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="status-badge {{ $statusStyle[$trx->status]??'bg-gray-100 text-gray-600' }}">{{ $trx->label_status }}</span>
                        </td>
                        <td class="px-6 py-4 num text-xs text-gray-500">{{ $trx->tanggal_transaksi?->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="btn btn-ghost !py-2 !px-3 text-xs">Detail &rarr;</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($transaksis as $trx)
            <div class="p-4">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="text-xl">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</p>
                            <p class="num text-xs text-gray-400">{{ $trx->kode_transaksi }}</p>
                        </div>
                    </div>
                    <span class="status-badge {{ $statusStyle[$trx->status]??'bg-gray-100 text-gray-600' }} flex-shrink-0">{{ $trx->label_status }}</span>
                </div>
                <p class="num text-2xl font-extrabold text-brand-700 mb-3">Rp {{ number_format($trx->total_bayar,0,',','.') }}</p>
                <div class="flex items-center justify-between gap-3">
                    <div class="flex flex-wrap items-center gap-2">
                        @if($trx->metode_bayar==='online')
                        <span class="chip bg-blue-50 text-blue-700">🌐 Online</span>
                        @else
                        <span class="chip bg-orange-50 text-orange-700">📍 Di Tempat</span>
                        @endif
                        <span class="num text-xs text-gray-400">{{ $trx->tanggal_transaksi?->format('d M Y') }}</span>
                    </div>
                    <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="btn btn-ghost !py-2 !px-3 text-xs flex-shrink-0">Detail &rarr;</a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-gray-100">{{ $transaksis->links() }}</div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="text-5xl mb-3">🧾</div>
            <p class="text-gray-800 font-extrabold">Belum ada transaksi</p>
            <p class="text-gray-400 text-sm mt-1">User ini belum memiliki transaksi.</p>
        </div>
        @endif
    </div>

    <!-- TAB: UTANG -->
    <div x-show="tab === 'utang'" x-cloak class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 bg-red-50/60 border-b border-red-100 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-gray-900 text-sm sm:text-base">Daftar Utang Zakat</h3>
            <span class="chip bg-red-100 text-red-700"><span class="num">{{ $utangs->count() }}</span> item</span>
        </div>
        @if($utangs->count())
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-red-50 text-left">
                        <th class="px-6 py-3.5 text-xs font-bold text-red-700 uppercase tracking-wider">Jenis Zakat</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-red-700 uppercase tracking-wider text-right">Nominal</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-red-700 uppercase tracking-wider">Dibuat Pada</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($utangs as $utang)
                    <tr class="hover:bg-red-50/50 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-2.5">
                                <span class="text-lg">{{ $jenisIcon($utang->jenis_zakat ?? '') }}</span>
                                <span class="text-gray-800 font-bold">{{ $utang->label_jenis_zakat }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="num font-extrabold text-red-600">Rp {{ number_format($utang->jumlah_zakat, 0, ',', '.') }}</span>
                        </td>
                        <td class="px-6 py-4 num text-xs text-gray-500">{{ $utang->created_at->format('d M Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($utangs as $utang)
            <div class="p-4 flex items-center justify-between gap-3">
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="text-xl">{{ $jenisIcon($utang->jenis_zakat ?? '') }}</span>
                    <div class="min-w-0">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $utang->label_jenis_zakat }}</p>
                        <p class="num text-xs text-gray-400">{{ $utang->created_at->format('d M Y H:i') }}</p>
                    </div>
                </div>
                <span class="num font-extrabold text-red-600 text-right flex-shrink-0">Rp {{ number_format($utang->jumlah_zakat, 0, ',', '.') }}</span>
            </div>
            @endforeach
        </div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="text-5xl mb-3">🎉</div>
            <p class="text-gray-800 font-extrabold">Alhamdulillah, tidak ada utang</p>
            <p class="text-gray-400 text-sm mt-1">User ini tidak memiliki utang zakat.</p>
        </div>
        @endif
    </div>
</div>
@endsection
