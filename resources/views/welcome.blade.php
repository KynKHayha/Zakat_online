@extends('layouts.public')

@section('title', 'Zakat Online — Tunaikan Zakat dengan Mudah & Amanah')

@section('content')

{{-- ══════════════════════ HERO SECTION ══════════════════════ --}}
<section class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 hero-pattern">
    {{-- Decorative blobs --}}
    <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand-400/20 rounded-full blur-3xl"></div>
    <div class="absolute -bottom-32 -left-24 w-[28rem] h-[28rem] bg-teal-400/10 rounded-full blur-3xl"></div>

    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-32 pb-20 md:pt-40 md:pb-28">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- Left: Copywriting --}}
            <div class="text-center lg:text-left">
                <span class="inline-flex items-center gap-2 bg-white/10 border border-white/20 backdrop-blur-sm text-brand-100 text-xs font-semibold px-4 py-2 rounded-full mb-6">
                    <span class="w-2 h-2 bg-brand-300 rounded-full animate-pulse"></span>
                    Platform Zakat Digital Terpercaya
                </span>

                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-[1.15] tracking-tight">
                    Tunaikan Zakat,<br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-brand-300 via-teal-200 to-brand-200">Sucikan Harta,</span><br>
                    Bahagiakan Umat.
                </h1>

                <p class="mt-6 text-base sm:text-lg text-brand-100/90 leading-relaxed max-w-xl mx-auto lg:mx-0">
                    Hitung kewajiban zakat Anda dalam hitungan detik, bayar dengan metode favorit, dan pantau penyalurannya secara transparan — semua dalam satu genggaman.
                </p>

                <div class="mt-8 flex flex-col sm:flex-row items-center justify-center lg:justify-start gap-4">
                    <a href="{{ auth()->check() ? route('user.donasi.create') : route('register') }}"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-brand-800 font-extrabold px-8 py-4 rounded-2xl shadow-2xl shadow-brand-900/40 hover:shadow-brand-900/60 hover:-translate-y-1 transition-all text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                        Tunaikan Zakat
                    </a>
                    <a href="#kalkulator"
                       class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white/10 hover:bg-white/20 border border-white/25 backdrop-blur-sm text-white font-bold px-8 py-4 rounded-2xl transition-all text-base">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 7h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1z"/></svg>
                        Hitung Dulu
                    </a>
                </div>

                {{-- Trust indicators --}}
                <div class="mt-10 flex flex-wrap items-center justify-center lg:justify-start gap-x-6 gap-y-3 text-brand-100/80 text-xs font-medium">
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M2.166 4.999A11.954 11.954 0 0010 1.944 11.954 11.954 0 0017.834 5c.11.65.166 1.32.166 2.001 0 5.225-3.34 9.67-8 11.317C5.34 16.67 2 12.225 2 7c0-.682.057-1.35.166-2.001zm11.541 3.708a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> 100% Amanah</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg> Sesuai Syariah</span>
                    <span class="flex items-center gap-1.5"><svg class="w-4 h-4 text-brand-300" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/></svg> Verifikasi 1×24 Jam</span>
                </div>
            </div>

            {{-- Right: Visual Card --}}
            <div class="relative hidden lg:block">
                <div class="float-slow bg-white/10 backdrop-blur-xl border border-white/20 rounded-3xl p-8 shadow-2xl max-w-md ml-auto">
                    <p class="font-arabic text-white text-2xl text-center leading-loose mb-3">خُذْ مِنْ أَمْوَالِهِمْ صَدَقَةً تُطَهِّرُهُمْ وَتُزَكِّيهِمْ بِهَا</p>
                    <p class="text-brand-100 text-sm text-center leading-relaxed">"Ambillah zakat dari harta mereka, guna membersihkan dan menyucikan mereka..."</p>
                    <p class="text-brand-300 text-xs text-center mt-3 font-bold tracking-widest">— QS. AT-TAUBAH: 103 —</p>
                </div>
                <div class="float-slower absolute -bottom-10 -left-4 bg-white rounded-2xl shadow-2xl p-5 flex items-center gap-4">
                    <div class="w-12 h-12 bg-brand-100 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 font-medium">Zakat Tersalurkan</p>
                        <p class="text-lg font-extrabold text-gray-900">100% Transparan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Wave divider --}}
    <svg class="block w-full text-gray-50" viewBox="0 0 1440 80" fill="currentColor" preserveAspectRatio="none">
        <path d="M0,40 C360,90 1080,-10 1440,40 L1440,80 L0,80 Z"></path>
    </svg>
</section>

{{-- ══════════════════════ STATS BAR ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-2 mb-4">
    <div class="reveal bg-white rounded-3xl shadow-soft border border-gray-100 grid grid-cols-2 md:grid-cols-4 divide-x divide-gray-100 overflow-hidden">
        <div class="p-6 text-center">
            <p class="text-2xl md:text-3xl font-extrabold text-brand-600">2.5%</p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 font-medium">Kadar Zakat Maal</p>
        </div>
        <div class="p-6 text-center">
            <p class="text-2xl md:text-3xl font-extrabold text-brand-600">2.5<span class="text-base">kg</span></p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 font-medium">Fitrah per Jiwa</p>
        </div>
        <div class="p-6 text-center border-t md:border-t-0 border-gray-100">
            <p class="text-2xl md:text-3xl font-extrabold text-brand-600">85<span class="text-base">gr</span></p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 font-medium">Nisab Emas</p>
        </div>
        <div class="p-6 text-center border-t md:border-t-0 border-gray-100">
            <p class="text-2xl md:text-3xl font-extrabold text-brand-600">24<span class="text-base">/7</span></p>
            <p class="text-xs md:text-sm text-gray-500 mt-1 font-medium">Layanan Online</p>
        </div>
    </div>
</section>

{{-- ══════════════════════ FITUR / LAYANAN ══════════════════════ --}}
<section id="fitur" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    <div class="reveal text-center max-w-2xl mx-auto mb-12">
        <span class="inline-block bg-brand-100 text-brand-700 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest mb-4">Layanan Kami</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Semua Jenis Zakat, Satu Platform</h2>
        <p class="text-gray-500 mt-4 text-base md:text-lg">Pilih jenis zakat yang ingin Anda tunaikan — kami bantu hitung dan salurkan dengan amanah.</p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Zakat Fitrah --}}
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100">
            <div class="w-14 h-14 bg-gradient-to-br from-amber-100 to-amber-200 rounded-2xl flex items-center justify-center text-3xl mb-5">🌾</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Zakat Fitrah</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-4">Wajib bagi setiap jiwa menjelang Idul Fitri. Besarnya 2,5 kg beras (atau senilai uang) per jiwa.</p>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-700 bg-amber-50 px-3 py-1.5 rounded-full">2,5 kg beras / jiwa</span>
        </div>

        {{-- Zakat Maal --}}
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100">
            <div class="w-14 h-14 bg-gradient-to-br from-brand-100 to-brand-200 rounded-2xl flex items-center justify-center text-3xl mb-5">💰</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Zakat Maal</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-4">Zakat atas harta yang tersimpan setahun (haul) dan mencapai nisab 85 gram emas.</p>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-brand-700 bg-brand-50 px-3 py-1.5 rounded-full">2,5% dari harta</span>
        </div>

        {{-- Zakat Profesi --}}
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100">
            <div class="w-14 h-14 bg-gradient-to-br from-blue-100 to-blue-200 rounded-2xl flex items-center justify-center text-3xl mb-5">💼</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Zakat Profesi</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-4">Zakat dari penghasilan bulanan — gaji, honorarium, dan pendapatan profesi lainnya.</p>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-700 bg-blue-50 px-3 py-1.5 rounded-full">2,5% per bulan</span>
        </div>

        {{-- Zakat Perniagaan --}}
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100">
            <div class="w-14 h-14 bg-gradient-to-br from-purple-100 to-purple-200 rounded-2xl flex items-center justify-center text-3xl mb-5">🏪</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Zakat Perniagaan</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-4">Zakat atas aset usaha dagang: modal, laba, dan piutang yang mencapai nisab.</p>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-700 bg-purple-50 px-3 py-1.5 rounded-full">2,5% dari aset usaha</span>
        </div>

        {{-- Zakat Emas --}}
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100">
            <div class="w-14 h-14 bg-gradient-to-br from-yellow-100 to-yellow-200 rounded-2xl flex items-center justify-center text-3xl mb-5">🥇</div>
            <h3 class="text-xl font-extrabold text-gray-900 mb-2">Zakat Emas & Perak</h3>
            <p class="text-sm text-gray-500 leading-relaxed mb-4">Zakat atas kepemilikan emas ≥85 gram atau perak ≥595 gram selama satu tahun.</p>
            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-yellow-700 bg-yellow-50 px-3 py-1.5 rounded-full">2,5% dari nilai</span>
        </div>

        {{-- Sedekah --}}
        <div class="reveal card-hover bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-8 shadow-glow text-white">
            <div class="w-14 h-14 bg-white/20 rounded-2xl flex items-center justify-center text-3xl mb-5">❤️</div>
            <h3 class="text-xl font-extrabold mb-2">Sedekah</h3>
            <p class="text-sm text-brand-100 leading-relaxed mb-4">Salurkan kebaikan kapan pun. Sedekah tidak mengenal nisab dan waktu — sekecil apa pun berarti.</p>
            <a href="{{ auth()->check() ? route('user.donasi.create') : route('register') }}"
               class="inline-flex items-center gap-1.5 text-xs font-bold bg-white text-brand-700 px-4 py-2 rounded-full hover:bg-brand-50 transition-colors">
                Mulai Sedekah
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 12h12"/></svg>
            </a>
        </div>
    </div>
</section>

{{-- ══════════════════════ KALKULATOR ZAKAT (PUBLIK) ══════════════════════ --}}
<section id="kalkulator" class="bg-gradient-to-b from-gray-50 to-brand-50/60 py-16 md:py-24">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8" x-data="landingKalkulator()">
        <div class="reveal text-center max-w-2xl mx-auto mb-12">
            <span class="inline-block bg-brand-100 text-brand-700 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest mb-4">Kalkulator Zakat</span>
            <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Hitung Zakatmu dalam Hitungan Detik</h2>
            <p class="text-gray-500 mt-4 text-base md:text-lg">Tidak perlu ribet. Pilih jenis zakat, masukkan nominal, dan langsung ketahui kewajiban Anda.</p>
        </div>

        <div class="reveal bg-white rounded-3xl shadow-soft border border-gray-100 overflow-hidden">
            {{-- Tab jenis zakat --}}
            <div class="p-6 md:p-8 pb-0">
                <div class="grid grid-cols-3 gap-2 md:gap-3 bg-gray-100 rounded-2xl p-1.5">
                    <button @click="tab = 'fitrah'; hitung()"
                            class="py-3 px-2 rounded-xl text-xs md:text-sm font-bold transition-all"
                            :class="tab === 'fitrah' ? 'bg-white text-brand-700 shadow-md' : 'text-gray-500 hover:text-gray-700'">
                        🌾 Fitrah
                    </button>
                    <button @click="tab = 'maal'; hitung()"
                            class="py-3 px-2 rounded-xl text-xs md:text-sm font-bold transition-all"
                            :class="tab === 'maal' ? 'bg-white text-brand-700 shadow-md' : 'text-gray-500 hover:text-gray-700'">
                        💰 Maal
                    </button>
                    <button @click="tab = 'profesi'; hitung()"
                            class="py-3 px-2 rounded-xl text-xs md:text-sm font-bold transition-all"
                            :class="tab === 'profesi' ? 'bg-white text-brand-700 shadow-md' : 'text-gray-500 hover:text-gray-700'">
                        💼 Profesi
                    </button>
                </div>
            </div>

            <div class="p-6 md:p-8 grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Input --}}
                <div class="space-y-5">
                    {{-- Fitrah --}}
                    <template x-if="tab === 'fitrah'">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jumlah Jiwa dalam Tanggungan</label>
                                <div class="relative">
                                    <input type="number" min="1" x-model.number="jiwa" @input="hitung()"
                                           class="w-full border-2 border-gray-200 rounded-2xl px-5 py-4 text-xl font-bold text-gray-800 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 transition-all">
                                    <span class="absolute right-5 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">jiwa</span>
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Harga Beras per Kg</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">Rp</span>
                                    <input type="number" min="0" x-model.number="hargaBeras" @input="hitung()"
                                           class="w-full border-2 border-gray-200 rounded-2xl pl-14 pr-5 py-4 text-xl font-bold text-gray-800 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 transition-all">
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Ketentuan: 2,5 kg beras per jiwa</p>
                            </div>
                        </div>
                    </template>

                    {{-- Maal --}}
                    <template x-if="tab === 'maal'">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Total Harta Tersimpan (1 Tahun)</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">Rp</span>
                                    <input type="number" min="0" x-model.number="harta" @input="hitung()"
                                           class="w-full border-2 border-gray-200 rounded-2xl pl-14 pr-5 py-4 text-xl font-bold text-gray-800 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Utang / Kewajiban <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">Rp</span>
                                    <input type="number" min="0" x-model.number="utang" @input="hitung()"
                                           class="w-full border-2 border-gray-200 rounded-2xl pl-14 pr-5 py-4 text-xl font-bold text-gray-800 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 transition-all">
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Ketentuan: 2,5% jika harta ≥ nisab (85 gr emas)</p>
                            </div>
                        </div>
                    </template>

                    {{-- Profesi --}}
                    <template x-if="tab === 'profesi'">
                        <div class="space-y-5">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Penghasilan Bruto per Bulan</label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">Rp</span>
                                    <input type="number" min="0" x-model.number="gaji" @input="hitung()"
                                           class="w-full border-2 border-gray-200 rounded-2xl pl-14 pr-5 py-4 text-xl font-bold text-gray-800 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 transition-all">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kebutuhan Pokok <span class="text-gray-400 font-normal">(opsional)</span></label>
                                <div class="relative">
                                    <span class="absolute left-5 top-1/2 -translate-y-1/2 text-gray-400 font-semibold">Rp</span>
                                    <input type="number" min="0" x-model.number="kebutuhan" @input="hitung()"
                                           class="w-full border-2 border-gray-200 rounded-2xl pl-14 pr-5 py-4 text-xl font-bold text-gray-800 focus:outline-none focus:border-brand-500 focus:ring-4 focus:ring-brand-100 transition-all">
                                </div>
                                <p class="text-xs text-gray-400 mt-2">Ketentuan: 2,5% dari penghasilan bersih</p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Hasil --}}
                <div class="flex flex-col">
                    <div class="flex-1 bg-gradient-to-br from-brand-600 to-brand-800 rounded-3xl p-8 text-white flex flex-col justify-center relative overflow-hidden">
                        <div class="absolute -top-10 -right-10 w-40 h-40 bg-white/10 rounded-full"></div>
                        <div class="absolute -bottom-12 -left-8 w-32 h-32 bg-white/5 rounded-full"></div>
                        <p class="relative text-brand-200 text-sm font-semibold mb-2">Zakat yang Harus Ditunaikan</p>
                        <p class="relative text-4xl md:text-5xl font-extrabold tracking-tight" x-text="'Rp ' + formatRupiah(hasil)"></p>
                        <p class="relative text-brand-200 text-sm mt-3" x-text="keterangan"></p>

                        <div class="relative mt-8 pt-6 border-t border-white/20">
                            <a href="{{ auth()->check() ? route('user.donasi.create') : route('register') }}"
                               class="flex items-center justify-center gap-2 w-full bg-white text-brand-800 font-extrabold py-4 rounded-2xl hover:bg-brand-50 transition-all shadow-lg">
                                Tunaikan Sekarang
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5-5 5M6 12h12"/></svg>
                            </a>
                            <p class="text-center text-xs text-brand-200 mt-3">Login / daftar gratis untuk melanjutkan pembayaran</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══════════════════════ CARA KERJA ══════════════════════ --}}
<section id="tentang" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24">
    <div class="reveal text-center max-w-2xl mx-auto mb-12">
        <span class="inline-block bg-brand-100 text-brand-700 text-xs font-bold px-4 py-1.5 rounded-full uppercase tracking-widest mb-4">Cara Kerja</span>
        <h2 class="text-3xl md:text-4xl font-extrabold text-gray-900 tracking-tight">Semudah 1, 2, 3</h2>
        <p class="text-gray-500 mt-4 text-base md:text-lg">Proses pembayaran zakat yang simpel dan tidak membingungkan.</p>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-2xl flex items-center justify-center text-2xl font-extrabold mx-auto mb-5 shadow-glow">1</div>
            <h3 class="text-lg font-extrabold text-gray-900 mb-2">Daftar & Hitung</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Buat akun gratis, lalu gunakan kalkulator untuk mengetahui besaran zakat yang wajib ditunaikan.</p>
        </div>
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-2xl flex items-center justify-center text-2xl font-extrabold mx-auto mb-5 shadow-glow">2</div>
            <h3 class="text-lg font-extrabold text-gray-900 mb-2">Pilih Cara Bayar</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Transfer bank dengan upload bukti, atau bayar tunai langsung di kantor kami dengan tiket QR digital.</p>
        </div>
        <div class="reveal card-hover bg-white rounded-3xl p-8 shadow-soft border border-gray-100 text-center">
            <div class="w-16 h-16 bg-gradient-to-br from-brand-500 to-brand-700 text-white rounded-2xl flex items-center justify-center text-2xl font-extrabold mx-auto mb-5 shadow-glow">3</div>
            <h3 class="text-lg font-extrabold text-gray-900 mb-2">Pantau & Terima Laporan</h3>
            <p class="text-sm text-gray-500 leading-relaxed">Lacak status transaksi secara real-time dan unduh laporan zakat pribadi dalam format PDF.</p>
        </div>
    </div>
</section>

{{-- ══════════════════════ CTA FINAL ══════════════════════ --}}
<section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-20">
    <div class="reveal relative overflow-hidden bg-gradient-to-br from-brand-800 via-brand-700 to-brand-500 rounded-3xl hero-pattern px-6 py-16 md:px-16 text-center">
        <div class="absolute -top-20 -right-20 w-64 h-64 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute -bottom-24 -left-16 w-72 h-72 bg-teal-300/10 rounded-full blur-2xl"></div>

        <div class="relative">
            <p class="font-arabic text-2xl md:text-3xl text-brand-100 mb-4">وَمَا تُقَدِّمُوا لِأَنْفُسِكُمْ مِنْ خَيْرٍ تَجِدُوهُ عِنْدَ اللَّهِ</p>
            <h2 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight mb-4">Kebaikan yang Anda Tanam,<br class="hidden sm:block"> Akan Anda Tuai di Sisi Allah</h2>
            <p class="text-brand-100 max-w-xl mx-auto mb-8">Jangan tunda kewajiban. Mulai tunaikan zakat Anda hari ini juga — mudah, aman, dan amanah.</p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ auth()->check() ? route('user.donasi.create') : route('register') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white text-brand-800 font-extrabold px-10 py-4 rounded-2xl shadow-2xl hover:-translate-y-1 transition-all">
                    Tunaikan Zakat Sekarang
                </a>
                <a href="{{ route('login') }}"
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-white/10 border border-white/25 text-white font-bold px-10 py-4 rounded-2xl hover:bg-white/20 transition-all">
                    Sudah Punya Akun? Login
                </a>
            </div>
        </div>
    </div>
</section>

@endsection

@push('scripts')
<script>
function landingKalkulator() {
    return {
        tab: 'fitrah',
        jiwa: 1,
        hargaBeras: 14000,
        harta: 0,
        utang: 0,
        gaji: 0,
        kebutuhan: 0,
        hasil: 0,
        keterangan: '',

        init() { this.hitung(); },

        hitung() {
            if (this.tab === 'fitrah') {
                this.hasil = Math.max(0, (this.jiwa || 0) * 2.5 * (this.hargaBeras || 0));
                this.keterangan = (this.jiwa || 0) + ' jiwa × 2,5 kg × Rp ' + this.formatRupiah(this.hargaBeras || 0) + ' /kg';
            } else if (this.tab === 'maal') {
                const bersih = Math.max(0, (this.harta || 0) - (this.utang || 0));
                this.hasil = bersih * 0.025;
                this.keterangan = bersih > 0
                    ? '2,5% dari Rp ' + this.formatRupiah(bersih) + ' (harta bersih)'
                    : 'Masukkan total harta Anda untuk menghitung';
            } else {
                const bersih = Math.max(0, (this.gaji || 0) - (this.kebutuhan || 0));
                this.hasil = bersih * 0.025;
                this.keterangan = bersih > 0
                    ? '2,5% dari Rp ' + this.formatRupiah(bersih) + ' (penghasilan bersih)'
                    : 'Masukkan penghasilan Anda untuk menghitung';
            }
        },

        formatRupiah(val) {
            return Number(Math.round(val || 0)).toLocaleString('id-ID');
        }
    }
}
</script>
@endpush
