@extends('layouts.app')
@section('title', 'Riwayat Zakat')
@section('page-title', 'Riwayat Zakat')
@section('page-subtitle', 'History transaksi pembayaran zakat Anda')

@php
    $jenisIcon = fn ($jenis) => match ($jenis) {
        'fitrah'            => '🌾',
        'mal'               => '💰',
        'profesi'           => '💼',
        'perniagaan'        => '🏪',
        'pertanian'         => '🌱',
        'emas', 'emas_perak' => '🥇',
        default             => '📦',
    };
    $metodeBadge = fn ($metode) => $metode === 'online'
        ? ['bg-blue-50 text-blue-700', '🏦 Online']
        : ['bg-orange-50 text-orange-700', '🏢 Di Tempat'];
@endphp

@section('content')
<div x-data="{ activeTab: '{{ $tab }}' }" class="space-y-6">

    {{-- ══ RINGKASAN ══ --}}
    <section class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 to-brand-800 hero-pattern p-5 text-white shadow-glow">
            <div class="relative z-10">
                <p class="text-brand-200 text-xs font-bold uppercase tracking-wider">Total Sudah Dibayar</p>
                <p class="num text-2xl sm:text-3xl font-extrabold mt-2">Rp {{ number_format($totalSudahBayar, 0, ',', '.') }}</p>
                <p class="text-brand-200 text-xs mt-2">{{ $countSudahBayar }} transaksi sukses</p>
            </div>
        </div>

        <div class="rounded-4xl bg-gradient-to-br from-amber-400 to-amber-600 p-5 text-white shadow-lg shadow-amber-200/60">
            <p class="text-amber-50 text-xs font-bold uppercase tracking-wider">Utang / Belum Bayar</p>
            <p class="num text-2xl sm:text-3xl font-extrabold mt-2">Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
            <p class="text-amber-50 text-xs mt-2">{{ $countUtang }} donasi belum selesai</p>
        </div>

        <div class="rounded-4xl bg-white border border-gray-100 shadow-card p-5">
            <p class="text-gray-500 text-xs font-bold uppercase tracking-wider">Total Zakat Seluruhnya</p>
            <p class="num text-2xl sm:text-3xl font-extrabold text-gray-900 mt-2">
                Rp {{ number_format($totalSudahBayar + $totalUtang, 0, ',', '.') }}
            </p>
            <p class="text-gray-400 text-xs mt-2">Sudah + belum dibayar</p>
        </div>
    </section>

    {{-- ══ TAB + FILTER + TABEL ══ --}}
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">

        {{-- Tab --}}
        <div class="p-4 sm:p-5 border-b border-gray-100 bg-gray-50/60">
            <div class="grid grid-cols-2 gap-2 p-1.5 bg-gray-100 rounded-2xl">
                <button type="button" @click="activeTab = 'sudah_bayar'"
                        class="flex items-center justify-center gap-2 rounded-xl px-3 sm:px-6 py-3 text-sm font-bold transition-all"
                        :class="activeTab === 'sudah_bayar'
                            ? 'bg-white text-brand-700 shadow-card'
                            : 'text-gray-500 hover:text-gray-700'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="truncate">Sudah Dibayar</span>
                    <span class="chip"
                          :class="activeTab === 'sudah_bayar' ? 'bg-brand-100 text-brand-700' : 'bg-gray-200 text-gray-500'"
                          x-text="'{{ $countSudahBayar }}'"></span>
                </button>
                <button type="button" @click="activeTab = 'belum_bayar'"
                        class="flex items-center justify-center gap-2 rounded-xl px-3 sm:px-6 py-3 text-sm font-bold transition-all"
                        :class="activeTab === 'belum_bayar'
                            ? 'bg-white text-amber-700 shadow-card'
                            : 'text-gray-500 hover:text-gray-700'">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="truncate">Belum / Utang</span>
                    @if($countUtang > 0)
                    <span class="chip"
                          :class="activeTab === 'belum_bayar' ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-600'"
                          x-text="'{{ $countUtang }}'"></span>
                    @endif
                </button>
            </div>
        </div>

        {{-- Filter --}}
        <div class="px-4 sm:px-6 py-4 bg-gray-50/60 border-b border-gray-100">
            <form method="GET" action="{{ route('user.riwayat') }}"
                  class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
                <input type="hidden" name="tab" :value="activeTab">

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

                <div class="lg:col-span-3" x-show="activeTab === 'belum_bayar'" x-cloak>
                    <label class="fld-label">Status Utang</label>
                    <select name="status_belum" class="fld">
                        <option value="">Semua Status Utang</option>
                        <option value="draft" {{ request('status_belum')=='draft'?'selected':'' }}>Draft (Belum Bayar)</option>
                        <option value="menunggu" {{ request('status_belum')=='menunggu'?'selected':'' }}>Menunggu Verifikasi</option>
                        <option value="ditolak" {{ request('status_belum')=='ditolak'?'selected':'' }}>Ditolak</option>
                    </select>
                </div>

                <div class="lg:col-span-2">
                    <label class="fld-label">Dari Tanggal</label>
                    <input type="date" name="dari_tanggal" value="{{ request('dari_tanggal') }}" class="fld">
                </div>
                <div class="lg:col-span-2">
                    <label class="fld-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tanggal" value="{{ request('sampai_tanggal') }}" class="fld">
                </div>

                <div class="lg:col-span-2 flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1 !px-4 !py-3">Filter</button>
                    <a href="{{ route('user.riwayat') }}" class="btn btn-ghost !px-4 !py-3">Reset</a>
                </div>

                <div class="sm:col-span-2 lg:col-span-12">
                    <a href="{{ route('user.laporan') }}" class="btn btn-outline w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        Laporan & Unduh PDF
                    </a>
                </div>
            </form>
        </div>

        {{-- ══ TAB 1: SUDAH DIBAYAR ══ --}}
        <div x-show="activeTab === 'sudah_bayar'">
            @if($transaksiSudahBayar->count())
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-brand-50 text-left">
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">No</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Kode Transaksi</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Jenis Zakat</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Jumlah</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Metode</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Tgl Verifikasi</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($transaksiSudahBayar as $idx => $trx)
                                @php [$mClass, $mLabel] = $metodeBadge($trx->metode_bayar); @endphp
                                <tr class="hover:bg-brand-50/40 transition-colors">
                                    <td class="px-5 py-4 text-gray-400 text-xs num">{{ $transaksiSudahBayar->firstItem() + $idx }}</td>
                                    <td class="px-5 py-4">
                                        <span class="num text-xs bg-gray-100 px-2.5 py-1.5 rounded-lg font-bold text-gray-700">{{ $trx->kode_transaksi }}</span>
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
                                    <td class="px-5 py-4"><span class="chip {{ $mClass }}">{{ $mLabel }}</span></td>
                                    <td class="px-5 py-4 num text-gray-500 text-xs">
                                        {{ $trx->diverifikasi_at?->format('d M Y') ?? '-' }}
                                    </td>
                                    <td class="px-5 py-4"><span class="status-badge bg-brand-100 text-brand-700">✓ Lunas</span></td>
                                    <td class="px-5 py-4 text-right">
                                        <a href="{{ route('user.transaksi.show', $trx->id) }}"
                                           class="btn btn-ghost !py-2 !px-3 text-xs">Detail →</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($transaksiSudahBayar as $trx)
                        @php [$mClass, $mLabel] = $metodeBadge($trx->metode_bayar); @endphp
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3 mb-3">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-xl">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $trx->donasiZakat?->label_jenis_zakat ?? '-' }}</p>
                                        <p class="num text-xs text-gray-400">{{ $trx->kode_transaksi }}</p>
                                    </div>
                                </div>
                                <span class="status-badge bg-brand-100 text-brand-700 flex-shrink-0">✓ Lunas</span>
                            </div>
                            <p class="num text-2xl font-extrabold text-brand-700 mb-3">
                                Rp {{ number_format($trx->total_bayar, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-between gap-3">
                                <div class="flex items-center gap-2">
                                    <span class="chip {{ $mClass }}">{{ $mLabel }}</span>
                                    <span class="num text-xs text-gray-400">{{ $trx->diverifikasi_at?->format('d M Y') ?? '-' }}</span>
                                </div>
                                <a href="{{ route('user.transaksi.show', $trx->id) }}"
                                   class="text-sm font-bold text-brand-600 hover:text-brand-700 flex-shrink-0">Detail →</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
                    {{ $transaksiSudahBayar->appends(request()->query())->links() }}
                </div>
            @else
                <div class="py-16 px-6 text-center">
                    <div class="text-5xl mb-3">💰</div>
                    <p class="text-gray-800 font-bold">Belum ada transaksi sukses</p>
                    <p class="text-gray-400 text-sm mt-1">Bayarkan zakat Anda sekarang.</p>
                    <a href="{{ route('user.donasi.create') }}" class="btn btn-primary mt-5">Bayar Zakat Sekarang</a>
                </div>
            @endif
        </div>

        {{-- ══ TAB 2: BELUM BAYAR / UTANG ══ --}}
        <div x-show="activeTab === 'belum_bayar'">
            @if($totalUtang > 0)
                <div class="mx-4 sm:mx-6 mt-4 p-4 bg-amber-50 border border-amber-200 rounded-2xl flex items-center gap-3">
                    <span class="text-2xl flex-shrink-0">⚠️</span>
                    <div>
                        <p class="text-amber-800 font-bold text-sm">
                            Total Utang Zakat: <strong class="num">Rp {{ number_format($totalUtang, 0, ',', '.') }}</strong>
                        </p>
                        <p class="text-amber-600 text-xs mt-0.5">Segera selesaikan pembayaran zakat Anda agar mendapat ketenangan.</p>
                    </div>
                </div>
            @endif

            @if($donasiUtang->count())
                {{-- Desktop --}}
                <div class="hidden md:block overflow-x-auto mt-2">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-amber-50 text-left">
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider">No</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider">Jenis Zakat</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider">Nama</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider text-right">Jumlah</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider">Tgl Input</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider">Status</th>
                                <th class="px-5 py-3.5 text-xs font-bold text-amber-700 uppercase tracking-wider text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($donasiUtang as $idx => $donasi)
                                @php
                                    $statusColor = match($donasi->status) {
                                        'draft'    => 'bg-gray-100 text-gray-600',
                                        'menunggu' => 'bg-amber-100 text-amber-700',
                                        'ditolak'  => 'bg-red-100 text-red-700',
                                        default    => 'bg-gray-100 text-gray-600',
                                    };
                                    $statusLabel = match($donasi->status) {
                                        'draft'    => '📝 Draft (Belum Bayar)',
                                        'menunggu' => '⏳ Menunggu Verifikasi',
                                        'ditolak'  => '❌ Ditolak — Bayar Ulang',
                                        default    => $donasi->status,
                                    };
                                @endphp
                                <tr class="hover:bg-amber-50/40 transition-colors">
                                    <td class="px-5 py-4 text-gray-400 text-xs num">{{ $donasiUtang->firstItem() + $idx }}</td>
                                    <td class="px-5 py-4">
                                        <div class="flex items-center gap-2">
                                            <span class="text-lg">{{ $jenisIcon($donasi->jenis_zakat ?? '') }}</span>
                                            <span class="font-semibold text-gray-700">{{ $donasi->label_jenis_zakat }}</span>
                                        </div>
                                    </td>
                                    <td class="px-5 py-4 text-gray-600">{{ $donasi->nama_muzakki ?? '-' }}</td>
                                    <td class="px-5 py-4 text-right">
                                        <span class="num font-extrabold text-amber-700">Rp {{ number_format($donasi->jumlah_zakat, 0, ',', '.') }}</span>
                                    </td>
                                    <td class="px-5 py-4 num text-gray-500 text-xs">{{ $donasi->created_at->format('d M Y') }}</td>
                                    <td class="px-5 py-4"><span class="status-badge {{ $statusColor }}">{{ $statusLabel }}</span></td>
                                    <td class="px-5 py-4 text-right">
                                        @if($donasi->status === 'draft')
                                            <a href="{{ route('user.donasi.bayar', $donasi->id) }}"
                                               class="btn bg-amber-500 hover:bg-amber-600 text-white !py-2 !px-3 text-xs">💳 Bayar</a>
                                        @elseif($donasi->status === 'ditolak')
                                            <a href="{{ route('user.donasi.create') }}"
                                               class="btn bg-red-500 hover:bg-red-600 text-white !py-2 !px-3 text-xs">🔄 Bayar Ulang</a>
                                        @elseif($donasi->status === 'menunggu' && $donasi->transaksi)
                                            <a href="{{ route('user.transaksi.show', $donasi->transaksi->id) }}"
                                               class="text-amber-700 hover:text-amber-800 text-xs font-bold">Lihat Status →</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Mobile --}}
                <div class="md:hidden divide-y divide-gray-100">
                    @foreach($donasiUtang as $donasi)
                        @php
                            $statusColor = match($donasi->status) {
                                'draft'    => 'bg-gray-100 text-gray-600',
                                'menunggu' => 'bg-amber-100 text-amber-700',
                                'ditolak'  => 'bg-red-100 text-red-700',
                                default    => 'bg-gray-100 text-gray-600',
                            };
                            $statusLabel = match($donasi->status) {
                                'draft'    => '📝 Draft',
                                'menunggu' => '⏳ Menunggu Verifikasi',
                                'ditolak'  => '❌ Ditolak',
                                default    => $donasi->status,
                            };
                        @endphp
                        <div class="p-4">
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="text-xl">{{ $jenisIcon($donasi->jenis_zakat ?? '') }}</span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-bold text-gray-800 truncate">{{ $donasi->label_jenis_zakat }}</p>
                                        <p class="text-xs text-gray-400 truncate">{{ $donasi->nama_muzakki ?? '-' }}</p>
                                    </div>
                                </div>
                                <span class="status-badge {{ $statusColor }} flex-shrink-0">{{ $statusLabel }}</span>
                            </div>
                            <p class="num text-2xl font-extrabold text-amber-700 mb-3">
                                Rp {{ number_format($donasi->jumlah_zakat, 0, ',', '.') }}
                            </p>
                            <div class="flex items-center justify-between gap-3">
                                <span class="num text-xs text-gray-400">Dibuat {{ $donasi->created_at->format('d M Y') }}</span>
                                <div class="flex-shrink-0">
                                    @if($donasi->status === 'draft')
                                        <a href="{{ route('user.donasi.bayar', $donasi->id) }}"
                                           class="btn bg-amber-500 hover:bg-amber-600 text-white !py-2 !px-4 text-xs">💳 Bayar</a>
                                    @elseif($donasi->status === 'ditolak')
                                        <a href="{{ route('user.donasi.create') }}"
                                           class="btn bg-red-500 hover:bg-red-600 text-white !py-2 !px-4 text-xs">🔄 Bayar Ulang</a>
                                    @elseif($donasi->status === 'menunggu' && $donasi->transaksi)
                                        <a href="{{ route('user.transaksi.show', $donasi->transaksi->id) }}"
                                           class="text-amber-700 text-xs font-bold">Lihat Status →</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
                    {{ $donasiUtang->appends(request()->query())->links() }}
                </div>
            @else
                <div class="py-16 px-6 text-center">
                    <div class="text-5xl mb-3">🎉</div>
                    <p class="text-gray-800 font-bold">Tidak ada utang zakat!</p>
                    <p class="text-gray-400 text-sm mt-1">Semua kewajiban zakat Anda sudah terpenuhi. Alhamdulillah!</p>
                </div>
            @endif
        </div>
    </section>
</div>
@endsection
