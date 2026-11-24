@extends('layouts.admin')
@section('title', 'Daftar Utang Zakat')
@section('page-title', 'Status Pembayaran Zakat')
@section('page-subtitle', 'Pantau muzakki yang sudah melunasi zakat dan yang masih memiliki utang')

@section('content')
<div class="space-y-5">

    {{-- ══ HERO + KPI ══ --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br {{ $totalUtangKeseluruhan > 0 ? 'from-red-600 via-red-700 to-rose-900' : 'from-brand-600 via-brand-700 to-brand-900' }} hero-pattern p-5 sm:p-7 text-white shadow-glow">
        <div class="relative z-10 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-white/70 text-[11px] font-bold uppercase tracking-[0.2em]">Panel Amil</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1.5">Status Pembayaran Zakat</h2>
                <p class="text-white/80 text-sm mt-1.5 leading-relaxed">Kirim pengingat untuk muzakki yang belum menyelesaikan pembayarannya.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="bg-white/10 border border-white/15 rounded-3xl px-5 py-3 min-w-[140px]">
                    <p class="text-white/70 text-[11px] font-bold uppercase tracking-wider">Total User Menunggak</p>
                    <p class="num text-2xl font-extrabold mt-1">{{ $totalUtangKeseluruhan }}</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-3xl px-5 py-3 min-w-[140px]">
                    <p class="text-white/70 text-[11px] font-bold uppercase tracking-wider">Tayang di Halaman</p>
                    <p class="num text-2xl font-extrabold mt-1">{{ $users->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <!-- Toolbar -->
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100 flex flex-col lg:flex-row lg:items-end lg:justify-between gap-4 bg-gray-50/70">
            <div class="flex items-center gap-3">
                <div class="w-11 h-11 {{ $totalUtangKeseluruhan > 0 ? 'bg-red-100 text-red-600' : 'bg-brand-100 text-brand-600' }} rounded-2xl flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-gray-900 text-sm sm:text-base">Daftar Muzakki &amp; Statusnya</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Total menunggak: <strong class="num text-red-600">{{ $totalUtangKeseluruhan }}</strong> muzakki</p>
                </div>
            </div>

            <div class="flex flex-col sm:flex-row sm:items-end gap-3">
                <form method="GET" action="{{ route('admin.users.utang') }}" class="min-w-[190px]">
                    <label class="fld-label">Filter Status</label>
                    <select name="filter" class="fld" onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="utang" {{ request('filter') == 'utang' ? 'selected' : '' }}>Masih Utang</option>
                        <option value="lunas" {{ request('filter') == 'lunas' ? 'selected' : '' }}>Sudah Lunas</option>
                    </select>
                </form>

                @if($totalUtangKeseluruhan > 0)
                <form action="{{ route('admin.notifikasi.kirim-pengingat') }}" method="POST">
                    @csrf
                    <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white shadow-lg shadow-red-200/70 w-full sm:w-auto">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.036 2.036 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V4a2 2 0 10-4 0v1.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        Kirim Pengingat (Tunggakan)
                    </button>
                </form>
                @endif
            </div>
        </div>

        @if($users->count())
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50 text-left">
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Muzakki</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Status Pembayaran</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Total Nominal</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    @php
                        $totalUtang = $user->totalUtang();
                        $isUtang = $totalUtang > 0;
                        $totalLunas = $user->totalZakatDibayar();
                    @endphp
                    <tr class="hover:bg-brand-50/40 transition-colors">
                        <td class="px-6 py-4 align-top">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full {{ $isUtang ? 'bg-gradient-to-br from-red-400 to-red-600' : 'bg-gradient-to-br from-brand-400 to-brand-600' }} flex items-center justify-center text-white font-extrabold flex-shrink-0 shadow-card">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-400">{{ $user->email }}</p>
                                    @if($user->phone)<p class="num text-xs text-gray-400">{{ $user->phone }}</p>@endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 align-top">
                            @if($isUtang)
                                <span class="status-badge bg-red-50 text-red-700 border border-red-200 mb-2">⚠️ Masih Utang</span>
                            @else
                                <span class="status-badge bg-brand-50 text-brand-700 border border-brand-200 mb-2">✅ Sudah Lunas</span>
                            @endif
                            <ul class="space-y-1.5">
                                @foreach($user->donasiZakats as $donasi)
                                <li class="flex items-center justify-between gap-3 text-xs px-3 py-2 rounded-xl border {{ $donasi->status === 'sukses' ? 'bg-brand-50/70 border-brand-100 text-brand-800' : 'bg-red-50/70 border-red-100 text-red-800' }}">
                                    <span class="font-bold">{{ $donasi->label_jenis_zakat }}</span>
                                    <span class="num font-extrabold">Rp {{ number_format($donasi->jumlah_zakat,0,',','.') }}</span>
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            @if($isUtang)
                            <div class="num font-extrabold text-red-600 text-base">
                                Utang: Rp {{ number_format($totalUtang, 0, ',', '.') }}
                            </div>
                            @endif
                            @if($totalLunas > 0)
                            <div class="num font-extrabold text-brand-600 text-base mt-1.5">
                                Lunas: Rp {{ number_format($totalLunas, 0, ',', '.') }}
                            </div>
                            @endif
                        </td>
                        <td class="px-6 py-4 align-top text-right">
                            <a href="{{ route('admin.users.riwayat', $user->id) }}"
                                class="btn btn-ghost !py-2 !px-3.5 text-xs">Lihat Profil</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($users as $user)
            @php
                $totalUtang = $user->totalUtang();
                $isUtang = $totalUtang > 0;
                $totalLunas = $user->totalZakatDibayar();
            @endphp
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-11 h-11 rounded-full {{ $isUtang ? 'bg-gradient-to-br from-red-400 to-red-600' : 'bg-gradient-to-br from-brand-400 to-brand-600' }} flex items-center justify-center text-white font-extrabold flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-400 break-all">{{ $user->email }}</p>
                        @if($user->phone)<p class="num text-xs text-gray-400">{{ $user->phone }}</p>@endif
                    </div>
                    @if($isUtang)
                        <span class="status-badge bg-red-50 text-red-700 border border-red-200 flex-shrink-0">⚠️ Masih Utang</span>
                    @else
                        <span class="status-badge bg-brand-50 text-brand-700 border border-brand-200 flex-shrink-0">✅ Sudah Lunas</span>
                    @endif
                </div>

                @if($isUtang || $totalLunas > 0)
                <div class="grid grid-cols-2 gap-2 mb-3">
                    @if($isUtang)
                    <div class="bg-red-50 border border-red-100 rounded-2xl px-3 py-2.5">
                        <p class="text-[10px] font-bold text-red-500 uppercase tracking-wider">Utang</p>
                        <p class="num text-sm font-extrabold text-red-600">Rp {{ number_format($totalUtang, 0, ',', '.') }}</p>
                    </div>
                    @endif
                    @if($totalLunas > 0)
                    <div class="bg-brand-50 border border-brand-100 rounded-2xl px-3 py-2.5">
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider">Lunas</p>
                        <p class="num text-sm font-extrabold text-brand-700">Rp {{ number_format($totalLunas, 0, ',', '.') }}</p>
                    </div>
                    @endif
                </div>
                @endif

                <ul class="space-y-1.5 mb-3">
                    @foreach($user->donasiZakats as $donasi)
                    <li class="flex items-center justify-between gap-3 text-xs px-3 py-2 rounded-xl border {{ $donasi->status === 'sukses' ? 'bg-brand-50/70 border-brand-100 text-brand-800' : 'bg-red-50/70 border-red-100 text-red-800' }}">
                        <span class="font-bold truncate">{{ $donasi->label_jenis_zakat }}</span>
                        <span class="num font-extrabold flex-shrink-0">Rp {{ number_format($donasi->jumlah_zakat,0,',','.') }}</span>
                    </li>
                    @endforeach
                </ul>

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.users.riwayat', $user->id) }}"
                        class="btn btn-ghost !py-2 !px-4 text-xs">Lihat Profil</a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
            {{ $users->links() }}
        </div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="text-6xl mb-3">📭</div>
            <p class="text-gray-800 font-extrabold text-lg">Tidak ada data pembayaran.</p>
            <p class="text-gray-400 mt-1">Belum ada muzakki yang masuk ke dalam kategori ini.</p>
        </div>
        @endif
    </section>
</div>
@endsection
