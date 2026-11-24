@extends('layouts.app')
@section('title', 'Tiket Pembayaran')
@section('page-title', 'Tiket Pembayaran')

@push('styles')
<style>
    @media print {
        body * { visibility: hidden !important; }
        #tiket-print, #tiket-print * { visibility: visible !important; }
        #tiket-print { position: fixed; left: 0; top: 0; width: 100%; padding: 20px; }
        .no-print { display: none !important; }
    }
    .perforation { position: relative; }
    .perforation::before,
    .perforation::after {
        content: ''; position: absolute; top: 50%; transform: translateY(-50%);
        width: 22px; height: 22px; border-radius: 9999px; background: #f1f5f9;
    }
    .perforation::before { left: -11px; }
    .perforation::after  { right: -11px; }
</style>
@endpush

@section('content')
<div class="max-w-lg mx-auto">
    <!-- Aksi (tidak ikut tercetak) -->
    <div class="no-print mb-5 flex items-center justify-between gap-3">
        <a href="{{ route('user.riwayat') }}"
           class="inline-flex items-center gap-2 text-sm text-gray-500 hover:text-brand-600 transition-colors group">
            <svg class="w-4 h-4 group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Kembali
        </a>
        <button type="button" onclick="window.print()" class="btn btn-primary !py-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
            </svg>
            Cetak Tiket
        </button>
    </div>

    <!-- Tiket -->
    <div id="tiket-print" class="bg-white rounded-4xl shadow-soft border border-gray-200 overflow-hidden">
        <!-- Header -->
        <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-500 hero-pattern px-8 py-8 text-center text-white">
            <div class="relative z-10">
                <div class="w-14 h-14 bg-white/20 border border-white/25 rounded-2xl flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-14a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 7.586V4z" clip-rule="evenodd"/>
                    </svg>
                </div>
                <h2 class="text-2xl font-extrabold tracking-tight">TIKET ZAKAT</h2>
                <p class="text-brand-100 text-sm mt-1">Bayar di Tempat · Tunai</p>
            </div>
        </div>

        <div class="perforation border-t-2 border-dashed border-gray-200 mx-4"></div>

        <!-- QR -->
        <div class="px-8 py-6 text-center">
            <div class="inline-block p-3 bg-white border-2 border-dashed border-brand-200 rounded-2xl">
                <div id="qrcode" class="flex justify-center"></div>
            </div>
            <p class="num font-extrabold text-lg text-gray-900 tracking-widest mt-4">{{ $transaksi->kode_transaksi }}</p>
            <p class="text-xs text-gray-400 mt-1">Tunjukkan QR code ini kepada petugas amil</p>
        </div>

        <div class="perforation border-t-2 border-dashed border-gray-200 mx-4"></div>

        <!-- Detail -->
        <dl class="px-8 py-6 space-y-3.5 text-sm">
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
                <dd class="num font-extrabold text-brand-700 text-base text-right">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-gray-500">Tanggal Transaksi</dt>
                <dd class="num font-bold text-gray-900 text-right">{{ $transaksi->tanggal_transaksi?->format('d M Y') }}</dd>
            </div>
            <div class="flex justify-between gap-4">
                <dt class="text-gray-500">Metode</dt>
                <dd class="font-bold text-orange-600 text-right">Bayar di Tempat</dd>
            </div>
        </dl>

        <div class="perforation border-t-2 border-dashed border-gray-200 mx-4"></div>

        <!-- Instruksi -->
        <div class="px-8 py-6 bg-amber-50">
            <h4 class="font-extrabold text-amber-800 text-sm mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                </svg>
                Instruksi Kedatangan
            </h4>
            <ol class="space-y-2.5 text-xs text-amber-800">
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-200 text-amber-800 font-extrabold flex items-center justify-center flex-shrink-0 text-[10px]">1</span>
                    <span>Datang ke <strong>Kantor Zakat Online</strong>, Jl. Islami No. 1, Jakarta</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-200 text-amber-800 font-extrabold flex items-center justify-center flex-shrink-0 text-[10px]">2</span>
                    <span>Jam operasional: <strong>Senin–Jumat, 08.00–16.00 WIB</strong></span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-200 text-amber-800 font-extrabold flex items-center justify-center flex-shrink-0 text-[10px]">3</span>
                    <span>Tunjukkan <strong>QR code ini atau kode transaksi</strong> kepada petugas</span>
                </li>
                <li class="flex items-start gap-2.5">
                    <span class="w-5 h-5 rounded-full bg-amber-200 text-amber-800 font-extrabold flex items-center justify-center flex-shrink-0 text-[10px]">4</span>
                    <span>Siapkan uang tunai sejumlah <strong class="num">Rp {{ number_format($transaksi->total_bayar, 0, ',', '.') }}</strong></span>
                </li>
            </ol>
        </div>

        <!-- Footer -->
        <div class="px-8 py-5 bg-gray-50 text-center border-t border-gray-100">
            @if($transaksi->batas_waktu_bayar)
                <p class="text-xs text-gray-500">
                    Rencana Kedatangan: <strong class="num text-gray-700">{{ $transaksi->tanggal_rencana_bayar?->format('d M Y') }}</strong>
                </p>
                <p class="text-xs text-red-500 mt-1 font-bold">
                    Batas Akhir: <strong class="num">{{ $transaksi->batas_waktu_bayar?->format('d M Y H:i') }} WIB</strong>
                </p>
            @else
                <p class="text-xs text-gray-500">
                    Tiket berlaku sampai: <strong class="num text-gray-700">{{ $transaksi->tanggal_transaksi?->addDays(7)->format('d M Y') }}</strong>
                </p>
            @endif
            <p class="text-xs text-gray-400 mt-2.5">© {{ date('Y') }} Zakat Online · Amanah &amp; Transparan</p>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    new QRCode(document.getElementById("qrcode"), {
        text: "{{ $transaksi->kode_transaksi }}",
        width: 180,
        height: 180,
        colorDark: "#047857",
        colorLight: "#ffffff",
        correctLevel: QRCode.CorrectLevel.H
    });
</script>
@endpush
@endsection
