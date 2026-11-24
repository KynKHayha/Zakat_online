@extends('layouts.app')
@section('title', 'Detail Transaksi')
@section('page-title', 'Detail Transaksi')
@section('page-subtitle', 'Informasi lengkap transaksi zakat')

@section('content')
<div class="max-w-3xl mx-auto space-y-5" x-data="{ showBatalModal: false, showBuktiModal: false }">

    <!-- Kembali -->
    <a href="{{ route('user.riwayat') }}"
       class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-600 transition-colors group">
        <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
        </svg>
        Kembali ke Riwayat
    </a>

    <!-- Banner Status -->
    @php
        $bannerConfig = [
            'menunggu'   => ['bg'=>'bg-gradient-to-r from-amber-500 to-yellow-500','icon'=>'⏳','label'=>'Menunggu Verifikasi','sub'=>'Transaksi sedang dalam antrian verifikasi oleh admin.'],
            'sukses'     => ['bg'=>'bg-gradient-to-r from-brand-700 to-brand-500','icon'=>'✅','label'=>'Pembayaran Berhasil','sub'=>'Alhamdulillah! Zakat Anda telah diterima dan dikonfirmasi.'],
            'ditolak'    => ['bg'=>'bg-gradient-to-r from-red-600 to-red-500','icon'=>'❌','label'=>'Pembayaran Ditolak','sub'=>'Pembayaran tidak dapat diproses. Lihat alasan di bawah.'],
            'dibatalkan' => ['bg'=>'bg-gradient-to-r from-gray-600 to-gray-500','icon'=>'🚫','label'=>'Dibatalkan','sub'=>'Transaksi ini telah dibatalkan.'],
        ];
        $banner = $bannerConfig[$transaksi->status] ?? $bannerConfig['menunggu'];
    @endphp

    <div class="{{ $banner['bg'] }} hero-pattern relative overflow-hidden rounded-4xl p-6 text-white shadow-glow">
        <div class="relative z-10 flex items-start gap-4">
            <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center text-3xl flex-shrink-0">
                {{ $banner['icon'] }}
            </div>
            <div class="min-w-0">
                <h2 class="text-xl sm:text-2xl font-extrabold">{{ $banner['label'] }}</h2>
                <p class="text-white/85 text-sm mt-1 leading-relaxed">{{ $banner['sub'] }}</p>
                <p class="num text-white/70 text-xs mt-2 font-bold">{{ $transaksi->kode_transaksi }}</p>
            </div>
        </div>
        @if($transaksi->status === 'ditolak' && $transaksi->alasan_tolak)
            <div class="relative z-10 mt-4 bg-white/15 rounded-2xl p-4 border border-white/25">
                <p class="text-sm font-bold text-white">Alasan Penolakan</p>
                <p class="text-white/90 text-sm mt-1 leading-relaxed">{{ $transaksi->alasan_tolak }}</p>
            </div>
        @endif
    </div>

    <!-- Data Zakat & Transaksi -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
        <div class="bg-white rounded-4xl shadow-card border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-brand-100 rounded-xl flex items-center justify-center">🌙</span>
                Data Zakat
            </h3>
            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Nama Muzakki</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->donasiZakat?->nama_muzakki }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Jenis Zakat</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->donasiZakat?->label_jenis_zakat }}</dd>
                </div>
                <div class="flex justify-between gap-4 pt-3 border-t border-gray-100">
                    <dt class="text-gray-500">Jumlah Zakat</dt>
                    <dd class="num font-extrabold text-brand-700 text-right">Rp {{ number_format($transaksi->donasiZakat?->jumlah_zakat ?? 0, 0, ',', '.') }}</dd>
                </div>
                @if($transaksi->donasiZakat?->keterangan)
                    <div class="pt-3 border-t border-gray-100">
                        <dt class="text-xs text-gray-400 mb-1">Keterangan</dt>
                        <dd class="text-sm text-gray-600 leading-relaxed">{{ $transaksi->donasiZakat->keterangan }}</dd>
                    </div>
                @endif
            </dl>
        </div>

        <div class="bg-white rounded-4xl shadow-card border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center">💳</span>
                Data Transaksi
            </h3>
            <dl class="space-y-3.5 text-sm">
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Kode</dt>
                    <dd class="num text-xs font-bold text-gray-700 bg-gray-100 px-2.5 py-1 rounded-lg">{{ $transaksi->kode_transaksi }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Metode</dt>
                    <dd class="font-bold text-gray-900 text-right">{{ $transaksi->label_metode_bayar }}</dd>
                </div>
                <div class="flex justify-between gap-4">
                    <dt class="text-gray-500">Tanggal Transaksi</dt>
                    <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->tanggal_transaksi?->format('d M Y H:i') }}</dd>
                </div>
                @if($transaksi->batas_waktu_bayar)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">Rencana Pembayaran</dt>
                        <dd class="num font-medium text-gray-900 text-right">{{ $transaksi->tanggal_rencana_bayar?->format('d M Y') }}</dd>
                    </div>
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">Batas Waktu</dt>
                        <dd class="num font-bold text-red-500 text-right">{{ $transaksi->batas_waktu_bayar?->format('d M Y H:i') }} WIB</dd>
                    </div>
                @endif
                <div class="flex justify-between gap-4 pt-3 border-t border-gray-100">
                    <dt class="text-gray-500">Total Bayar</dt>
                    <dd class="num font-extrabold text-brand-700 text-right">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</dd>
                </div>
                @if($transaksi->diverifikasi_at)
                    <div class="flex justify-between gap-4">
                        <dt class="text-gray-500">Diverifikasi</dt>
                        <dd class="num text-gray-700 text-right">{{ $transaksi->diverifikasi_at->format('d M Y H:i') }}</dd>
                    </div>
                @endif
            </dl>
        </div>
    </div>

    <!-- Data Pembayaran -->
    @if($transaksi->pembayaran)
        <div class="bg-white rounded-4xl shadow-card border border-gray-100 p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-5 flex items-center gap-2.5">
                <span class="w-8 h-8 bg-purple-100 rounded-xl flex items-center justify-center">🏦</span>
                Data Pembayaran
            </h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-400 font-semibold">Metode</p>
                    <p class="text-sm font-bold text-gray-900 mt-1 capitalize">{{ $transaksi->pembayaran->metode_pembayaran }}</p>
                </div>
                @if($transaksi->pembayaran->nama_bank)
                    <div>
                        <p class="text-xs text-gray-400 font-semibold">Bank</p>
                        <p class="text-sm font-bold text-gray-900 mt-1">{{ $transaksi->pembayaran->nama_bank }}</p>
                    </div>
                @endif
                <div>
                    <p class="text-xs text-gray-400 font-semibold">Total Bayar</p>
                    <p class="num text-sm font-extrabold text-brand-700 mt-1">Rp {{ number_format($transaksi->pembayaran->total_bayar, 0, ',', '.') }}</p>
                </div>
                <div>
                    <p class="text-xs text-gray-400 font-semibold">Status Pembayaran</p>
                    <p class="text-sm font-bold text-gray-900 mt-1 capitalize">{{ ucfirst($transaksi->pembayaran->status) }}</p>
                </div>
            </div>

            @if($transaksi->pembayaran->bukti_transfer)
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <p class="text-xs text-gray-400 font-semibold mb-3">Bukti Transfer</p>
                    <button type="button" @click="showBuktiModal = true"
                            class="relative overflow-hidden rounded-2xl border-2 border-dashed border-gray-200 hover:border-brand-400 transition-colors cursor-pointer w-40 h-28 flex items-center justify-center group">
                        <img src="{{ $transaksi->pembayaran->bukti_transfer_url }}" alt="Bukti Transfer"
                             class="w-full h-full object-cover rounded-2xl">
                        <span class="absolute inset-0 bg-black/45 opacity-0 group-hover:opacity-100 transition-opacity rounded-2xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </span>
                    </button>
                    <p class="text-xs text-gray-400 mt-2">Ketuk gambar untuk memperbesar.</p>
                </div>
            @elseif($transaksi->status === 'menunggu')
                <div class="mt-5 pt-5 border-t border-gray-100">
                    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-4 mb-4">
                        <p class="text-sm font-bold text-amber-800">Bukti Transfer Belum Diunggah</p>
                        <p class="text-xs text-amber-700 mt-1 leading-relaxed">
                            Silakan unggah bukti transfer ke rekening {{ $transaksi->pembayaran->nama_bank }} sebelum batas waktu
                            agar transaksi Anda dapat diverifikasi.
                        </p>
                    </div>
                    <form action="{{ route('user.transaksi.upload-bukti', $transaksi->id) }}"
                          method="POST" enctype="multipart/form-data"
                          class="flex flex-col sm:flex-row gap-3 sm:items-end">
                        @csrf
                        <div class="flex-1">
                            <label class="fld-label">Pilih File Bukti Transfer</label>
                            <input type="file" name="bukti_transfer" required accept="image/jpeg,image/png,image/jpg"
                                   class="fld !py-2.5 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-1.5 file:text-xs file:font-bold file:text-brand-700">
                            @error('bukti_transfer')
                                <p class="text-red-500 text-xs mt-1.5">{{ $message }}</p>
                            @enderror
                        </div>
                        <button type="submit" class="btn btn-primary sm:w-auto">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                            </svg>
                            Unggah Bukti
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Lightbox Bukti -->
        @if($transaksi->pembayaran->bukti_transfer)
            <div x-show="showBuktiModal" x-cloak @click="showBuktiModal = false"
                 class="fixed inset-0 bg-black/85 backdrop-blur-sm z-50 flex items-center justify-center p-4"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <img src="{{ $transaksi->pembayaran->bukti_transfer_url }}" alt="Bukti Transfer"
                     class="max-w-3xl w-full max-h-[85vh] object-contain rounded-2xl shadow-2xl bg-white" @click.stop>
                <button type="button" @click="showBuktiModal = false"
                        class="absolute top-5 right-5 w-11 h-11 rounded-full bg-white/20 text-white text-xl font-bold hover:bg-white/30">✕</button>
            </div>
        @endif
    @endif

    <!-- Aksi -->
    @if($transaksi->status === 'menunggu')
        <div class="bg-white rounded-4xl shadow-card border border-gray-100 p-5 sm:p-6">
            <h3 class="font-extrabold text-gray-900 text-sm mb-4">Butuh?</h3>
            <div class="flex flex-col sm:flex-row gap-3">
                <button type="button" @click="showBatalModal = true"
                        class="btn bg-red-50 hover:bg-red-100 text-red-600 border border-red-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Batalkan Transaksi
                </button>
                @if($transaksi->metode_bayar === 'ditempat')
                    <a href="{{ route('user.donasi.tiket', $transaksi->id) }}" class="btn btn-primary">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"/>
                        </svg>
                        Cetak Tiket QR
                    </a>
                @endif
            </div>
        </div>

        <!-- Modal Batal -->
        <div x-show="showBatalModal" x-cloak @click.self="showBatalModal = false"
             class="fixed inset-0 bg-black/55 backdrop-blur-sm z-50 flex items-end sm:items-center justify-center p-0 sm:p-4"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
            <div class="bg-white rounded-t-3xl sm:rounded-4xl p-6 max-w-sm w-full shadow-2xl fade-up" @click.stop>
                <div class="text-center mb-5">
                    <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-3">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-gray-900 text-lg">Batalkan Transaksi?</h3>
                    <p class="text-gray-500 text-sm mt-2 leading-relaxed">
                        Transaksi <strong class="num">{{ $transaksi->kode_transaksi }}</strong> akan dibatalkan.
                        Tindakan ini tidak bisa diurungkan.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button type="button" @click="showBatalModal = false" class="btn btn-ghost flex-1">Tidak</button>
                    <form action="{{ route('user.transaksi.batal', $transaksi->id) }}" method="POST" class="flex-1">
                        @csrf
                        <button type="submit" class="btn bg-red-600 hover:bg-red-700 text-white w-full">Ya, Batalkan</button>
                    </form>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
