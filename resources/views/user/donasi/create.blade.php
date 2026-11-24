@extends('layouts.app')

@section('title', 'Kalkulator & Pembayaran Zakat')

@push('styles')
<style>
    .jenis-card { position: relative; border: 2px solid #e5e7eb; transition: transform .22s ease, border-color .22s ease, box-shadow .22s ease, background .22s ease; }
    .jenis-card:hover { transform: translateY(-4px); border-color: #6ee7b7; box-shadow: 0 16px 32px -14px rgba(5,150,105,.35); }
    .jenis-card.is-active { border-color: #059669; background: linear-gradient(160deg, #ecfdf5, #d1fae5); box-shadow: 0 14px 30px -12px rgba(5,150,105,.4); }
    .jenis-card.is-active .jenis-check { opacity: 1; transform: scale(1); }
    .jenis-check { opacity: 0; transform: scale(.6); transition: opacity .2s ease, transform .2s ease; }
    .step-line { transition: background-color .4s ease; }
    .pane-enter { animation: paneIn .35s ease both; }
    @keyframes paneIn { from { opacity: 0; transform: translateX(18px); } to { opacity: 1; transform: translateX(0); } }
    .amt-shimmer { background-image: linear-gradient(115deg, rgba(255,255,255,.16) 0%, rgba(255,255,255,0) 40%); }
</style>
@endpush

@section('content')
<div x-data="zakatForm()" x-init="init()" class="max-w-5xl mx-auto">

    {{-- ═══════════ HEADER HALAMAN ═══════════ --}}
    <div class="mb-6">
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-gray-500 hover:text-brand-600 transition-colors mb-3">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            Kembali ke Dashboard
        </a>
        <div class="flex flex-wrap items-end justify-between gap-3">
            <div>
                <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">Kalkulator &amp; Bayar Zakat</h1>
                <p class="text-sm text-gray-500 mt-1.5">Tiga langkah singkat — hitung, periksa, tunaikan.</p>
            </div>
            <span class="chip bg-brand-50 text-brand-700 border border-brand-100">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Otomatis terhitung
            </span>
        </div>
    </div>

    {{-- ═══════════ STEPPER ═══════════ --}}
    <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-4 sm:p-5 mb-6">
        <div class="flex items-center">
            <template x-for="(s, i) in steps" :key="i">
                <div class="flex items-center flex-1 last:flex-none">
                    <div class="flex items-center gap-2.5 min-w-0">
                        <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-full flex items-center justify-center text-sm font-extrabold flex-shrink-0 transition-all duration-300"
                             :class="{
                                 'bg-gradient-to-br from-brand-600 to-brand-500 text-white shadow-glow': step > i+1,
                                 'bg-gradient-to-br from-brand-600 to-brand-500 text-white ring-4 ring-brand-100 shadow-glow': step === i+1,
                                 'bg-gray-100 text-gray-400': step < i+1
                             }">
                            <svg x-show="step > i+1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                            <span x-show="step <= i+1" x-text="i+1"></span>
                        </div>
                        <p class="text-xs sm:text-sm font-bold hidden sm:block truncate"
                           :class="step >= i+1 ? 'text-brand-700' : 'text-gray-400'" x-text="s"></p>
                    </div>
                    <div x-show="i < steps.length - 1"
                         class="step-line flex-1 h-1 rounded-full mx-2 sm:mx-3 bg-gray-100"
                         :class="step > i+1 ? '!bg-brand-400' : ''"></div>
                </div>
            </template>
        </div>
        <div class="mt-4 flex items-center justify-between text-[11px] font-bold">
            <span class="text-gray-500">Langkah <span class="text-brand-600 num" x-text="step"></span> dari 3</span>
            <span class="text-brand-600 num" x-text="Math.round((step/3)*100) + '%'"></span>
        </div>
        <div class="mt-1.5 h-1.5 bg-gray-100 rounded-full overflow-hidden">
            <div class="h-full bg-gradient-to-r from-brand-400 to-brand-600 rounded-full transition-all duration-500"
                 :style="'width: ' + Math.round((step/3)*100) + '%'"></div>
        </div>
    </div>

    {{-- ═══════════ ERROR VALIDASI ═══════════ --}}
    @if($errors->any())
    <div class="flex items-start gap-3 bg-red-50 border border-red-200 rounded-2xl p-4 mb-6">
        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
        <div>
            <p class="text-sm font-extrabold text-red-800">Perlu diperbaiki</p>
            <ul class="mt-1 space-y-1">
                @foreach($errors->all() as $error)
                    <li class="text-xs text-red-700 flex items-center gap-1.5"><span class="w-1 h-1 rounded-full bg-red-500 flex-shrink-0"></span>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif

    <form action="{{ route('user.donasi.store') }}" method="POST" id="zakatForm" @submit.prevent="submitForm()">
        @csrf

        {{-- ══════════════ LANGKAH 1: PILIH JENIS ══════════════ --}}
        <div x-show="step === 1" x-transition class="pane-enter">
            <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-5 sm:p-8">
                <h2 class="text-lg sm:text-xl font-extrabold text-gray-900">Jenis zakat mana yang ingin Anda tunaikan?</h2>
                <p class="text-sm text-gray-500 mt-1 mb-6">Ketentuan tiap jenis sudah kami sesuaikan dengan kadar syariah yang umum dipakai.</p>

                <div class="grid grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                    <template x-for="jenis in jenisZakatOptions" :key="jenis.value">
                        <button type="button" @click="selectJenis(jenis.value)"
                                class="jenis-card text-left bg-white rounded-2xl p-3.5 sm:p-5"
                                :class="{ 'is-active': form.jenis_zakat === jenis.value }">
                            <div class="flex items-start justify-between gap-2">
                                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-2xl bg-gray-50 border border-gray-100 flex items-center justify-center text-xl sm:text-2xl flex-shrink-0" x-text="jenis.icon"></div>
                                <span class="jenis-check w-5 h-5 sm:w-6 sm:h-6 rounded-full bg-brand-600 flex items-center justify-center flex-shrink-0">
                                    <svg class="w-3 h-3 sm:w-3.5 sm:h-3.5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </span>
                            </div>
                            <p class="font-extrabold text-gray-900 text-xs sm:text-sm mt-3 sm:mt-3.5" x-text="'Zakat ' + jenis.label"></p>
                            <p class="text-[11px] sm:text-xs text-gray-500 leading-relaxed mt-1" x-text="jenis.deskripsi"></p>
                            <span class="chip bg-brand-50 text-brand-700 border border-brand-100 mt-2.5 sm:mt-3 !text-[10px] sm:!text-xs !px-2 sm:!px-3" x-text="jenis.kadar"></span>
                        </button>
                    </template>
                </div>

                <input type="hidden" name="jenis_zakat" :value="form.jenis_zakat">

                <div x-show="form.jenis_zakat" x-cloak class="mt-6 flex items-center gap-3 p-4 bg-brand-50 rounded-2xl border border-brand-100">
                    <span class="text-2xl flex-shrink-0" x-text="selectedJenisData?.icon"></span>
                    <div class="min-w-0">
                        <p class="font-bold text-brand-800 text-sm" x-text="'Anda memilih: Zakat ' + selectedJenisData?.label"></p>
                        <p class="text-xs text-brand-600 mt-0.5" x-text="selectedJenisData?.info"></p>
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="button" @click="nextStep()" :disabled="!form.jenis_zakat" class="btn btn-primary">
                        Lanjut mengisi data
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
            </div>
        </div>

        {{-- ══════════════ LANGKAH 2: ISI DATA ══════════════ --}}
        <div x-show="step === 2" x-transition class="pane-enter">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

                {{-- Kolom input --}}
                <div class="lg:col-span-3 bg-white rounded-3xl shadow-soft border border-gray-100 p-5 sm:p-7">
                    <div class="flex items-center gap-3 mb-6 pb-5 border-b border-gray-100">
                        <div class="w-11 h-11 rounded-2xl bg-brand-50 border border-brand-100 flex items-center justify-center text-xl flex-shrink-0" x-text="selectedJenisData?.icon"></div>
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-lg font-extrabold text-gray-900" x-text="'Zakat ' + selectedJenisData?.label"></h2>
                            <p class="text-xs text-gray-500" x-text="selectedJenisData?.info"></p>
                        </div>
                    </div>

                    <div class="space-y-5">
                        {{-- Nama Muzakki --}}
                        <div>
                            <label class="fld-label" for="nama_muzakki">Nama Muzakki</label>
                            <input id="nama_muzakki" type="text" name="nama_muzakki" x-model="form.nama_muzakki"
                                   value="{{ auth()->user()->name }}" required
                                   class="fld {{ $errors->has('nama_muzakki') ? 'fld-error' : '' }}">
                            @error('nama_muzakki')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        {{-- ===== FITRAH ===== --}}
                        <div x-show="form.jenis_zakat === 'fitrah'" class="space-y-5">
                            <div>
                                <label class="fld-label" for="jumlah_jiwa">Jumlah Jiwa (Tanggungan)</label>
                                <div class="relative">
                                    <input id="jumlah_jiwa" type="number" name="jumlah_jiwa" x-model.number="form.jumlah_jiwa"
                                           @input="hitungZakat()" min="1" placeholder="1" inputmode="numeric"
                                           class="fld fld-lg num pl-4 pr-16">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">jiwa</span>
                                </div>
                                @error('jumlah_jiwa')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                                <div class="flex flex-wrap gap-2 mt-2.5">
                                    <template x-for="n in [1,2,3,4,5,6]" :key="n">
                                        <button type="button" @click="form.jumlah_jiwa = n; hitungZakat()"
                                                class="chip border transition-colors"
                                                :class="form.jumlah_jiwa == n ? 'bg-brand-600 text-white border-brand-600' : 'bg-white text-gray-600 border-gray-200 hover:border-brand-300'"
                                                x-text="n + ' jiwa'"></button>
                                    </template>
                                </div>
                            </div>
                            <div>
                                <label class="fld-label" for="harga_beras">Harga Beras per Kg</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="harga_beras" type="number" name="harga_beras" x-model.number="form.harga_beras"
                                           @input="hitungZakat()" placeholder="{{ $hargaBeras ?? 14000 }}" inputmode="numeric"
                                           class="fld fld-lg num pl-12 pr-4">
                                </div>
                                <p class="text-xs text-brand-600 font-semibold mt-1.5">Referensi harga saat ini: Rp {{ number_format($hargaBeras ?? 14000, 0, ',', '.') }}/kg</p>
                            </div>
                            <div class="flex items-start gap-2.5 bg-amber-50 border border-amber-100 rounded-2xl p-4">
                                <span class="text-base leading-none mt-0.5">📐</span>
                                <p class="text-xs text-amber-800 leading-relaxed">
                                    <strong>Ketentuan:</strong> 2,5 kg beras per jiwa. Perhitungan Anda:
                                    <span class="font-bold block mt-1 num" x-text="form.jumlah_jiwa + ' jiwa × 2,5 kg × Rp ' + formatRupiah(form.harga_beras)"></span>
                                </p>
                            </div>
                        </div>

                        {{-- ===== MAL ===== --}}
                        <div x-show="form.jenis_zakat === 'mal'" class="space-y-5">
                            <div>
                                <label class="fld-label" for="total_harta">Total Harta yang Dimiliki (selama 1 tahun)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="total_harta" type="number" name="total_harta" x-model.number="form.total_harta"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                                @error('total_harta')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="fld-label" for="total_utang">Total Utang / Kewajiban <span class="text-gray-400 font-medium">(opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="total_utang" type="number" name="total_utang" x-model.number="form.total_utang"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                                <span class="text-base leading-none mt-0.5">ℹ️</span>
                                <p class="text-xs text-blue-800 leading-relaxed"><strong>Nisab:</strong> setara 85 gram emas. Kadar zakat <strong>2,5%</strong> dari (Total Harta &minus; Utang).</p>
                            </div>
                        </div>

                        {{-- ===== PROFESI ===== --}}
                        <div x-show="form.jenis_zakat === 'profesi'" class="space-y-5">
                            <div>
                                <label class="fld-label" for="penghasilan_bruto">Penghasilan Bruto per Bulan</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="penghasilan_bruto" type="number" name="penghasilan_bruto" x-model.number="form.penghasilan_bruto"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                                @error('penghasilan_bruto')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                            </div>
                            <div>
                                <label class="fld-label" for="pengeluaran_pokok">Pengeluaran Kebutuhan Pokok <span class="text-gray-400 font-medium">(opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="pengeluaran_pokok" type="number" name="pengeluaran_pokok" x-model.number="form.pengeluaran_pokok"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                                <span class="text-base leading-none mt-0.5">ℹ️</span>
                                <p class="text-xs text-blue-800 leading-relaxed">Kadar <strong>2,5%</strong> dari penghasilan bersih (bruto &minus; kebutuhan pokok).</p>
                            </div>
                        </div>

                        {{-- ===== PERNIAGAAN ===== --}}
                        <div x-show="form.jenis_zakat === 'perniagaan'" class="space-y-5">
                            <div>
                                <label class="fld-label" for="nilai_aset">Nilai Aset Perdagangan</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="nilai_aset" type="number" name="nilai_aset" x-model.number="form.nilai_aset"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div>
                                <label class="fld-label" for="piutang">Piutang yang Dapat Dicairkan</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="piutang" type="number" name="piutang" x-model.number="form.piutang"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div>
                                <label class="fld-label" for="utang_jatuh_tempo">Utang Jatuh Tempo</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="utang_jatuh_tempo" type="number" name="utang_jatuh_tempo" x-model.number="form.utang_jatuh_tempo"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 bg-blue-50 border border-blue-100 rounded-2xl p-4">
                                <span class="text-base leading-none mt-0.5">ℹ️</span>
                                <p class="text-xs text-blue-800 leading-relaxed">Kadar <strong>2,5%</strong> dari (aset + piutang &minus; utang jatuh tempo).</p>
                            </div>
                        </div>

                        {{-- ===== PERTANIAN ===== --}}
                        <div x-show="form.jenis_zakat === 'pertanian'" class="space-y-5">
                            <div>
                                <label class="fld-label" for="hasil_panen">Hasil Panen (dalam Rupiah)</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="hasil_panen" type="number" name="hasil_panen" x-model.number="form.hasil_panen"
                                           @input="hitungZakat()" placeholder="0" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div>
                                <label class="fld-label">Cara Pengairan Lahan</label>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                    <label class="flex items-center gap-3 cursor-pointer rounded-2xl border-2 p-4 transition-all"
                                           :class="form.irigasi === 'tadah_hujan' ? 'border-brand-500 bg-brand-50' : 'border-gray-200 bg-white hover:border-brand-200'">
                                        <input type="radio" name="irigasi" value="tadah_hujan" x-model="form.irigasi" @change="hitungZakat()" class="w-4 h-4 accent-emerald-600">
                                        <span class="min-w-0">
                                            <span class="block text-sm font-bold text-gray-800">Tadah Hujan</span>
                                            <span class="block text-xs text-gray-500">Kadar 10%</span>
                                        </span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer rounded-2xl border-2 p-4 transition-all"
                                           :class="form.irigasi === 'irigasi' ? 'border-brand-500 bg-brand-50' : 'border-gray-200 bg-white hover:border-brand-200'">
                                        <input type="radio" name="irigasi" value="irigasi" x-model="form.irigasi" @change="hitungZakat()" class="w-4 h-4 accent-emerald-600">
                                        <span class="min-w-0">
                                            <span class="block text-sm font-bold text-gray-800">Dengan Irigasi</span>
                                            <span class="block text-xs text-gray-500">Kadar 5%</span>
                                        </span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- ===== EMAS & PERAK ===== --}}
                        <div x-show="form.jenis_zakat === 'emas_perak'" class="space-y-5">
                            <div>
                                <label class="fld-label" for="berat_emas">Berat Emas (gram)</label>
                                <div class="relative">
                                    <input id="berat_emas" type="number" name="berat_emas" x-model.number="form.berat_emas"
                                           @input="hitungZakat()" placeholder="0" step="0.01" inputmode="decimal"
                                           class="fld fld-lg num pl-4 pr-16">
                                    <span class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold text-sm">gram</span>
                                </div>
                            </div>
                            <div>
                                <label class="fld-label" for="harga_emas_per_gram">Harga Emas per Gram</label>
                                <div class="relative">
                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 font-bold">Rp</span>
                                    <input id="harga_emas_per_gram" type="number" name="harga_emas_per_gram" x-model.number="form.harga_emas"
                                           @input="hitungZakat()" placeholder="1000000" inputmode="numeric" class="fld fld-lg num pl-12 pr-4">
                                </div>
                            </div>
                            <div class="flex items-start gap-2.5 bg-amber-50 border border-amber-100 rounded-2xl p-4">
                                <span class="text-base leading-none mt-0.5">🥇</span>
                                <p class="text-xs text-amber-800 leading-relaxed"><strong>Nisab emas:</strong> 85 gram. Kadar zakat <strong>2,5%</strong> dari nilai emas.</p>
                            </div>
                        </div>

                        {{-- Jumlah zakat --}}
                        <div class="pt-5 border-t border-gray-100">
                            <div class="flex flex-wrap items-center justify-between gap-2 mb-2.5">
                                <label class="fld-label !mb-0" for="jumlah_zakat">Jumlah Zakat yang Dibayarkan</label>
                                <button type="button" @click="hitungViaAjax()" :disabled="isCalculating"
                                        class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-700 bg-brand-50 hover:bg-brand-100 border border-brand-100 px-3 py-2 rounded-xl transition-colors disabled:opacity-60">
                                    <svg x-show="isCalculating" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                    <span x-text="isCalculating ? 'Menghitung...' : '🧮 Hitung ulang otomatis'"></span>
                                </button>
                            </div>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-extrabold text-lg">Rp</span>
                                <input id="jumlah_zakat" type="number" name="jumlah_zakat" x-model.number="form.jumlah_zakat"
                                       placeholder="10000" required min="10000" max="1000000000" inputmode="numeric"
                                       class="fld fld-lg num pl-14 pr-4 font-extrabold"
                                       :class="{'fld-error': form.jumlah_zakat < 10000 || form.jumlah_zakat > 1000000000}">
                            </div>
                            <div class="flex flex-wrap justify-between items-start gap-1 mt-2 text-xs">
                                <p class="text-gray-400">Nominal hasil hitung boleh Anda sesuaikan manual.</p>
                                <p class="font-bold num" :class="form.jumlah_zakat < 10000 || form.jumlah_zakat > 1000000000 ? 'text-red-500' : 'text-brand-600'">
                                    Min Rp 10.000 &middot; Max Rp 1 Miliar
                                </p>
                            </div>
                            <p x-show="form.jumlah_zakat < 10000 && form.jumlah_zakat > 0" x-cloak class="text-xs text-red-600 font-bold mt-1.5">Nominal terlalu kecil — minimal Rp 10.000.</p>
                            <p x-show="form.jumlah_zakat > 1000000000" x-cloak class="text-xs text-red-600 font-bold mt-1.5">Nominal terlalu besar — maksimal Rp 1.000.000.000.</p>
                            @error('jumlah_zakat')<p class="text-xs text-red-600 font-semibold mt-1.5">{{ $message }}</p>@enderror
                        </div>

                        {{-- Catatan --}}
                        <div>
                            <label class="fld-label" for="catatan">Catatan <span class="text-gray-400 font-medium">(opsional)</span></label>
                            <textarea id="catatan" name="catatan" x-model="form.catatan" rows="3" placeholder="Tulis catatan jika ada..."
                                      class="fld resize-none"></textarea>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row gap-3 mt-7">
                        <button type="button" @click="prevStep()" class="btn btn-ghost sm:w-auto w-full">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                            Kembali
                        </button>
                        <button type="button" @click="nextStep()"
                                :disabled="!form.jumlah_zakat || form.jumlah_zakat < 10000 || form.jumlah_zakat > 1000000000"
                                class="btn btn-primary flex-1">
                            Tinjau &amp; konfirmasi
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                        </button>
                    </div>
                </div>

                {{-- Ringkasan live --}}
                <div class="lg:col-span-2">
                    <div class="lg:sticky lg:top-6 space-y-4">
                        <div class="relative overflow-hidden bg-gradient-to-br from-brand-700 via-brand-700 to-brand-500 hero-pattern rounded-3xl p-6 text-white shadow-glow">
                            <div class="absolute inset-0 amt-shimmer pointer-events-none"></div>
                            <div class="relative">
                                <div class="flex items-center gap-2 text-brand-100 text-xs font-bold uppercase tracking-wider">
                                    <span x-text="selectedJenisData?.icon"></span>
                                    <span>Estimasi Zakat Anda</span>
                                </div>
                                <p class="num text-3xl sm:text-4xl font-extrabold tracking-tight mt-3" x-text="'Rp ' + formatRupiah(form.jumlah_zakat)"></p>
                                <p class="text-brand-200 text-xs mt-2 leading-relaxed" x-text="selectedJenisData?.kadar + ' — dihitung otomatis dari data yang Anda isi.'"></p>
                            </div>
                        </div>

                        <div class="bg-white rounded-3xl shadow-soft border border-gray-100 p-5">
                            <p class="text-xs font-extrabold text-gray-900 uppercase tracking-wider mb-3">Rincian Input</p>
                            <dl class="space-y-2.5 text-sm">
                                <div class="flex items-start justify-between gap-3">
                                    <dt class="text-gray-500">Jenis</dt>
                                    <dd class="font-bold text-gray-800 text-right" x-text="'Zakat ' + (selectedJenisData?.label || '-')"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'fitrah'">
                                    <dt class="text-gray-500">Tanggungan</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="form.jumlah_jiwa + ' jiwa'"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'fitrah'">
                                    <dt class="text-gray-500">Harga beras</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.harga_beras) + '/kg'"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'mal'">
                                    <dt class="text-gray-500">Total harta</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.total_harta)"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'mal'">
                                    <dt class="text-gray-500">Utang</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.total_utang)"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'profesi'">
                                    <dt class="text-gray-500">Penghasilan</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.penghasilan_bruto)"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'perniagaan'">
                                    <dt class="text-gray-500">Aset usaha</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.nilai_aset)"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'pertanian'">
                                    <dt class="text-gray-500">Hasil panen</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.hasil_panen)"></dd>
                                </div>
                                <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'emas_perak'">
                                    <dt class="text-gray-500">Emas</dt>
                                    <dd class="font-bold text-gray-800 num" x-text="form.berat_emas + ' gram'"></dd>
                                </div>
                                <div class="pt-3 mt-1 border-t border-dashed border-gray-200 flex items-center justify-between">
                                    <dt class="text-gray-700 font-bold">Total bayar</dt>
                                    <dd class="text-brand-700 font-extrabold num" x-text="'Rp ' + formatRupiah(form.jumlah_zakat)"></dd>
                                </div>
                            </dl>
                        </div>

                        <div class="flex items-start gap-2.5 bg-gray-50 border border-gray-100 rounded-2xl p-4">
                            <svg class="w-4 h-4 text-brand-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/></svg>
                            <p class="text-xs text-gray-500 leading-relaxed">Angka ini bersifat estimasi. Amil akan memverifikasi transaksi Anda maksimal 1&times;24 jam.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══════════════ LANGKAH 3: KONFIRMASI ══════════════ --}}
        <div x-show="step === 3" x-transition class="pane-enter">
            <div class="max-w-2xl mx-auto space-y-4">
                <div class="bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
                    <div class="relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-500 hero-pattern p-6 sm:p-8 text-center text-white">
                        <div class="relative">
                            <div class="w-14 h-14 bg-white/15 border border-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center mx-auto mb-3 text-2xl" x-text="selectedJenisData?.icon"></div>
                            <h2 class="text-lg sm:text-xl font-extrabold">Konfirmasi Pembayaran Zakat</h2>
                            <p class="text-brand-200 text-sm mt-1">Periksa kembali data sebelum Anda kirim.</p>
                        </div>
                    </div>

                    <div class="p-5 sm:p-7">
                        <dl class="space-y-3 bg-gray-50 rounded-2xl p-5 mb-5">
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-sm text-gray-500">Nama Muzakki</dt>
                                <dd class="text-sm font-bold text-gray-800 text-right" x-text="form.nama_muzakki"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-sm text-gray-500">Jenis Zakat</dt>
                                <dd class="text-sm font-bold text-gray-800 text-right">
                                    <span x-text="selectedJenisData?.icon"></span> <span x-text="'Zakat ' + selectedJenisData?.label"></span>
                                </dd>
                            </div>
                            <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'fitrah'">
                                <dt class="text-sm text-gray-500">Jumlah Jiwa</dt>
                                <dd class="text-sm font-bold text-gray-800 num" x-text="form.jumlah_jiwa + ' jiwa'"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3" x-show="form.jenis_zakat === 'profesi'">
                                <dt class="text-sm text-gray-500">Penghasilan/Bulan</dt>
                                <dd class="text-sm font-bold text-gray-800 num" x-text="'Rp ' + formatRupiah(form.penghasilan_bruto)"></dd>
                            </div>
                            <div class="flex items-start justify-between gap-3">
                                <dt class="text-sm text-gray-500">Tanggal</dt>
                                <dd class="text-sm font-bold text-gray-800">{{ now()->isoFormat('D MMMM Y') }}</dd>
                            </div>
                            <div class="flex items-start justify-between gap-3" x-show="form.catatan">
                                <dt class="text-sm text-gray-500">Catatan</dt>
                                <dd class="text-sm font-bold text-gray-800 text-right max-w-[60%]" x-text="form.catatan"></dd>
                            </div>
                        </dl>

                        <div class="relative overflow-hidden bg-gradient-to-br from-brand-50 to-brand-100 border border-brand-200 rounded-2xl p-5 sm:p-6 mb-5">
                            <div class="flex items-center justify-between gap-4">
                                <div class="min-w-0">
                                    <p class="text-xs font-bold text-brand-700 uppercase tracking-wider">Total Zakat Dibayarkan</p>
                                    <p class="num text-3xl sm:text-4xl font-extrabold text-brand-800 tracking-tight mt-1.5" x-text="'Rp ' + formatRupiah(form.jumlah_zakat)"></p>
                                </div>
                                <div class="w-14 h-14 bg-brand-600 rounded-2xl flex items-center justify-center flex-shrink-0 shadow-glow">
                                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                            </div>
                        </div>

                        <label class="flex items-start gap-3 cursor-pointer bg-amber-50 border border-amber-100 rounded-2xl p-4 mb-5">
                            <input type="checkbox" x-model="setuju" class="w-[18px] h-[18px] rounded-md accent-emerald-600 mt-0.5 flex-shrink-0 cursor-pointer">
                            <span class="text-sm text-amber-800 leading-relaxed">Saya menyatakan data ini benar dan saya ikhlas menunaikan zakat tersebut.</span>
                        </label>

                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="button" @click="prevStep()" class="btn btn-ghost w-full sm:w-auto">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
                                Ubah data
                            </button>
                            <button type="submit" :disabled="!setuju || isSubmitting" class="btn btn-primary flex-1 !py-4 text-base">
                                <svg x-show="isSubmitting" x-cloak class="w-5 h-5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                                <svg x-show="!isSubmitting" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <span x-text="isSubmitting ? 'Memproses...' : 'Kirim &amp; Bayar Zakat'">Kirim &amp; Bayar Zakat</span>
                            </button>
                        </div>
                    </div>
                </div>

                <p class="text-center text-xs text-gray-400">Setelah dikirim, Anda akan diarahkan ke halaman pembayaran.</p>
            </div>
        </div>

    </form>
</div>
@endsection

@push('scripts')
<script>
function zakatForm() {
    return {
        step: 1,
        setuju: false,
        isCalculating: false,
        isSubmitting: false,
        steps: ['Pilih Jenis', 'Isi Data', 'Konfirmasi'],
        form: {
            jenis_zakat: '{!! old("jenis_zakat") !!}' || new URLSearchParams(window.location.search).get('jenis') || '',
            nama_muzakki: '{!! old("nama_muzakki", auth()->user()->name ?? "") !!}',
            jumlah_jiwa: {{ old("jumlah_jiwa", 1) }},
            harga_beras: {{ old("harga_beras", $hargaBeras ?? 14000) }},
            total_harta: {{ old("total_harta", 0) }},
            total_utang: {{ old("total_utang", 0) }},
            penghasilan_bruto: {{ old("penghasilan_bruto", 0) }},
            pengeluaran_pokok: {{ old("pengeluaran_pokok", 0) }},
            nilai_aset: {{ old("nilai_aset", 0) }},
            piutang: {{ old("piutang", 0) }},
            utang_jatuh_tempo: {{ old("utang_jatuh_tempo", 0) }},
            hasil_panen: {{ old("hasil_panen", 0) }},
            irigasi: '{!! old("irigasi", "irigasi") !!}',
            berat_emas: {{ old("berat_emas", 0) }},
            harga_emas: {{ old("harga_emas", 1000000) }},
            jumlah_zakat: parseFloat('{!! old("jumlah_zakat") !!}') || parseFloat(new URLSearchParams(window.location.search).get('jumlah')) || 0,
            catatan: '{!! old("catatan", "") !!}',
        },
        jenisZakatOptions: [
            { value: 'fitrah', label: 'Fitrah', icon: '🌾', kadar: '2,5 kg beras / jiwa', deskripsi: 'Wajib sebelum Idul Fitri untuk setiap jiwa.', info: 'Dihitung dari jumlah tanggungan × harga beras.' },
            { value: 'mal', label: 'Mal', icon: '💰', kadar: '2,5% dari harta', deskripsi: 'Harta yang mencapai nisab 85gr emas.', info: 'Kadar 2,5% dari (harta − utang).' },
            { value: 'profesi', label: 'Profesi', icon: '💼', kadar: '2,5% penghasilan', deskripsi: 'Gaji, honorarium, dan pendapatan lain.', info: 'Kadar 2,5% dari penghasilan bersih.' },
            { value: 'perniagaan', label: 'Perniagaan', icon: '🏪', kadar: '2,5% aset usaha', deskripsi: 'Usaha dagang yang mencapai nisab.', info: 'Aset + piutang − utang jatuh tempo.' },
            { value: 'pertanian', label: 'Pertanian', icon: '🌱', kadar: '5% atau 10% panen', deskripsi: 'Hasil bumi sesuai cara pengairan.', info: '5% beririgasi, 10% tadah hujan.' },
            { value: 'emas_perak', label: 'Emas & Perak', icon: '🥇', kadar: '2,5% dari nilai', deskripsi: 'Nisab 85gr emas atau 595gr perak.', info: 'Kadar 2,5% dari nilai emas.' },
        ],

        get selectedJenisData() {
            return this.jenisZakatOptions.find(j => j.value === this.form.jenis_zakat);
        },

        init() {
            if (this.form.jenis_zakat === 'emas') this.form.jenis_zakat = 'emas_perak';
            if (this.form.jenis_zakat) this.step = 2;
            if (this.form.jumlah_zakat > 0 && this.form.jenis_zakat) this.step = 2;
            if (this.form.jenis_zakat && !this.form.jumlah_zakat) this.hitungZakat();
        },

        selectJenis(jenis) {
            this.form.jenis_zakat = jenis;
            this.hitungZakat();
        },

        nextStep() {
            if (this.step < 3) this.step++;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        prevStep() {
            if (this.step > 1) this.step--;
            window.scrollTo({ top: 0, behavior: 'smooth' });
        },

        hitungZakat() {
            clearTimeout(this._debounce);
            this._debounce = setTimeout(() => { this.hitungViaAjax(); }, 700);
        },

        async hitungViaAjax() {
            this.isCalculating = true;
            try {
                const res = await fetch('{{ route('user.donasi.hitung') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify({ jenis_zakat: this.form.jenis_zakat, ...this.form })
                });
                if (res.ok) {
                    const data = await res.json();
                    if (data.success && data.jumlah !== undefined) {
                        this.form.jumlah_zakat = Math.round(data.jumlah);
                    }
                } else {
                    this.form.jumlah_zakat = Math.round(this.hitungLokal());
                }
            } catch {
                this.form.jumlah_zakat = Math.round(this.hitungLokal());
            } finally {
                this.isCalculating = false;
            }
        },

        hitungLokal() {
            const f = this.form;
            switch(f.jenis_zakat) {
                case 'fitrah': return (f.jumlah_jiwa||1) * 2.5 * (f.harga_beras||14000);
                case 'mal': return Math.max(0, (f.total_harta||0) - (f.total_utang||0)) * 0.025;
                case 'profesi': return Math.max(0, (f.penghasilan_bruto||0) - (f.pengeluaran_pokok||0)) * 0.025;
                case 'perniagaan': return Math.max(0, (f.nilai_aset||0) + (f.piutang||0) - (f.utang_jatuh_tempo||0)) * 0.025;
                case 'pertanian': return (f.hasil_panen||0) * (f.irigasi === 'tadah_hujan' ? 0.1 : 0.05);
                case 'emas_perak': return (f.berat_emas||0) * (f.harga_emas||0) * 0.025;
                default: return 0;
            }
        },

        async submitForm() {
            this.isSubmitting = true;
            document.getElementById('zakatForm').submit();
        },

        formatRupiah(val) {
            if (!val) return '0';
            return Number(Math.round(val)).toLocaleString('id-ID');
        }
    }
}
</script>
@endpush
