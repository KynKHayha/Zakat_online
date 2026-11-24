@extends('layouts.admin')
@section('title', 'Laporan Keuangan')
@section('page-title', 'Laporan Keuangan')
@section('page-subtitle', 'Rekap penerimaan zakat berdasarkan periode')

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

    // Data untuk rekap & grafik per jenis zakat
    $palette = [
        'fitrah'     => '#059669',
        'mal'        => '#0ea5e9',
        'profesi'    => '#8b5cf6',
        'perniagaan' => '#f59e0b',
        'pertanian'  => '#10b981',
        'emas_perak' => '#6366f1',
        'unknown'    => '#94a3b8',
    ];
    $rekapJenis   = collect($subtotalPerJenis)->filter(fn ($row) => ($row['total'] ?? 0) > 0);
    $maxJenis     = $rekapJenis->max('total') ?: 0;
    $chartJenis   = [
        'labels' => $rekapJenis->pluck('jenis')->values()->all(),
        'values' => $rekapJenis->pluck('total')->values()->all(),
        'colors' => $rekapJenis->keys()->map(fn ($k) => $palette[$k] ?? '#10b981')->values()->all(),
    ];
@endphp

@section('content')
<div class="space-y-5">

    {{-- ══ HERO + GRAND TOTAL ══ --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 via-brand-700 to-brand-900 hero-pattern p-5 sm:p-7 text-white shadow-glow">
        <div class="relative z-10 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-brand-200 text-[11px] font-bold uppercase tracking-[0.2em]">Panel Amil</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1.5">Laporan Keuangan</h2>
                <p class="text-brand-100/80 text-sm mt-1.5 leading-relaxed">
                    Rekap transaksi sukses periode
                    <strong class="num">{{ $dariTanggal ? \Carbon\Carbon::parse($dariTanggal)->format('d M Y') : 'Awal' }}</strong>
                    s/d
                    <strong class="num">{{ $sampaiTanggal ? \Carbon\Carbon::parse($sampaiTanggal)->format('d M Y') : 'Sekarang' }}</strong>
                </p>
            </div>
            <div class="bg-white/10 border border-white/15 rounded-3xl px-5 py-4">
                <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">💎 Grand Total</p>
                <p class="num text-2xl sm:text-3xl font-extrabold mt-1">Rp {{ number_format($grandTotal,0,',','.') }}</p>
                <p class="text-brand-200/80 text-xs mt-1"><span class="num">{{ $transaksis->count() }}</span> transaksi sukses</p>
            </div>
        </div>
    </section>

    <!-- Filter -->
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 p-4 sm:p-6">
        <h3 class="font-extrabold text-gray-900 text-sm sm:text-base mb-4 flex items-center gap-2.5">
            <span class="w-8 h-8 bg-brand-100 rounded-xl flex items-center justify-center">🔍</span>
            Filter Periode &amp; Kategori
        </h3>
        <form method="GET" action="{{ route('admin.laporan.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-12 gap-3 items-end">
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
                    <option value="fitrah" {{ request('jenis_zakat')==='fitrah'?'selected':'' }}>Fitrah</option>
                    <option value="mal" {{ request('jenis_zakat')==='mal'?'selected':'' }}>Mal</option>
                    <option value="profesi" {{ request('jenis_zakat')==='profesi'?'selected':'' }}>Profesi</option>
                    <option value="perniagaan" {{ request('jenis_zakat')==='perniagaan'?'selected':'' }}>Perniagaan</option>
                    <option value="pertanian" {{ request('jenis_zakat')==='pertanian'?'selected':'' }}>Pertanian</option>
                    <option value="emas_perak" {{ request('jenis_zakat')==='emas_perak'?'selected':'' }}>Emas & Perak</option>
                </select>
            </div>
            <div class="lg:col-span-3">
                <label class="fld-label">Metode</label>
                <select name="metode_bayar" class="fld">
                    <option value="">Semua Metode</option>
                    <option value="online" {{ request('metode_bayar')==='online'?'selected':'' }}>Online</option>
                    <option value="ditempat" {{ request('metode_bayar')==='ditempat'?'selected':'' }}>Di Tempat</option>
                </select>
            </div>
            <div class="sm:col-span-2 lg:col-span-12 flex flex-col sm:flex-row gap-2">
                <button type="submit" class="btn btn-primary flex-1 sm:flex-none !px-6">Tampilkan</button>
                <a href="{{ route('admin.laporan.index') }}" class="btn btn-ghost flex-1 sm:flex-none !px-6">Reset</a>
                <a href="{{ route('admin.laporan.pdf') }}?{{ http_build_query(request()->all()) }}"
                    class="btn bg-red-600 hover:bg-red-700 text-white flex-1 sm:flex-none !px-6 shadow-lg shadow-red-200/60" target="_blank">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    Unduh PDF
                </a>
            </div>
        </form>
    </section>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 card-hover">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Online</p>
                    <p class="num text-xl font-extrabold text-blue-600 mt-0.5">Rp {{ number_format($totalOnline,0,',','.') }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-3">
                {{ $grandTotal > 0 ? round(($totalOnline / $grandTotal) * 100) : 0 }}% dari grand total
            </p>
        </div>

        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 card-hover">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-orange-50 text-orange-600 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-xs text-gray-500 font-bold uppercase tracking-wider">Total Di Tempat</p>
                    <p class="num text-xl font-extrabold text-orange-600 mt-0.5">Rp {{ number_format($totalDitempat,0,',','.') }}</p>
                </div>
            </div>
            <p class="text-[11px] text-gray-400 mt-3">
                {{ $grandTotal > 0 ? round(($totalDitempat / $grandTotal) * 100) : 0 }}% dari grand total
            </p>
        </div>

        <div class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-brand-600 to-brand-800 hero-pattern p-5 shadow-glow">
            <div class="relative z-10 flex items-center gap-4">
                <div class="w-12 h-12 bg-white/20 rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div class="min-w-0">
                    <p class="text-brand-200 text-xs font-bold uppercase tracking-wider">Grand Total</p>
                    <p class="num text-xl font-extrabold text-white mt-0.5">Rp {{ number_format($grandTotal,0,',','.') }}</p>
                </div>
            </div>
            <p class="relative z-10 text-brand-200/80 text-[11px] mt-3"><span class="num">{{ $transaksis->count() }}</span> transaksi pada periode ini</p>
        </div>
    </div>

    <!-- Period Info -->
    @if($dariTanggal || $sampaiTanggal)
    <div class="bg-blue-50 border border-blue-200 rounded-3xl px-4 sm:px-5 py-3.5 flex items-center gap-2.5 text-sm text-blue-700">
        <svg class="w-4 h-4 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
        Periode: <strong class="num">{{ $dariTanggal ? \Carbon\Carbon::parse($dariTanggal)->format('d M Y') : 'Awal' }}</strong>
        s/d <strong class="num">{{ $sampaiTanggal ? \Carbon\Carbon::parse($sampaiTanggal)->format('d M Y') : 'Sekarang' }}</strong>
    </div>
    @endif

    <!-- Rekap per Jenis Zakat -->
    @if($rekapJenis->count())
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-5">
        <section class="lg:col-span-3 bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-6">
            <h3 class="font-extrabold text-gray-900 text-sm sm:text-base mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-brand-100 rounded-xl flex items-center justify-center">📊</span>
                Rekap per Jenis Zakat
            </h3>
            <div class="space-y-4">
                @foreach($rekapJenis as $key => $row)
                @php
                    $persen = $maxJenis > 0 ? max(6, round(($row['total'] / $maxJenis) * 100)) : 0;
                    $warna  = $palette[$key] ?? '#10b981';
                @endphp
                <div>
                    <div class="flex items-center justify-between gap-3 mb-1.5">
                        <span class="text-sm font-bold text-gray-700 flex items-center gap-2 min-w-0">
                            <span>{{ $jenisIcon($key) }}</span>
                            <span class="truncate">{{ $row['jenis'] }}</span>
                            <span class="chip bg-gray-100 text-gray-500 flex-shrink-0"><span class="num">{{ $row['jumlah'] }}</span> trx</span>
                        </span>
                        <span class="num text-sm font-extrabold text-gray-900 flex-shrink-0">Rp {{ number_format($row['total'],0,',','.') }}</span>
                    </div>
                    <div class="h-2.5 bg-gray-100 rounded-full overflow-hidden">
                        <div class="h-full rounded-full" style="width: {{ $persen }}%; background: {{ $warna }};"></div>
                    </div>
                </div>
                @endforeach
            </div>
        </section>

        <section class="lg:col-span-2 bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-6">
            <h3 class="font-extrabold text-gray-900 text-sm sm:text-base mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-purple-100 rounded-xl flex items-center justify-center">🍩</span>
                Porsi Nominal
            </h3>
            <div class="relative h-56 flex items-center justify-center">
                <canvas id="laporanJenisChart"></canvas>
            </div>
        </section>
    </div>
    @endif

    <!-- Table -->
    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-gray-900 text-sm sm:text-base">Detail Transaksi</h3>
            <span class="chip bg-brand-50 text-brand-700"><span class="num">{{ $transaksis->count() }}</span> transaksi</span>
        </div>

        @if($transaksis->count())
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50 text-left">
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">No</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Tanggal</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Kode</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Muzakki</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Jenis Zakat</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Metode</th>
                        <th class="px-5 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($transaksis as $i => $trx)
                    <tr class="hover:bg-brand-50/40 transition-colors">
                        <td class="px-5 py-3.5 text-gray-400 text-xs num">{{ $i+1 }}</td>
                        <td class="px-5 py-3.5 num text-xs text-gray-500">{{ $trx->tanggal_transaksi?->format('d M Y') }}</td>
                        <td class="px-5 py-3.5">
                            <span class="num text-xs font-bold text-gray-600 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $trx->kode_transaksi }}</span>
                        </td>
                        <td class="px-5 py-3.5 font-bold text-gray-900">{{ $trx->user?->name }}</td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <span class="text-base">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                                <span class="text-gray-600 text-xs font-semibold">{{ $trx->donasiZakat?->label_jenis_zakat }}</span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5">
                            @if($trx->metode_bayar==='online')
                            <span class="chip bg-blue-50 text-blue-700">🌐 Online</span>
                            @else
                            <span class="chip bg-orange-50 text-orange-700">📍 Di Tempat</span>
                            @endif
                        </td>
                        <td class="px-5 py-3.5 text-right">
                            <span class="num font-extrabold text-gray-900">Rp {{ number_format($trx->total_bayar,0,',','.') }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-brand-50 border-t-2 border-brand-200">
                        <td colspan="6" class="px-5 py-4 text-sm font-extrabold text-brand-800 uppercase tracking-wide">Grand Total</td>
                        <td class="px-5 py-4 text-right font-extrabold text-brand-700 text-base num">Rp {{ number_format($grandTotal,0,',','.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($transaksis as $i => $trx)
            <div class="p-4">
                <div class="flex items-start justify-between gap-3 mb-2">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="chip bg-gray-100 text-gray-500 flex-shrink-0"><span class="num">{{ $i+1 }}</span></span>
                        <div class="min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate">{{ $trx->user?->name }}</p>
                            <p class="num text-xs text-gray-400">{{ $trx->kode_transaksi }} · {{ $trx->tanggal_transaksi?->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-2 mb-3">
                    <span class="text-lg">{{ $jenisIcon($trx->donasiZakat?->jenis_zakat ?? '') }}</span>
                    <span class="text-xs font-bold text-gray-600">{{ $trx->donasiZakat?->label_jenis_zakat }}</span>
                </div>
                <div class="flex items-center justify-between gap-3">
                    @if($trx->metode_bayar==='online')
                    <span class="chip bg-blue-50 text-blue-700">🌐 Online</span>
                    @else
                    <span class="chip bg-orange-50 text-orange-700">📍 Di Tempat</span>
                    @endif
                    <span class="num font-extrabold text-brand-700">Rp {{ number_format($trx->total_bayar,0,',','.') }}</span>
                </div>
            </div>
            @endforeach
            <div class="p-4 bg-brand-50 flex items-center justify-between gap-3">
                <span class="text-sm font-extrabold text-brand-800 uppercase tracking-wide">Grand Total</span>
                <span class="num font-extrabold text-brand-700 text-base">Rp {{ number_format($grandTotal,0,',','.') }}</span>
            </div>
        </div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="text-5xl mb-3">📊</div>
            <p class="text-gray-800 font-extrabold">Tidak ada data</p>
            <p class="text-gray-400 text-sm mt-1">Tidak ada data untuk periode yang dipilih.</p>
        </div>
        @endif
    </section>
</div>
@endsection

@if($rekapJenis->count())
@push('scripts')
<script>
    (function () {
        const ctx = document.getElementById('laporanJenisChart');
        if (!ctx || typeof Chart === 'undefined') return;
        const d = @json($chartJenis);
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: d.labels,
                datasets: [{
                    data: d.values,
                    backgroundColor: d.colors,
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '62%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 12, font: { size: 11, weight: '700' }, color: '#4b5563', padding: 14 }
                    },
                    tooltip: {
                        callbacks: {
                            label: function (item) {
                                return ' Rp ' + item.parsed.toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });
        // Tailwind CDN memberi tinggi wrapper setelah Chart.js mengukur, jadi ukur ulang saat load.
        window.addEventListener('load', () => chart.resize());
    })();
</script>
@endpush
@endif
