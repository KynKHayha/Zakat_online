@extends('layouts.admin')
@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')
@section('page-subtitle', 'Daftar seluruh muzakki terdaftar')

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
@endphp

@section('content')
<div class="space-y-5">

    {{-- ══ HERO + KPI ══ --}}
    <section class="relative overflow-hidden rounded-4xl bg-gradient-to-br from-slate-800 via-slate-900 to-brand-900 hero-pattern p-5 sm:p-7 text-white shadow-glow">
        <div class="relative z-10 flex flex-col gap-5 md:flex-row md:items-end md:justify-between">
            <div>
                <p class="text-brand-300 text-[11px] font-bold uppercase tracking-[0.2em]">Panel Amil</p>
                <h2 class="text-2xl sm:text-3xl font-extrabold mt-1.5">Manajemen User</h2>
                <p class="text-slate-300 text-sm mt-1.5 leading-relaxed">Daftar muzakki terdaftar beserta ringkasan zakatnya.</p>
            </div>
            <div class="flex flex-wrap gap-3">
                <div class="bg-white/10 border border-white/15 rounded-3xl px-5 py-3 min-w-[130px]">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Total Muzakki</p>
                    <p class="num text-2xl font-extrabold mt-1">{{ $users->total() }}</p>
                </div>
                <div class="bg-white/10 border border-white/15 rounded-3xl px-5 py-3 min-w-[130px]">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Tayang di Halaman</p>
                    <p class="num text-2xl font-extrabold mt-1">{{ $users->count() }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <!-- Search -->
        <div class="px-4 sm:px-6 py-4 sm:py-5 border-b border-gray-100">
            <form method="GET" action="{{ route('admin.users.index') }}" class="grid grid-cols-1 sm:grid-cols-12 gap-3 items-end">
                <div class="sm:col-span-5">
                    <label class="fld-label">Cari Muzakki</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama atau email..." class="fld">
                </div>
                <div class="sm:col-span-4">
                    <label class="fld-label">Jenis Zakat</label>
                    <select name="jenis_zakat" class="fld">
                        <option value="semua">Semua Jenis Zakat</option>
                        <option value="fitrah" {{ request('jenis_zakat') == 'fitrah' ? 'selected' : '' }}>Zakat Fitrah</option>
                        <option value="mal" {{ request('jenis_zakat') == 'mal' ? 'selected' : '' }}>Zakat Mal</option>
                        <option value="profesi" {{ request('jenis_zakat') == 'profesi' ? 'selected' : '' }}>Zakat Profesi</option>
                        <option value="perniagaan" {{ request('jenis_zakat') == 'perniagaan' ? 'selected' : '' }}>Zakat Perniagaan</option>
                        <option value="pertanian" {{ request('jenis_zakat') == 'pertanian' ? 'selected' : '' }}>Zakat Pertanian</option>
                        <option value="emas_perak" {{ request('jenis_zakat') == 'emas_perak' ? 'selected' : '' }}>Zakat Emas & Perak</option>
                    </select>
                </div>
                <div class="sm:col-span-3 flex gap-2">
                    <button type="submit" class="btn btn-primary flex-1 !px-4">Cari</button>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-ghost !px-4">Reset</a>
                </div>
            </form>
        </div>

        <!-- Summary -->
        <div class="px-4 sm:px-6 py-3.5 bg-gray-50/70 border-b border-gray-100 flex items-center justify-between gap-3">
            <span class="text-sm text-gray-500">Total: <strong class="num text-gray-900 font-extrabold">{{ $users->total() }}</strong> muzakki</span>
            <a href="{{ route('admin.users.utang') }}" class="btn btn-outline !py-2 !px-3 text-xs">💳 Status Pembayaran</a>
        </div>

        @if($users->count())
        {{-- Desktop --}}
        <div class="hidden md:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50 text-left">
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Muzakki</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-center">Total Transaksi</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Total Zakat</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Kategori Zakat</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider">Bergabung</th>
                        <th class="px-6 py-3.5 text-xs font-bold text-brand-700 uppercase tracking-wider text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($users as $user)
                    <tr class="hover:bg-brand-50/40 transition-colors">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-extrabold flex-shrink-0 shadow-card">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <p class="font-bold text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="num text-xs text-gray-400">ID: {{ $user->id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4">
                            <p class="text-gray-700 break-all">{{ $user->email }}</p>
                            @if($user->phone)
                            <p class="num text-xs text-gray-400 mt-0.5">{{ $user->phone }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="inline-flex items-center justify-center min-w-[2.25rem] h-9 px-2 bg-blue-50 text-blue-700 num font-extrabold text-sm rounded-2xl">
                                {{ $user->transaksis_count ?? $user->transaksis()->count() }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <span class="num font-extrabold text-brand-700">
                                Rp {{ number_format($user->transaksis_sum_total_bayar ?? $user->transaksis()->where('status','sukses')->sum('total_bayar'), 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $categories = $user->transaksis->pluck('donasiZakat.jenis_zakat')->filter()->unique();
                            @endphp
                            @if($categories->count() > 0)
                                <div class="flex flex-wrap gap-1.5">
                                @foreach($categories as $cat)
                                    <span class="chip bg-brand-50 text-brand-700">{{ $jenisIcon($cat) }} {{ $cat }}</span>
                                @endforeach
                                </div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 num text-xs text-gray-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-6 py-4 text-right">
                            <a href="{{ route('admin.users.riwayat', $user->id) }}"
                                class="btn btn-ghost !py-2 !px-3.5 text-xs inline-flex items-center gap-1.5">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                                Riwayat
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile --}}
        <div class="md:hidden divide-y divide-gray-100">
            @foreach($users as $user)
            <div class="p-4">
                <div class="flex items-start gap-3 mb-3">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-br from-brand-400 to-brand-600 flex items-center justify-center text-white font-extrabold flex-shrink-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-bold text-gray-900 truncate">{{ $user->name }}</p>
                        <p class="text-xs text-gray-500 break-all">{{ $user->email }}</p>
                        @if($user->phone)
                        <p class="num text-xs text-gray-400">{{ $user->phone }}</p>
                        @endif
                        <p class="num text-[11px] text-gray-400 mt-0.5">ID: {{ $user->id }} · Bergabung {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-3">
                    <div class="bg-brand-50 rounded-2xl px-3 py-2.5">
                        <p class="text-[10px] font-bold text-brand-600 uppercase tracking-wider">Total Zakat</p>
                        <p class="num text-sm font-extrabold text-brand-700">
                            Rp {{ number_format($user->transaksis_sum_total_bayar ?? $user->transaksis()->where('status','sukses')->sum('total_bayar'), 0, ',', '.') }}
                        </p>
                    </div>
                    <div class="bg-blue-50 rounded-2xl px-3 py-2.5">
                        <p class="text-[10px] font-bold text-blue-600 uppercase tracking-wider">Transaksi</p>
                        <p class="num text-sm font-extrabold text-blue-700">{{ $user->transaksis_count ?? $user->transaksis()->count() }}</p>
                    </div>
                </div>

                @php
                    $categories = $user->transaksis->pluck('donasiZakat.jenis_zakat')->filter()->unique();
                @endphp
                @if($categories->count() > 0)
                    <div class="flex flex-wrap gap-1.5 mb-3">
                    @foreach($categories as $cat)
                        <span class="chip bg-gray-100 text-gray-600">{{ $jenisIcon($cat) }} {{ $cat }}</span>
                    @endforeach
                    </div>
                @else
                    <p class="text-xs text-gray-400 mb-3">Belum ada kategori zakat.</p>
                @endif

                <div class="flex justify-end pt-3 border-t border-gray-100">
                    <a href="{{ route('admin.users.riwayat', $user->id) }}"
                        class="btn btn-ghost !py-2 !px-4 text-xs inline-flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        Riwayat
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        <div class="px-4 sm:px-6 py-4 border-t border-gray-100">
            {{ $users->withQueryString()->links() }}
        </div>
        @else
        <div class="py-16 px-6 text-center">
            <div class="text-6xl mb-3">👥</div>
            <p class="text-gray-800 font-extrabold">Tidak ada user ditemukan</p>
            <p class="text-gray-400 text-sm mt-1">Coba kata kunci atau filter jenis zakat lain.</p>
        </div>
        @endif
    </section>
</div>
@endsection
