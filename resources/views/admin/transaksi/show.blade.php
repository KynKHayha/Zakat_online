@extends('layouts.admin')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('page-subtitle', 'Informasi lengkap & verifikasi transaksi')

@section('content')
<div x-data="{ showTerimaModal: false, showTolakModal: false, showBuktiModal: false }" class="space-y-5">

    <!-- Kembali -->
    <a href="{{ route('admin.transaksi.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-gray-500 hover:text-brand-600 transition-colors group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
        Kembali ke Daftar
    </a>

    <!-- Status Header -->
    @php
        $banners=['menunggu'=>['bg'=>'bg-gradient-to-br from-amber-500 to-amber-600','label'=>'Menunggu Verifikasi','icon'=>'⏳','sub'=>'Silakan periksa bukti transfer sebelum menerima pembayaran ini.'],'sukses'=>['bg'=>'bg-gradient-to-br from-brand-600 to-brand-800','label'=>'Transaksi Sukses','icon'=>'✅','sub'=>'Zakat sudah diterima dan dikonfirmasi oleh amil.'],'ditolak'=>['bg'=>'bg-gradient-to-br from-red-600 to-red-700','label'=>'Transaksi Ditolak','icon'=>'❌','sub'=>'Pembayaran ini telah ditolak oleh amil.'],'dibatalkan'=>['bg'=>'bg-gradient-to-br from-gray-600 to-gray-800','label'=>'Dibatalkan','icon'=>'🚫','sub'=>'Transaksi ini sudah dibatalkan.']];
        $b=$banners[$transaksi->status]??$banners['menunggu'];
    @endphp
    <div class="{{ $b['bg'] }} hero-pattern relative overflow-hidden rounded-4xl p-5 sm:p-7 text-white shadow-glow">
        <div class="relative z-10 flex flex-col gap-5 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 sm:w-16 sm:h-16 rounded-3xl bg-white/20 flex items-center justify-center text-3xl sm:text-4xl flex-shrink-0">{{ $b['icon'] }}</div>
                <div class="min-w-0">
                    <h2 class="text-xl sm:text-2xl font-extrabold">{{ $b['label'] }}</h2>
                    <p class="text-white/80 text-sm mt-1 leading-relaxed">{{ $b['sub'] }}</p>
                    <p class="num text-white/70 text-xs mt-2 font-bold">{{ $transaksi->kode_transaksi }}</p>
                </div>
            </div>
            @if($transaksi->status === 'menunggu')
            <div class="flex flex-col sm:flex-row gap-3 flex-shrink-0">
                <button @click="showTerimaModal=true"
                    class="btn bg-white text-brand-700 hover:bg-brand-50 shadow-lg">✅ Terima</button>
                <button @click="showTolakModal=true"
                    class="btn bg-red-600 text-white hover:bg-red-700 border border-white/25 shadow-lg">❌ Tolak</button>
            </div>
            @endif
        </div>
    </div>

    @if($transaksi->status==='ditolak' && $transaksi->alasan_tolak)
    <div class="bg-red-50 border border-red-200 rounded-3xl p-4 sm:p-5 flex gap-3">
        <svg class="w-5 h-5 text-red-500 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg>
        <div>
            <p class="text-sm font-extrabold text-red-700">Alasan Penolakan:</p>
            <p class="text-sm text-red-600 mt-1 leading-relaxed">{{ $transaksi->alasan_tolak }}</p>
        </div>
    </div>
    @endif

    <!-- Ringkasan Nominal -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <div class="bg-white rounded-3xl shadow-card border border-gray-100 p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Total Bayar</p>
            <p class="num text-xl font-extrabold text-brand-700 mt-1.5">Rp {{ number_format($transaksi->total_bayar,0,',','.') }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-card border border-gray-100 p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Jumlah Zakat</p>
            <p class="num text-xl font-extrabold text-gray-900 mt-1.5">Rp {{ number_format($transaksi->donasiZakat?->jumlah_zakat??0,0,',','.') }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-card border border-gray-100 p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Metode</p>
            <p class="text-base font-extrabold text-gray-900 mt-2">{{ $transaksi->metode_bayar==='online' ? '🌐 Online' : '📍 Di Tempat' }}</p>
        </div>
        <div class="bg-white rounded-3xl shadow-card border border-gray-100 p-4">
            <p class="text-[11px] font-bold text-gray-400 uppercase tracking-wider">Jenis Zakat</p>
            <p class="text-base font-extrabold text-gray-900 mt-2">{{ $transaksi->donasiZakat?->label_jenis_zakat }}</p>
        </div>
    </div>

    <!-- Detail Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <!-- Muzakki -->
        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">👤</span>
                Data Muzakki
            </h3>
            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Nama</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->user?->name }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Email</dt>
                    <dd class="font-medium text-gray-900 text-right break-all">{{ $transaksi->user?->email }}</dd>
                </div>
                @if($transaksi->user?->phone)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Telepon</dt>
                    <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->user->phone }}</dd>
                </div>
                @endif
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Bergabung</dt>
                    <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->user?->created_at?->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Donasi -->
        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-brand-100 rounded-xl flex items-center justify-center">🌙</span>
                Data Donasi Zakat
            </h3>
            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Nama Muzakki</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->donasiZakat?->nama_muzakki }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Jenis Zakat</dt>
                    <dd class="font-bold text-brand-700 text-right">{{ $transaksi->donasiZakat?->label_jenis_zakat }}</dd>
                </div>
                <div class="flex justify-between gap-4 pt-3 border-t border-gray-100">
                    <dt class="text-gray-500">Jumlah Zakat</dt>
                    <dd class="num font-extrabold text-gray-900 text-right">Rp {{ number_format($transaksi->donasiZakat?->jumlah_zakat??0,0,',','.') }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Tanggal Donasi</dt>
                    <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->donasiZakat?->created_at?->format('d M Y') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Transaksi -->
        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-purple-100 rounded-xl flex items-center justify-center">💳</span>
                Data Transaksi
            </h3>
            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Kode</dt>
                    <dd class="num text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1.5 rounded-lg">{{ $transaksi->kode_transaksi }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Metode</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->metode_bayar==='online' ? '🌐 Online' : '📍 Di Tempat' }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Tanggal</dt>
                    <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->tanggal_transaksi?->format('d M Y H:i') }}</dd>
                </div>
                @if($transaksi->diverifikasi_at)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Diverifikasi</dt>
                    <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->diverifikasi_at->format('d M Y H:i') }}</dd>
                </div>
                @endif
                <div class="flex justify-between gap-4 pt-3 border-t border-gray-100">
                    <dt class="text-gray-500">Total Bayar</dt>
                    <dd class="num font-extrabold text-brand-700 text-right">Rp {{ number_format($transaksi->total_bayar,0,',','.') }}</dd>
                </div>
            </dl>
        </div>

        <!-- Pembayaran -->
        @if($transaksi->pembayaran)
        <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-amber-100 rounded-xl flex items-center justify-center">🏦</span>
                Data Pembayaran
            </h3>
            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Metode</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->pembayaran->metode_pembayaran }}</dd>
                </div>
                @if($transaksi->pembayaran->nama_bank)
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Bank</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->pembayaran->nama_bank }}</dd>
                </div>
                @endif
                <div class="flex justify-between gap-4 pt-3 border-t border-gray-100">
                    <dt class="text-gray-500">Total</dt>
                    <dd class="num font-extrabold text-brand-700 text-right">Rp {{ number_format($transaksi->pembayaran->total_bayar,0,',','.') }}</dd>
                </div>
            </dl>

            @if($transaksi->pembayaran->bukti_transfer)
            <div class="mt-5 pt-5 border-t border-gray-100">
                <p class="text-xs text-gray-400 font-bold mb-3">Bukti Transfer</p>
                <button @click="showBuktiModal=true"
                    class="relative overflow-hidden rounded-2xl border-2 border-dashed border-brand-200 hover:border-brand-400 cursor-pointer w-40 h-28 flex items-center justify-center group transition-colors shadow-card">
                    <img src="{{ $transaksi->pembayaran->bukti_transfer_url }}" alt="Bukti transfer {{ $transaksi->kode_transaksi }}" class="w-full h-full object-cover">
                    <div class="absolute inset-0 bg-slate-900/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                </button>
                <p class="text-[11px] text-gray-400 mt-2">Ketuk gambar untuk memperbesar.</p>
            </div>
            <!-- Lightbox -->
            <div x-show="showBuktiModal" x-cloak @click="showBuktiModal=false"
                class="fixed inset-0 bg-slate-900/85 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <img src="{{ $transaksi->pembayaran->bukti_transfer_url }}" alt="Bukti transfer diperbesar" class="max-w-2xl w-full max-h-[85vh] object-contain rounded-3xl shadow-2xl bg-white" @click.stop>
            </div>
            @endif
        </div>
        @endif
    </div>

    <!-- Modal Terima -->
    <div x-show="showTerimaModal" x-cloak @click.self="showTerimaModal=false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-4xl p-6 sm:p-7 max-w-sm w-full shadow-2xl fade-up" @click.stop>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-brand-100 rounded-3xl flex items-center justify-center mx-auto mb-4 text-4xl">✅</div>
                <h3 class="font-extrabold text-gray-900 text-lg">Terima Transaksi?</h3>
                <p class="text-gray-500 text-sm mt-2 leading-relaxed">Transaksi <strong class="num text-brand-700">{{ $transaksi->kode_transaksi }}</strong> akan diterima dan user akan mendapat notifikasi.</p>
            </div>
            <div class="flex gap-3">
                <button @click="showTerimaModal=false" class="btn btn-ghost flex-1">Batal</button>
                <form action="{{ route('admin.transaksi.terima', $transaksi->id) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" class="btn btn-primary w-full">Ya, Terima</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Tolak -->
    <div x-show="showTolakModal" x-cloak @click.self="showTolakModal=false"
        class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
        <div class="bg-white rounded-4xl p-6 sm:p-7 max-w-sm w-full shadow-2xl fade-up" @click.stop>
            <div class="text-center mb-6">
                <div class="w-16 h-16 bg-red-100 rounded-3xl flex items-center justify-center mx-auto mb-4 text-4xl">❌</div>
                <h3 class="font-extrabold text-gray-900 text-lg">Tolak Transaksi?</h3>
                <p class="text-gray-500 text-sm mt-2">Alasan penolakan akan dikirim ke muzakki.</p>
            </div>
            <form action="{{ route('admin.transaksi.tolak', $transaksi->id) }}" method="POST" class="space-y-4">
                @csrf
                <textarea name="alasan_tolak" rows="4" required minlength="10"
                    class="fld fld-error" placeholder="Tulis alasan penolakan dengan jelas..."></textarea>
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
