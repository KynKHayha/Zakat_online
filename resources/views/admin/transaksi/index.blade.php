@extends('layouts.admin')
@section('title', 'Manajemen Transaksi')
@section('page-title', 'Manajemen Transaksi')
@section('page-subtitle', 'Verifikasi dan kelola seluruh transaksi zakat')

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
<div x-data="{ showTerimaModal: false, showTolakModal: false, selectedId: null, selectedKode: '' }" class="space-y-5">

    {{-- ══ HERO + KPI ══ --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 hero-pattern p-5 sm:p-7 text-white shadow-glow">
        <div class="relative z-10">
            <p class="text-brand-200 text-[11px] font-bold uppercase tracking-[0.2em]">Panel Amil</p>
            <h2 class="text-2xl sm:text-3xl font-extrabold mt-1.5">Manajemen Transaksi</h2>
            <p class="text-brand-100/80 text-sm mt-1.5 leading-relaxed">Verifikasi pembayaran zakat muzakki dengan cepat, rapi, dan transparan.</p>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mt-6">
                <div class="bg-white/10 border border-white/15 rounded-3xl p-4">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">⏳ Menunggu Verifikasi</p>
                    <p class="num text-3xl font-extrabold mt-2">{{ $totalMenunggu }}</p>
                    <p class="text-brand-200/80 text-xs mt-1">transaksi perlu ditindaklanjuti</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-3xl p-4">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">💰 Total Saldo Masuk</p>
                    <p class="num text-xl sm:text-2xl font-extrabold mt-2.5">Rp {{ number_format($totalSaldo,0,',','.') }}</p>
                    <p class="text-brand-200/80 text-xs mt-1">akumulasi transaksi sukses</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-3xl p-4">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">📋 Total Transaksi</p>
                    <p class="num text-3xl font-extrabold mt-2">{{ $transaksis->total() }}</p>
                    <p class="text-brand-200/80 text-xs mt-1">sesuai filter saat ini</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ══ FILTER ══ --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 sm:py-5">
            <form method="GET" action="{{ route('admin.transaksi.index') }}"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-2 lg:col-span-3">
                    <label class="fld-label">Cari</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nama / kode transaksi..." class="fld">
                </div>
                <div class="lg:col-span-2">
                    <label class="fld-label">Status</label>
                    <select name="status" class="fld">
                        <option value="">Semua Status</option>
                        <option value="menunggu" {{ request('status')=='menunggu'?'selected':'' }}>Menunggu</option>
                        <option value="sukses" {{ request('status')=='sukses'?'selected':'' }}>Sukses</option>
                        <option value="ditolak" {{ request('status')=='ditolak'?'selected':'' }}>Ditolak</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="fld-label">Jenis Zakat</label>
                    <select name="jenis_zakat" class="fld">
                        <option value="">Semua Jenis Zakat</option>
                        <option value="fitrah" {{ request('jenis_zakat') == 'fitrah' ? 'selected' : '' }}>Zakat Fitrah</option>
                        <option value="mal" {{ request('jenis_zakat') == 'mal' ? 'selected' : '' }}>Zakat Mal</option>
                        <option value="profesi" {{ request('jenis_zakat') == 'profesi' ? 'selected' : '' }}>Zakat Profesi</option>
                        <option value="perniagaan" {{ request('jenis_zakat') == 'perniagaan' ? 'selected' : '' }}>Zakat Perniagaan</option>
                        <option value="pertanian" {{ request('jenis_zakat') == 'pertanian' ? 'selected' : '' }}>Zakat Pertanian</option>
                        <option value="emas_perak" {{ request('jenis_zakat') == 'emas_perak' ? 'selected' : '' }}>Zakat Emas & Perak</option>
                    </select>
                </div>
                <div class="lg:col-span-2">
                    <label class="fld-label">Metode</label>
                    <select name="metode_bayar" class="fld">
                        <option value="">Semua Metode</option>
                        <option value="online" {{ request('metode_bayar')=='online'?'selected':'' }}>Online</option>
                        <option value="ditempat" {{ request('metode_bayar')=='ditempat'?'selected':'' }}>Di Tempat</option>
                    </select>
                </div>
                <div>
                    <label class="fld-label">Dari</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="fld">
                </div>
                <div>
                    <label class="fld-label">Sampai</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="fld">
                </div>
                <div class="sm:col-span-2 lg:col-span-12 flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1 lg:flex-none !px-6">Terapkan Filter</button>
                    <a href="{{ route('admin.transaksi.index') }}" class="btn btn-ghost flex-1 lg:flex-none !px-6">Reset</a>
                </div>
            </form>
        </div>

        {{-- ══ DAFTAR TRANSAKSI ══ --}}
        <div class="px-4 sm:px-6 py-4 bg-gray-50/70 border-y border-gray-100 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-gray-900 text-sm sm:text-base">Daftar Transaksi</h3>
            <span class="chip bg-brand-50 text-brand-700"><span class="num">{{ $transaksis->total() }}</span> transaksi</span>
        </div>

        @if($transaksis->count())
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50 text-left">
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Muzakki</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Jenis</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Metode</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Jumlah</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Status</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksis as $trx)
                    <tr class="hover:bg-brand-50/40 transition-colors">
                        <td class="px-5 py-4">
                            <a href="{{ route('admin.transaksi.show', $trx->id) }}"
                               class="num text-xs bg-gray-100 hover:bg-brand-100 px-2.5 py-1.5 rounded-lg font-bold text-brand-700 transition-colors">{{ $trx->kode_transaksi }}</a>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2.5">
                                <span class="w-9 h-9 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-xs font-extrabold flex-shrink-0">
                                    {{ strtoupper(substr($trx->user?->name ?? '?', 0, 1)) }}
                                </span>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 text-sm truncate">{{ $trx->user?->name }}</p>
                                    <p class="text-xs text-gray-400 truncate">{{ $trx->user?->email }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <span class="text-lg">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                                <span class="text-gray-600 text-xs font-semibold">{{ $trx->donasiZakat?->label_jenis_zakat }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            @if($trx->metode_bayar==='online')
                            <span class="chip bg-blue-50 text-blue-700">🌐 Online</span>
                            @else
                            <span class="chip bg-orange-50 text-orange-700">📍 Di Tempat</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <span class="num font-extrabold text-gray-900">Rp {{ number_format($trx->total_bayar,0,',','.') }}</span>
                        </td>
                        <td class="px-5 py-4">
                            <span class="status-badge {{ $statusStyle[$trx->status] ?? 'bg-gray-100 text-gray-600' }}">
                                {{ $trx->label_status }}
                            </span>
                        </td>
                        <td class="px-5 py-4 num text-xs text-gray-500">{{ $trx->tanggal_transaksi?->format('d M Y') }}</td>
                        <td class="px-5 py-4">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="btn btn-ghost !py-2 !px-3 text-xs">Detail</a>
                                @if($trx->status === 'menunggu')
                                    @if($trx->metode_bayar === 'online' && !$trx->pembayaran?->bukti_transfer)
                                        <button disabled class="btn !py-2 !px-3 text-xs" title="Belum ada bukti pembayaran">Menunggu Bukti</button>
                                        <button @click="selectedId={{ $trx->id }}; selectedKode='{{ $trx->kode_transaksi }}'; showTolakModal=true"
                                            class="btn bg-red-500 hover:bg-red-600 text-white !py-2 !px-3 text-xs shadow-lg shadow-red-200/60">Tolak</button>
                                    @else
                                        <button @click="selectedId={{ $trx->id }}; selectedKode='{{ $trx->kode_transaksi }}'; showTerimaModal=true"
                                            class="btn btn-primary !py-2 !px-3 text-xs">Terima</button>
                                        <button @click="selectedId={{ $trx->id }}; selectedKode='{{ $trx->kode_transaksi }}'; showTolakModal=true"
                                            class="btn bg-red-500 hover:bg-red-600 text-white !py-2 !px-3 text-xs shadow-lg shadow-red-200/60">Tolak</button>
                                    @endif
                                @endif
                            </div>
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
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <span class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white text-sm font-extrabold flex-shrink-0">
                            {{ strtoupper(substr($trx->user?->name ?? '?', 0, 1)) }}
                        </span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $trx->user?->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $trx->user?->email }}</p>
                        </div>
                    </div>
                    <span class="status-badge {{ $statusStyle[$trx->status] ?? 'bg-gray-100 text-gray-600' }} flex-shrink-0">{{ $trx->label_status }}</span>
                </div>

                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                    <span class="text-xs font-bold text-gray-600">{{ $trx->donasiZakat?->label_jenis_zakat }}</span>
                </div>

                <p class="num text-2xl font-extrabold text-brand-700 mb-3">Rp {{ number_format($trx->total_bayar,0,',','.') }}</p>

                <div class="flex flex-wrap items-center gap-2 mb-3">
                    @if($trx->metode_bayar==='online')
                    <span class="chip bg-blue-50 text-blue-700">🌐 Online</span>
                    @else
                    <span class="chip bg-orange-50 text-orange-700">📍 Di Tempat</span>
                    @endif
                    <span class="num text-xs text-gray-400">{{ $trx->tanggal_transaksi?->format('d M Y') }}</span>
                </div>

                <div class="flex items-center justify-between gap-3 pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.transaksi.show', $trx->id) }}"
                       class="num text-xs bg-gray-100 px-2.5 py-1.5 rounded-lg font-bold text-brand-700">{{ $trx->kode_transaksi }}</a>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <a href="{{ route('admin.transaksi.show', $trx->id) }}" class="btn btn-ghost !py-2 !px-3 text-xs">Detail</a>
                        @if($trx->status === 'menunggu')
                            @if($trx->metode_bayar === 'online' && !$trx->pembayaran?->bukti_transfer)
                                <button disabled class="btn !py-2 !px-3 text-xs" title="Belum ada bukti pembayaran">Menunggu Bukti</button>
                                <button @click="selectedId={{ $trx->id }}; selectedKode='{{ $trx->kode_transaksi }}'; showTolakModal=true"
                                    class="btn bg-red-500 hover:bg-red-600 text-white !py-2 !px-3 text-xs">Tolak</button>
                            @else
                                <button @click="selectedId={{ $trx->id }}; selectedKode='{{ $trx->kode_transaksi }}'; showTerimaModal=true"
                                    class="btn btn-primary !py-2 !px-3 text-xs">Terima</button>
                                <button @click="selectedId={{ $trx->id }}; selectedKode='{{ $trx->kode_transaksi }}'; showTolakModal=true"
                                    class="btn bg-red-500 hover:bg-red-600 text-white !py-2 !px-3 text-xs">Tolak</button>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-gray-100">{{ $transaksis->withQueryString()->links() }}</div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="text-5xl mb-3">🧾</div>
            <p class="text-gray-800 font-bold">Tidak ada transaksi ditemukan.</p>
            <p class="text-gray-400 text-sm mt-1">Coba ubah kata kunci atau filter tanggal.</p>
        </div>
        @endif
    </section>

    {{-- ══ MODAL TERIMA ══ --}}
    <div x-show="showTerimaModal" x-cloak @click.self="showTerimaModal=false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-4xl p-6 sm:p-7 max-w-sm w-full shadow-2xl fade-up" @click.stop>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-brand-100 rounded-3xl flex items-center justify-center mx-auto mb-4 text-4xl">✅</div>
                <h3 class="font-extrabold text-gray-900 text-lg">Terima Transaksi?</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Transaksi <span class="num font-extrabold text-brand-700" x-text="selectedKode"></span> akan diverifikasi dan diterima. Notifikasi akan dikirim ke user.</p>
            </div>
            <div class="flex gap-3">
                <button @click="showTerimaModal=false" class="btn btn-ghost flex-1">Batal</button>
                <form :action="'/admin/transaksi/' + selectedId + '/terima'" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">Ya, Terima</button>
                </form>
            </div>
        </div>
    </div>

    {{-- ══ MODAL TOLAK ══ --}}
    <div x-show="showTolakModal" x-cloak @click.self="showTolakModal=false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-4xl p-6 sm:p-7 max-w-sm w-full shadow-2xl fade-up" @click.stop>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-3xl flex items-center justify-center mx-auto mb-4 text-4xl">❌</div>
                <h3 class="font-extrabold text-gray-900 text-lg">Tolak Transaksi?</h3>
                <p class="text-gray-500 text-sm mt-2">Isi alasan penolakan. Notifikasi akan dikirim ke user.</p>
            </div>
            <form :action="'/admin/transaksi/' + selectedId + '/tolak'" method="POST" class="space-y-4">
                @csrf
                <textarea name="alasan_tolak" rows="3" required minlength="10"
                    class="fld fld-error" placeholder="Tulis alasan penolakan..."></textarea>
                @error('alasan_tolak')<p class="text-red-500 text-xs font-semibold">{{ $message }}</p>@enderror
                <div class="flex gap-3">
                    <button type="button" @click="showTolakModal=false" class="btn btn-ghost flex-1">Batal</button>
                    <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white flex-1 shadow-lg shadow-red-200/60">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
