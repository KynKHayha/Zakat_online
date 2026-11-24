@extends('layouts.app')
@section('title', 'Laporan Zakat Saya')
@section('page-title', 'Laporan Zakat Pribadi')
@section('page-subtitle', 'Rekap riwayat pembayaran zakat Anda — bisa dicetak sebagai PDF')

@php
    $jenisIcon = fn ($jenis) => match ($jenis) {
        'fitrah'             => '🌾',
        'mal'                => '💰',
        'profesi'            => '💼',
        'perniagaan'         => '🏪',
        'pertanian'          => '🌱',
        'emas', 'emas_perak' => '🥇',
        default              => '📦',
    };
@endphp

@section('content')
<div class="space-y-6">

    {{-- ══ RINGKASAN ══ --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 to-brand-800 hero-pattern p-5 text-white shadow-glow">
            <div class="relative z-10">
                <p class="text-brand-200 text-xs font-bold uppercase tracking-wider">Total Zakat Dibayar</p>
                <p class="num text-2xl sm:text-3xl font-extrabold mt-2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                <p class="text-brand-200 text-xs mt-2">{{ $transaksis->count() }} transaksi sukses</p>
            </div>
        </div>

        @if($totalUtang > 0)
            <div class="rounded-4xl bg-gradient-to-br from-amber-400 to-amber-600 p-5 text-white shadow-lg shadow-amber-200/60">
                <p class="text-amber-50 text-xs font-bold uppercase tracking-wider">Total Utang Zakat</p>
                <p class="num text-2xl sm:text-3xl font-extrabold mt-2">Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
                <p class="text-amber-50 text-xs mt-2">{{ $daftarUtang->count() }} donasi belum lunas</p>
            </div>
        @endif

        <div class="rounded-4xl bg-white border border-gray-100 shadow-card p-5">
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Periode Laporan</p>
            <p class="num text-lg font-extrabold text-gray-900 mt-2">
                {{ \Carbon\Carbon::parse($dariTanggal)->format('d M Y') }}
            </p>
            <p class="num text-xs text-gray-400 mt-0.5">
                s/d {{ \Carbon\Carbon::parse($sampaiTanggal)->format('d M Y') }}
            </p>
        </div>
    </section>

    {{-- ══ FILTER + UNDUH ══ --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-6">
        <form method="GET" action="{{ route('user.laporan') }}"
              class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-4 items-end">
            <div class="lg:col-span-3">
                <label class="fld-label">Dari Tanggal</label>
                <input type="date" name="dari_tanggal" value="{{ $dariTanggal }}" class="fld">
            </div>
            <div class="lg:col-span-3">
                <label class="fld-label">Sampai Tanggal</label>
                <input type="date" name="sampai_tanggal" value="{{ $sampaiTanggal }}" class="fld">
            </div>
            <div class="lg:col-span-3">
                <label class="fld-label">Jenis Zakat</label>
                <select name="jenis_zakat" class="fld">
                    <option value="">Semua Jenis</option>
                    <option value="fitrah" {{ request('jenis_zakat')=='fitrah'?'selected':'' }}>🌾 Zakat Fitrah</option>
                    <option value="mal" {{ request('jenis_zakat')=='mal'?'selected':'' }}>💰 Zakat Mal</option>
                    <option value="profesi" {{ request('jenis_zakat')=='profesi'?'selected':'' }}>💼 Zakat Profesi</option>
                    <option value="perniagaan" {{ request('jenis_zakat')=='perniagaan'?'selected':'' }}>🏪 Zakat Perniagaan</option>
                    <option value="pertanian" {{ request('jenis_zakat')=='pertanian'?'selected':'' }}>🌱 Zakat Pertanian</option>
                    <option value="emas_perak" {{ request('jenis_zakat')=='emas_perak'?'selected':'' }}>🥇 Zakat Emas & Perak</option>
                </select>
            </div>

            <div class="lg:col-span-3 flex gap-2">
                <button type="submit" class="btn btn-primary flex-1 !px-4">🔍 Filter</button>
                <a href="{{ route('user.laporan') }}" class="btn btn-ghost !px-4">Reset</a>
            </div>

            <div class="sm:col-span-2 lg:col-span-12 flex flex-col sm:flex-row gap-3 pt-1">
                <a href="{{ route('user.laporan.pdf', request()->query()) }}"
                   class="btn bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-200/60 w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Download PDF
                </a>
                <button type="button" onclick="window.print()" class="btn bg-gray-700 hover:bg-gray-800 text-white w-full sm:w-auto">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                    </svg>
                    Cetak Laporan
                </button>
            </div>
        </form>
    </section>

    {{-- ══ SUBTOTAL PER JENIS ══ --}}
    @if($subtotalPerJenis->count() > 0)
        <section>
            <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-3">Subtotal per Jenis Zakat</h3>
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                @foreach($subtotalPerJenis as $jenis => $sub)
                    <div class="card-hover bg-white rounded-3xl p-4 sm:p-5 shadow-card border border-gray-100">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="text-xl">{{ $jenisIcon($jenis) }}</span>
                            <p class="text-xs text-gray-500 font-bold">{{ $sub['label'] }}</p>
                        </div>
                        <p class="num text-lg sm:text-xl font-extrabold text-brand-700">Rp {{ number_format($sub['total'], 0, ',', '.') }}</p>
                        <p class="num text-xs text-gray-400 mt-1">{{ $sub['jumlah'] }} transaksi</p>
                    </div>
                @endforeach
            </div>
        </section>
    @endif

    {{-- ══ TABEL LAPORAN ══ --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-gray-900">Riwayat Pembayaran Sukses</h3>
            <span class="chip bg-gray-100 text-gray-500 num">{{ $transaksis->count() }} transaksi</span>
        </div>

        @if($transaksis->count() > 0)
            {{-- Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-50 text-left">
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Kode Transaksi</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Jenis Zakat</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Jumlah</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Metode</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Tgl Verifikasi</th>
                            <th class="px-5 py-3.5 text-xs font-bold text-gray-500 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($transaksis as $idx => $trx)
                            <tr class="hover:bg-brand-50/40 transition-colors">
                                <td class="px-5 py-4 text-gray-400 text-xs num">{{ $idx + 1 }}</td>
                                <td class="px-5 py-4">
                                    <span class="num text-xs bg-gray-100 px-2.5 py-1.5 rounded-lg font-bold">{{ $trx->kode_transaksi }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                                        <span class="font-semibold text-gray-700">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-4 text-right">
                                    <span class="num font-extrabold text-brand-700">Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-4">
                                    @if($trx->metode_bayar === 'online')
                                        <span class="chip bg-blue-50 text-blue-700">Online</span>
                                    @else
                                        <span class="chip bg-orange-50 text-orange-700">Di Tempat</span>
                                    @endif
                                </td>
                                <td class="px-5 py-4 num text-gray-500 text-xs">{{ $trx->diverifikasi_at?->format('d M Y') ?? '-' }}</td>
                                <td class="px-5 py-4"><span class="status-badge bg-brand-100 text-brand-700">✓ Sukses</span></td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-brand-50">
                            <td colspan="3" class="px-5 py-4 text-gray-700 font-extrabold">TOTAL KESELURUHAN</td>
                            <td class="px-5 py-4 text-right">
                                <span class="num font-black text-base text-brand-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</span>
                            </td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="md:hidden divide-y divide-gray-100">
                @foreach($transaksis as $idx => $trx)
                    <div class="p-4">
                        <div class="flex items-start justify-between gap-3 mb-2">
                            <div class="flex items-center gap-2 min-w-0">
                                <span class="text-xl">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                                <div class="min-w-0">
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</p>
                                    <p class="num text-xs text-gray-400">{{ $trx->kode_transaksi }}</p>
                                </div>
                            </div>
                            <span class="status-badge bg-brand-100 text-brand-700 flex-shrink-0">✓ Sukses</span>
                        </div>
                        <p class="num text-xl font-extrabold text-brand-700">{{ number_format($trx->total_bayar, 0, ',', '.') }}</p>
                        <div class="flex items-center justify-between gap-3 mt-2">
                            <div class="flex items-center gap-2">
                                @if($trx->metode_bayar === 'online')
                                    <span class="chip bg-blue-50 text-blue-700">Online</span>
                                @else
                                    <span class="chip bg-orange-50 text-orange-700">Di Tempat</span>
                                @endif
                                <span class="num text-xs text-gray-400">{{ $trx->diverifikasi_at?->format('d M Y') ?? '-' }}</span>
                            </div>
                            <span class="num text-xs text-gray-300">{{ $idx + 1 }}</span>
                        </div>
                    </div>
                @endforeach
                <div class="bg-brand-50 px-4 py-4 flex items-center justify-between">
                    <p class="text-sm font-extrabold text-gray-700">TOTAL KESELURUHAN</p>
                    <p class="num text-lg font-black text-brand-700">Rp {{ number_format($grandTotal, 0, ',', '.') }}</p>
                </div>
            </div>
        @else
            <div class="py-16 px-6 text-center text-gray-400">
                <div class="text-5xl mb-3">📋</div>
                <p class="font-bold text-gray-600">Belum ada laporan untuk periode ini.</p>
                <p class="text-sm mt-1">Coba ubah filter tanggal di atas.</p>
            </div>
        @endif
    </section>

    {{-- ══ DAFTAR UTANG ══ --}}
    @if($daftarUtang->count() > 0)
        <section class="bg-amber-50 border border-amber-200 rounded-4xl overflow-hidden">
            <div class="px-5 sm:px-6 py-4 border-b border-amber-200">
                <h3 class="font-extrabold text-amber-800">⚠️ Zakat Belum Dilunasi ({{ $daftarUtang->count() }} item)</h3>
                <p class="text-xs text-amber-600 mt-1">Data ini tidak termasuk dalam laporan sukses di atas</p>
            </div>

            {{-- Desktop --}}
            <div class="hidden md:block overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-amber-100 text-left">
                            <th class="px-5 py-3 text-xs font-bold text-amber-700 uppercase tracking-wider">Jenis</th>
                            <th class="px-5 py-3 text-xs font-bold text-amber-700 uppercase tracking-wider text-right">Jumlah</th>
                            <th class="px-5 py-3 text-xs font-bold text-amber-700 uppercase tracking-wider">Status</th>
                            <th class="px-5 py-3 text-xs font-bold text-amber-700 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-amber-100">
                        @foreach($daftarUtang as $d)
                            <tr>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-2">
                                        <span class="text-lg">{{ $jenisIcon($d->jenis_zakat ?? '') }}</span>
                                        <span class="font-semibold text-gray-700">{{ $d->label_jenis_zakat }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    <span class="num font-extrabold text-amber-700">Rp {{ number_format($d->jumlah_zakat, 0, ',', '.') }}</span>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="status-badge {{ $d->status === 'draft' ? 'bg-gray-100 text-gray-600' : ($d->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                        {{ ucfirst($d->status) }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-right">
                                    @if($d->status === 'draft')
                                        <a href="{{ route('user.donasi.bayar', $d->id) }}"
                                           class="btn bg-amber-500 hover:bg-amber-600 text-white !py-2 !px-3 text-xs">Bayar Sekarang →</a>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-amber-100">
                            <td class="px-5 py-3.5 font-extrabold text-amber-800">Total Utang</td>
                            <td class="px-5 py-3.5 text-right">
                                <span class="num font-black text-amber-800">Rp {{ number_format($totalUtang, 0, ',', '.') }}</span>
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            {{-- Mobile --}}
            <div class="md:hidden divide-y divide-amber-100">
                @foreach($daftarUtang as $d)
                    <div class="p-4 flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="text-xl">{{ $jenisIcon($d->jenis_zakat ?? '') }}</span>
                            <div class="min-w-0">
                                <p class="text-sm font-bold text-gray-700 truncate">{{ $d->label_jenis_zakat }}</p>
                                <p class="num text-xs text-amber-700 font-bold">Rp {{ number_format($d->jumlah_zakat, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        <div class="flex flex-col items-end gap-1.5 flex-shrink-0">
                            <span class="status-badge {{ $d->status === 'draft' ? 'bg-gray-100 text-gray-600' : ($d->status === 'menunggu' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700') }}">
                                {{ ucfirst($d->status) }}
                            </span>
                            @if($d->status === 'draft')
                                <a href="{{ route('user.donasi.bayar', $d->id) }}" class="text-xs font-bold text-amber-700">Bayar →</a>
                            @endif
                        </div>
                    </div>
                @endforeach
                <div class="bg-amber-100 px-4 py-3.5 flex items-center justify-between">
                    <p class="text-sm font-extrabold text-amber-800">Total Utang</p>
                    <p class="num text-base font-black text-amber-800">Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
                </div>
            </div>
        </section>
    @endif
</div>
@endsection
