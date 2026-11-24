<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistem Zakat Online')</title>
    @include('layouts.partials.theme')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body class="h-full bg-gray-50 text-gray-800 antialiased">

    <div class="min-h-screen flex flex-col lg:flex-row">

        {{-- ══════════ PANEL BRAND ══════════ --}}
        <aside class="relative overflow-hidden bg-gradient-to-br from-brand-900 via-brand-800 to-brand-600 hero-pattern px-5 py-5 sm:px-8 sm:py-7 lg:p-14 lg:w-[46%] lg:flex-shrink-0 lg:flex lg:flex-col lg:justify-between">
            <div class="absolute -top-24 -right-24 w-80 h-80 bg-brand-400/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-20 w-80 h-80 bg-teal-300/10 rounded-full blur-3xl pointer-events-none"></div>

            {{-- Logo --}}
            <div class="relative flex items-center gap-3">
                <div class="w-11 h-11 rounded-2xl bg-white/15 border border-white/20 backdrop-blur-sm flex items-center justify-center flex-shrink-0">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                    </svg>
                </div>
                <div class="leading-tight">
                    <h1 class="text-white font-extrabold text-lg">Zakat<span class="text-brand-300">Online</span></h1>
                    <p class="text-brand-200/80 text-[10px] sm:text-[11px] tracking-widest uppercase font-medium">Sistem Manajemen Zakat Digital</p>
                </div>
                <a href="{{ url('/') }}" class="ml-auto hidden lg:inline-flex items-center gap-1.5 text-xs font-bold text-brand-100/90 hover:text-white bg-white/10 border border-white/15 px-3 py-1.5 rounded-full transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Beranda
                </a>
            </div>

            {{-- Kutipan + statistik --}}
            <div class="relative hidden lg:block lg:mt-0">
                <div class="bg-white/10 backdrop-blur-md border border-white/15 rounded-3xl px-6 py-6 max-w-md mx-auto lg:mx-0">
                    <p class="font-arabic text-white text-xl lg:text-2xl text-center leading-loose">خُذْ مِنْ أَمْوَالِهِمْ صَدَقَةً تُطَهِّرُهُمْ</p>
                    <p class="text-brand-100/90 text-sm text-center leading-relaxed mt-2">"Ambillah zakat dari sebagian harta mereka, dengan zakat itu kamu membersihkan mereka..."</p>
                    <p class="text-brand-300 text-[11px] text-center font-bold tracking-widest mt-2">— QS. AT-TAUBAH: 103 —</p>
                </div>

                <div class="grid grid-cols-3 gap-3 max-w-md mx-auto lg:mx-0 mt-6">
                    <div class="bg-white/10 border border-white/12 rounded-2xl px-3 py-4 text-center backdrop-blur-sm hover:bg-white/15 transition-colors">
                        <p class="num text-white text-xl lg:text-2xl font-extrabold">2,5<span class="text-sm">%</span></p>
                        <p class="text-brand-200 text-[11px] font-semibold mt-1">Kadar Zakat Maal</p>
                    </div>
                    <div class="bg-white/10 border border-white/12 rounded-2xl px-3 py-4 text-center backdrop-blur-sm hover:bg-white/15 transition-colors">
                        <p class="num text-white text-xl lg:text-2xl font-extrabold">2,5<span class="text-sm">kg</span></p>
                        <p class="text-brand-200 text-[11px] font-semibold mt-1">Fitrah per Jiwa</p>
                    </div>
                    <div class="bg-white/10 border border-white/12 rounded-2xl px-3 py-4 text-center backdrop-blur-sm hover:bg-white/15 transition-colors">
                        <p class="num text-white text-xl lg:text-2xl font-extrabold">85<span class="text-sm">gr</span></p>
                        <p class="text-brand-200 text-[11px] font-semibold mt-1">Nisab Emas</p>
                    </div>
                </div>

                <div class="flex flex-wrap items-center justify-center lg:justify-start gap-2 max-w-md mx-auto lg:mx-0 mt-6">
                    <span class="chip bg-white/10 text-brand-100 border border-white/15">🔒 Aman & Terpercaya</span>
                    <span class="chip bg-white/10 text-brand-100 border border-white/15">⚡ Proses Cepat</span>
                    <span class="chip bg-white/10 text-brand-100 border border-white/15">🕌 Sesuai Syariah</span>
                </div>
            </div>

            {{-- Kaki panel --}}
            <div class="relative mt-4 lg:mt-0 lg:pt-4 lg:border-t lg:border-white/10 flex items-center justify-between gap-3">
                <p class="text-brand-200/70 text-[11px] sm:text-xs">&copy; {{ date('Y') }} Zakat Online</p>
                <span class="flex items-center gap-2 text-brand-100/90 text-[11px] sm:text-xs font-semibold">
                    <span class="w-2 h-2 bg-brand-300 rounded-full animate-pulse flex-shrink-0"></span>
                    <span class="lg:hidden">Aman &middot; Sesuai Syariah</span>
                    <span class="hidden lg:inline">Sistem Aktif</span>
                </span>
            </div>
        </aside>

        {{-- ══════════ PANEL FORM ══════════ --}}
        <main class="flex-1 flex items-center justify-center px-4 sm:px-6 py-8 sm:py-12">
            <div class="w-full max-w-md fade-up">
                @if(session('success'))
                    <div class="flex items-start gap-3 mb-5 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3.5">
                        <svg class="w-5 h-5 text-emerald-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        <p class="text-sm text-emerald-800 font-semibold">{{ session('success') }}</p>
                    </div>
                @endif

                @if(session('error'))
                    <div class="flex items-start gap-3 mb-5 rounded-2xl border border-red-200 bg-red-50 px-4 py-3.5">
                        <svg class="w-5 h-5 text-red-600 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                        <p class="text-sm text-red-700 font-semibold">{{ session('error') }}</p>
                    </div>
                @endif

                <div class="bg-white rounded-4xl shadow-soft border border-gray-100 p-5 sm:p-8">
                    @yield('content')
                </div>

                <p class="text-center text-xs text-gray-400 mt-8">
                    <a href="{{ url('/') }}" class="hover:text-brand-600 font-semibold transition-colors">&larr; Kembali ke Beranda</a>
                </p>
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
