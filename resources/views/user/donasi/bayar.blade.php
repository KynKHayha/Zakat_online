@extends('layouts.app')

@section('title', 'Pilih Metode Pembayaran')

@push('styles')
<style>
    .bank-option { border: 2px solid #e5e7eb; transition: border-color .2s ease, background-color .2s ease, box-shadow .2s ease; }
    .bank-option:hover { border-color: #6ee7b7; background-color: #f0fdf4; }
    .bank-option.active { border-color: #059669; background: #ecfdf5; box-shadow: 0 10px 22px -12px rgba(5,150,105,.5); }
    .upload-zone { border: 2px dashed #d1fae5; transition: border-color .2s ease, background-color .2s ease; }
    .upload-zone:hover, .upload-zone.dragover { border-color: #10b981; background: #ecfdf5; }
    .method-tab { transition: all .2s ease; }
    .method-tab.is-active { background: #fff; color: #047857; box-shadow: 0 6px 16px -8px rgba(5,150,105,.45); }
    .modal-backdrop { background: rgba(15,23,42,.55); backdrop-filter: blur(6px); }
    .modal-box { animation: modalIn .3s ease; }
    @keyframes modalIn { from { opacity:0; transform:scale(.94) translateY(16px); } to { opacity:1; transform:scale(1) translateY(0); } }
</style>
@endpush

@section('content')
<div x-data="bayarPage()" x-init="init()" class="max-w-5xl mx-auto">

    {{-- ═══════════ HEADER ═══════════ --}}
    <div class="mb-5">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-brand-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Metode Pembayaran</h1>
        <p class="text-sm text-gray-500 mt-1.5">Pilih cara yang paling mudah untuk Anda — dua-duanya aman dan tercatat.</p>
    </div>

    {{-- ═══════════ RINGKASAN TAGIHAN ═══════════ --}}
    <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-500 hero-pattern rounded-3xl p-5 sm:p-7 text-white shadow-glow mb-6">
        <div class="relative flex flex-col sm:flex-row sm:items-center justify-between gap-5">
            <div class="flex items-center gap-4 min-w-0">
                <div class="w-14 h-14 bg-white/15 border border-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center text-2xl flex-shrink-0">
                    @switch($donasi->jenis_zakat ?? '')
                        @case('fitrah') 🌾 @break
                        @case('mal') 💰 @break
                        @case('profesi') 💼 @break
                        @case('perniagaan') 🏪 @break
                        @case('pertanian') 🌱 @break
                        @case('emas_perak') 🥇 @break
                        @default 📦
                    @endswitch
                </div>
                <div class="min-w-0">
                    <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">{{ $donasi->label_jenis_zakat ?? 'Zakat' }}</p>
                    <p class="font-extrabold text-base sm:text-lg truncate">{{ $donasi->nama_muzakki ?? auth()->user()->name }}</p>
                    <p class="text-brand-200 text-xs mt-0.5">Diverifikasi amil maksimal 1&times;24 jam</p>
                </div>
            </div>
            <div class="sm:text-right flex-shrink-0">
                <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Total yang Harus Dibayar</p>
                <p class="num text-3xl sm:text-4xl font-extrabold tracking-tight mt-1">Rp {{ number_format($donasi->jumlah_zakat ?? 0, 0, ',', '.') }}</p>
            </div>
        </div>
    </div>

    {{-- ═══════════ PESAN ═══════════ --}}
    @if(session('error'))
    <div class="flex items-start justify-between gap-3 bg-red-50 border border-red-200 rounded-2xl p-4 mb-5">
        <p class="text-sm text-red-700 font-semibold">{{ session('error') }}</p>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-400 hover:text-red-600 text-lg leading-none">&times;</button>
    </div>
    @endif

    @if($errors->any())
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl p-4 mb-5">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <div>
            <p class="text-sm font-extrabold text-red-800">Perlu diperbaiki</p>
            <ul class="mt-1 space-y-1">
                @foreach($errors->all() as $err)
                    <li class="text-xs text-red-700 flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-red-500 flex-shrink-0"></span>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    {{-- ═══════════ PEMILIH METODE (MOBILE) ═══════════ --}}
    <div class="lg:hidden grid grid-cols-2 gap-2 bg-gray-100 rounded-2xl p-1.5 mb-5">
        <button type="button" @click="tab = 'online'" class="method-tab py-3 rounded-xl text-xs font-extrabold text-gray-500" :class="tab === 'online' ? 'is-active' : ''">
            💳 Transfer Bank
        </button>
        <button type="button" @click="tab = 'tempat'" class="method-tab py-3 rounded-xl text-xs font-extrabold text-gray-500" :class="tab === 'tempat' ? 'is-active' : ''">
            🕌 Bayar di Tempat
        </button>
    </div>

    {{-- ═══════════ KARTU METODE ═══════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

        {{-- ============ BAYAR ONLINE ============ --}}
        <div x-show="tab === 'online'" x-transition
             class="bg-white rounded-3xl shadow-soft border overflow-hidden"
             :class="tab === 'online' ? 'border-brand-200' : 'border-gray-100'">

            <div class="p-5 sm:p-6 border-b border-gray-100 cursor-pointer" @click="tab = 'online'">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="w-12 h-12 bg-blue-50 border border-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                    </div>
                    <span x-show="tab === 'online'" x-cloak class="chip bg-brand-50 text-brand-700 border border-brand-100">✓ Dipilih</span>
                </div>
                <h3 class="text-base sm:text-lg font-extrabold text-gray-900">Bayar Online (Transfer)</h3>
                <p class="text-sm text-gray-500 mt-1 leading-relaxed">Transfer ke rekening amil lalu unggah buktinya. Verifikasi maksimal 1&times;24 jam.</p>
            </div>

            {{-- Pilih bank --}}
            <div class="p-5 sm:p-6 bg-gray-50/70 border-b border-gray-100">
                <p class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider mb-3">Rekening Tujuan</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    @foreach($bankList as $bank)
                    <button type="button" class="bank-option text-left bg-white rounded-2xl p-3.5"
                            :class="{ 'active': selectedBank === '{{ $bank['nama'] }}' }"
                            @click="selectBank('{{ $bank['nama'] }}', '{{ $bank['norek'] }}', '{{ $bank['atas_nama'] }}')">
                        <div class="flex items-center gap-2 mb-1">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center text-white text-[10px] font-extrabold flex-shrink-0
                                @if(str_contains($bank['nama'], 'BRI')) bg-blue-600
                                @elseif(str_contains($bank['nama'], 'BNI')) bg-orange-500
                                @elseif(str_contains($bank['nama'], 'Mandiri')) bg-yellow-500
                                @else bg-brand-600 @endif">
                                {{ strtoupper(substr(last(explode(' ', $bank['nama'])), 0, 3)) }}
                            </div>
                            <span class="text-xs font-extrabold text-gray-700">{{ $bank['nama'] }}</span>
                        </div>
                        <p class="num text-sm font-extrabold text-gray-800">{{ $bank['norek'] }}</p>
                        <p class="text-[11px] text-gray-500">{{ $bank['atas_nama'] }}</p>
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="p-5 sm:p-6">
                {{-- Detail rekening terpilih --}}
                <div x-show="selectedBank" x-cloak x-transition
                     class="relative overflow-hidden bg-gradient-to-br from-brand-600 to-brand-800 hero-pattern rounded-2xl p-5 text-white mb-5">
                    <div class="relative">
                        <div class="flex items-start justify-between gap-3">
                            <div class="min-w-0">
                                <p class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Rekening Tujuan</p>
                                <p class="font-bold text-sm mt-1" x-text="selectedBank"></p>
                            </div>
                            <button type="button" @click="copyRekening()"
                                    class="inline-flex items-center gap-1.5 text-[11px] font-extrabold bg-white/15 hover:bg-white/25 border border-white/20 px-3 py-1.5 rounded-full transition-colors flex-shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                <span x-text="copied ? 'Tersalin!' : 'Salin'">Salin</span>
                            </button>
                        </div>
                        <p class="num text-2xl font-extrabold tracking-tight mt-2" x-text="selectedNorek"></p>
                        <p class="text-brand-200 text-xs mt-1" x-text="selectedAtas"></p>
                        <div class="mt-4 pt-3 border-t border-white/15 flex items-center justify-between">
                            <span class="text-brand-200 text-[11px] font-bold uppercase tracking-wider">Nominal Transfer</span>
                            <span class="num text-base font-extrabold">Rp {{ number_format($donasi->jumlah_zakat ?? 0, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <form action="{{ route('user.donasi.bayar-online', $donasi->id) }}" method="POST"
                      enctype="multipart/form-data" id="formOnline">
                    @csrf
                    <input type="hidden" name="metode_pembayaran" value="transfer">
                    <input type="hidden" name="nama_bank" id="hiddenBank" value="">

                    <div>
                        <label class="fld-label" for="tanggalOnline">Tanggal Rencana Bayar</label>
                        <input type="date" name="tanggal_rencana_bayar" id="tanggalOnline" required
                               min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+7 days')) }}"
                               value="{{ date('Y-m-d') }}"
                               class="fld {{ $errors->has('tanggal_rencana_bayar') ? 'fld-error' : '' }}">
                        <p class="text-xs text-gray-400 mt-1.5">Maksimal 7 hari ke depan.</p>
                        @error('tanggal_rencana_bayar')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                    </div>

                    <div class="mt-5">
                        <label class="fld-label">Unggah Bukti Transfer <span class="text-gray-400 font-medium">(boleh nanti)</span></label>
                        <div class="upload-zone rounded-2xl p-6 text-center cursor-pointer" id="uploadZone"
                             @dragover.prevent="$el.classList.add('dragover')"
                             @dragleave="$el.classList.remove('dragover')"
                             @drop.prevent="handleDrop($event)"
                             @click="$refs.fileInput.click()">
                            <div x-show="!uploadedFile">
                                <div class="w-12 h-12 bg-brand-50 border border-brand-100 rounded-2xl flex items-center justify-center mx-auto mb-2.5">
                                    <svg class="w-6 h-6 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <p class="text-sm font-bold text-gray-700">Ketuk untuk pilih foto bukti transfer</p>
                                <p class="text-xs text-gray-400 mt-1">JPG, PNG, JPEG &middot; maksimal 2 MB</p>
                            </div>
                            <div x-show="uploadedFile" x-cloak class="flex items-center justify-center gap-3">
                                <svg class="w-7 h-7 text-brand-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <div class="text-left min-w-0">
                                    <p class="text-sm font-bold text-brand-700 truncate" x-text="uploadedFile?.name"></p>
                                    <p class="text-xs text-gray-400" x-text="uploadedFile ? formatFileSize(uploadedFile.size) : ''"></p>
                                </div>
                                <button type="button" @click.stop="uploadedFile = null; fileError=''; $refs.fileInput.value = ''"
                                        class="text-red-400 hover:text-red-600 flex-shrink-0 ml-2 text-lg font-bold">&times;</button>
                            </div>
                        </div>
                        <input type="file" name="bukti_transfer" accept="image/jpeg,image/png,image/jpg,image/gif"
                               class="hidden" x-ref="fileInput" @change="handleFileChange($event)">
                        <p x-show="fileError" x-cloak class="text-xs text-red-600 font-bold mt-2" x-text="fileError"></p>
                        @error('bukti_transfer')<p class="text-xs text-red-600 font-semibold mt-2">{{ $message }}</p>@enderror
                    </div>

                    <div x-show="!selectedBank" x-cloak class="flex items-center gap-2.5 bg-amber-50 border border-amber-100 rounded-2xl p-3.5 mt-5">
                        <span class="text-base leading-none">⚠️</span>
                        <p class="text-xs text-amber-800 font-semibold">Pilih rekening tujuan transfer terlebih dahulu.</p>
                    </div>

                    <button type="button" @click="submitOnline()" :disabled="!selectedBank || isSubmittingOnline"
                            class="btn w-full mt-5 !py-4 text-sm"
                            :class="(!selectedBank || isSubmittingOnline) ? '!bg-gray-100 !text-gray-400 !shadow-none' : 'btn-primary'">
                        <svg x-show="isSubmittingOnline" x-cloak class="w-5 h-5 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <svg x-show="!isSubmittingOnline" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                        <span x-text="isSubmittingOnline ? 'Memproses...' : (!selectedBank ? 'Pilih rekening dulu' : (uploadedFile ? 'Unggah bukti & konfirmasi' : 'Lanjut, bukti menyusul'))">Lanjut, bukti menyusul</span>
                    </button>
                </form>
            </div>
        </div>

        {{-- ============ BAYAR DI TEMPAT ============ --}}
        <div x-show="tab === 'tempat'" x-transition
             class="bg-white rounded-3xl shadow-soft border overflow-hidden"
             :class="tab === 'tempat' ? 'border-brand-200' : 'border-gray-100'">

            <div class="p-5 sm:p-6 border-b border-gray-100 cursor-pointer" @click="tab = 'tempat'">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div class="w-12 h-12 bg-brand-50 border border-brand-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <span x-show="tab === 'tempat'" x-cloak class="chip bg-brand-50 text-brand-700 border border-brand-100">✓ Dipilih</span>
                </div>
                <h3 class="text-base sm:text-lg font-extrabold text-gray-900">Bayar di Tempat (Tunai)</h3>
                <p class="text-sm text-gray-500 mt-1 leading-relaxed">Datang ke kantor, bayar tunai, dan langsung diterima amil.</p>
                <div class="flex flex-wrap gap-2 mt-3">
                    <span class="chip bg-brand-50 text-brand-700 border border-brand-100">⚡ Langsung lunas</span>
                    <span class="chip bg-amber-50 text-amber-700 border border-amber-100">🎫 Tiket QR digital</span>
                </div>
            </div>

            <div class="p-5 sm:p-6 bg-gray-50/70 border-b border-gray-100 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-red-50 border border-red-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">Alamat Kantor</p>
                        <p class="text-sm text-gray-700 mt-1 leading-relaxed">Jl. Masjid Al-Ikhlas No. 12, Kel. Sumber Jaya, Kec. Kemiling, Kota Bandar Lampung 35158</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-blue-50 border border-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">Jam Operasional</p>
                        <div class="mt-1.5 space-y-1.5 text-sm">
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Senin &ndash; Jumat</span><span class="font-bold text-gray-800">08.00 &ndash; 16.00 WIB</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Sabtu</span><span class="font-bold text-gray-800">08.00 &ndash; 12.00 WIB</span></div>
                            <div class="flex justify-between gap-3"><span class="text-gray-500">Minggu</span><span class="font-bold text-red-500">Tutup</span></div>
                        </div>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="w-9 h-9 bg-brand-50 border border-brand-100 rounded-xl flex items-center justify-center flex-shrink-0">
                        <svg class="w-4 h-4 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-extrabold text-gray-500 uppercase tracking-wider">Kontak</p>
                        <p class="text-sm text-gray-700 mt-1 font-semibold">(0721) 123-4567</p>
                        <p class="text-sm text-gray-500">WhatsApp 0812-3456-7890</p>
                    </div>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-4 mb-5">
                    <p class="text-[11px] font-extrabold text-gray-500 uppercase tracking-wider mb-3">Yang Perlu Dibawa</p>
                    <ol class="space-y-2.5">
                        <li class="flex items-start gap-3 text-sm text-gray-700">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-extrabold flex-shrink-0">1</span>
                            Tiket digital ini (screenshot atau cetak)
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-700">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-extrabold flex-shrink-0">2</span>
                            KTP / identitas diri
                        </li>
                        <li class="flex items-start gap-3 text-sm text-gray-700">
                            <span class="w-6 h-6 rounded-full bg-brand-100 text-brand-700 flex items-center justify-center text-xs font-extrabold flex-shrink-0">3</span>
                            Uang tunai <strong class="text-brand-700">Rp {{ number_format($donasi->jumlah_zakat ?? 0, 0, ',', '.') }}</strong>
                        </li>
                    </ol>
                </div>

                <button type="button" @click="showConfirmModal = true; tab = 'tempat'" class="btn btn-primary w-full !py-4 text-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    Pilih Bayar di Tempat
                </button>
                <p class="text-center text-xs text-gray-400 mt-2.5">Anda akan menerima tiket digital berisi QR Code.</p>
            </div>
        </div>
    </div>

    {{-- ═══════════ MODAL KONFIRMASI BAYAR DI TEMPAT ═══════════ --}}
    <div x-show="showConfirmModal" x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-end sm:items-center justify-center p-0 sm:p-4 modal-backdrop"
         @click.self="showConfirmModal = false">

        <div class="modal-box bg-white w-full sm:max-w-md sm:rounded-3xl rounded-t-3xl overflow-hidden shadow-2xl max-h-[92vh] overflow-y-auto">
            <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 to-brand-600 hero-pattern p-6 text-center text-white">
                <div class="relative">
                    <div class="w-14 h-14 bg-white/15 border border-white/20 rounded-2xl flex items-center justify-center mx-auto mb-3">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    </div>
                    <h3 class="text-lg font-extrabold">Konfirmasi Bayar di Tempat</h3>
                    <p class="text-brand-200 text-sm mt-1">Bayar langsung di kantor amil kami.</p>
                </div>
            </div>

            <div class="p-5 sm:p-6">
                <dl class="space-y-2.5 bg-gray-50 rounded-2xl p-4 mb-5">
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-sm text-gray-500">Nama</dt>
                        <dd class="text-sm font-bold text-gray-800 text-right">{{ $donasi->nama_muzakki ?? auth()->user()->name }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-sm text-gray-500">Jenis Zakat</dt>
                        <dd class="text-sm font-bold text-gray-800 capitalize text-right">{{ $donasi->label_jenis_zakat ?? $donasi->jenis_zakat }}</dd>
                    </div>
                    <div class="flex items-start justify-between gap-3">
                        <dt class="text-sm text-gray-500">Metode</dt>
                        <dd class="text-sm font-bold text-gray-800 text-right">Tunai di kantor</dd>
                    </div>
                    <div class="pt-2.5 mt-1 border-t border-dashed border-gray-200 flex items-center justify-between gap-3">
                        <dt class="text-sm font-extrabold text-gray-700">Jumlah</dt>
                        <dd class="num text-base font-extrabold text-brand-700">Rp {{ number_format($donasi->jumlah_zakat ?? 0, 0, ',', '.') }}</dd>
                    </div>
                </dl>

                <div class="flex items-start gap-2.5 bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-5">
                    <span class="text-base leading-none">⚠️</span>
                    <p class="text-xs text-amber-800 leading-relaxed">Setelah konfirmasi, Anda mendapat tiket dan kode transaksi. Harap datang ke kantor untuk menyelesaikan pembayaran.</p>
                </div>

                <div class="flex flex-col-reverse sm:flex-row gap-3">
                    <button type="button" @click="showConfirmModal = false" class="btn btn-ghost w-full sm:w-auto">Batal</button>
                    <form action="{{ route('user.donasi.bayar-ditempat', $donasi->id) }}" method="POST" class="flex-1 space-y-4" id="formDitempat">
                        @csrf
                        <div>
                            <label class="fld-label">Tanggal Rencana Kedatangan</label>
                            <input type="date" name="tanggal_rencana_bayar" required
                                   min="{{ date('Y-m-d') }}" max="{{ date('Y-m-d', strtotime('+7 days')) }}"
                                   value="{{ date('Y-m-d') }}" class="fld">
                            @error('tanggal_rencana_bayar')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                        </div>
                        <button type="button" @click="konfirmasiBayarTempat()" :disabled="isSubmittingTempat"
                                class="btn btn-primary w-full">
                            <svg x-show="isSubmittingTempat" x-cloak class="w-4 h-4 animate-spin flex-shrink-0" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                            <span x-text="isSubmittingTempat ? 'Memproses...' : 'Ya, saya akan datang'">Ya, saya akan datang</span>
                            <svg x-show="!isSubmittingTempat" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
function bayarPage() {
    return {
        tab: 'online',
        selectedBank: '',
        selectedNorek: '',
        selectedAtas: '',
        showConfirmModal: false,
        uploadedFile: null,
        fileError: '',
        isSubmittingOnline: false,
        isSubmittingTempat: false,
        copied: false,

        init() {
            // Reset loading states on page load (jika browser back-button)
            this.isSubmittingOnline = false;
            this.isSubmittingTempat = false;
        },

        selectBank(nama, norek, atas) {
            this.selectedBank = nama;
            this.selectedNorek = norek;
            this.selectedAtas = atas;
            this.tab = 'online';
            // Update hidden input
            document.getElementById('hiddenBank').value = nama;
        },

        handleFileChange(event) {
            const file = event.target.files[0];
            this.fileError = '';
            if (!file) return;

            // Cek ukuran
            if (file.size > 2 * 1024 * 1024) {
                this.fileError = 'Ukuran file maksimal 2 MB. Silakan pilih foto lain.';
                event.target.value = '';
                this.uploadedFile = null;
                return;
            }

            // Cek tipe file
            const allowed = ['image/jpeg','image/jpg','image/png','image/gif'];
            if (!allowed.includes(file.type)) {
                this.fileError = 'Hanya file gambar (JPG, PNG, JPEG) yang diperbolehkan.';
                event.target.value = '';
                this.uploadedFile = null;
                return;
            }

            this.uploadedFile = file;
        },

        handleDrop(event) {
            event.target.classList.remove('dragover');
            this.fileError = '';
            const file = event.dataTransfer.files[0];
            if (!file) return;

            if (file.size > 2 * 1024 * 1024) {
                this.fileError = 'Ukuran file maksimal 2 MB. Silakan pilih foto lain.';
                return;
            }

            this.uploadedFile = file;
            // Sync ke input file
            try {
                const dt = new DataTransfer();
                dt.items.add(file);
                this.$refs.fileInput.files = dt.files;
            } catch(e) {}
        },

        submitOnline() {
            // Validasi manual sebelum submit
            if (!this.selectedBank) {
                this.tab = 'online';
                this.selectedBank = '';
                this.$nextTick(() => document.getElementById('uploadZone')?.scrollIntoView({ behavior: 'smooth', block: 'center' }));
                return;
            }

            // Set hidden input bank
            document.getElementById('hiddenBank').value = this.selectedBank;

            this.isSubmittingOnline = true;

            // Submit form HTML biasa
            document.getElementById('formOnline').submit();
        },

        konfirmasiBayarTempat() {
            this.isSubmittingTempat = true;
            // Submit form bayar di tempat
            document.getElementById('formDitempat').submit();
        },

        async copyRekening() {
            try {
                await navigator.clipboard.writeText(this.selectedNorek);
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            } catch(e) {
                // Fallback
                const el = document.createElement('textarea');
                el.value = this.selectedNorek;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
                this.copied = true;
                setTimeout(() => { this.copied = false; }, 2000);
            }
        },

        formatFileSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
        }
    }
}
</script>
@endpush
