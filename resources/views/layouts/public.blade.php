<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Zakat Online — Tunaikan Zakat dengan Mudah & Amanah')</title>
    <meta name="description" content="Platform zakat digital terpercaya. Hitung, tunaikan, dan pantau zakat Anda dengan mudah, aman, dan sesuai syariah.">
    @include('layouts.partials.theme')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .nav-link { position: relative; transition: color .2s ease; }
        .nav-link::after {
            content: ''; position: absolute; left: 0; bottom: -4px;
            width: 0; height: 2px; border-radius: 2px;
            background: linear-gradient(90deg, #10b981, #059669);
            transition: width .25s ease;
        }
        .nav-link:hover::after, .nav-link.active::after { width: 100%; }
    </style>
    @stack('styles')
</head>
<body class="bg-gray-50 text-gray-800 antialiased" x-data="{ mobileMenu: false, scrolled: false }"
      @scroll.window="scrolled = window.scrollY > 24">

    {{-- ══════════════ STICKY NAVBAR ══════════════ --}}
    <header class="fixed top-0 inset-x-0 z-50 transition-all duration-300"
            :class="scrolled ? 'bg-white/90 backdrop-blur-lg shadow-soft' : 'bg-transparent'">
        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16 md:h-[72px]">

                {{-- Logo --}}
                <a href="{{ url('/') }}" class="flex items-center gap-2.5 group">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center shadow-glow group-hover:scale-105 transition-transform">
                        <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                        </svg>
                    </div>
                    <div class="leading-tight">
                        <span class="block font-extrabold text-base md:text-lg transition-colors"
                              :class="scrolled ? 'text-gray-900' : 'text-white'">Zakat<span class="text-brand-500">Online</span></span>
                        <span class="hidden sm:block text-[10px] font-medium tracking-widest uppercase transition-colors"
                              :class="scrolled ? 'text-gray-400' : 'text-brand-200'">Amanah & Terpercaya</span>
                    </div>
                </a>

                {{-- Desktop Menu --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ url('/') }}"
                       class="nav-link text-sm font-semibold transition-colors {{ request()->is('/') ? 'active' : '' }}"
                       :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Home</a>
                    <a href="{{ url('/#kalkulator') }}"
                       class="nav-link text-sm font-semibold transition-colors"
                       :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Kalkulator</a>
                    <a href="{{ auth()->check() ? route('user.donasi.create') : route('login') }}"
                       class="nav-link text-sm font-semibold transition-colors"
                       :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Bayar Zakat</a>
                    <a href="{{ url('/#tentang') }}"
                       class="nav-link text-sm font-semibold transition-colors"
                       :class="scrolled ? 'text-gray-700 hover:text-brand-600' : 'text-white/90 hover:text-white'">Tentang</a>
                </div>

                {{-- CTA Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}"
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-600 to-brand-700 hover:from-brand-700 hover:to-brand-800 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-glow transition-all hover:-translate-y-0.5">
                            Dashboard
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5-5 5M6 12h12"/></svg>
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="text-sm font-bold px-5 py-2.5 rounded-xl border transition-all"
                           :class="scrolled ? 'text-brand-700 border-brand-200 hover:bg-brand-50' : 'text-white border-white/30 hover:bg-white/10'">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-brand-700 hover:from-brand-600 hover:to-brand-800 text-white text-sm font-bold px-5 py-2.5 rounded-xl shadow-glow transition-all hover:-translate-y-0.5">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>

                {{-- Mobile Hamburger --}}
                <button @click="mobileMenu = !mobileMenu"
                        class="md:hidden p-2 rounded-xl transition-colors"
                        :class="scrolled ? 'text-gray-700 hover:bg-gray-100' : 'text-white hover:bg-white/10'"
                        aria-label="Menu">
                    <svg x-show="!mobileMenu" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    <svg x-show="mobileMenu" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>
        </nav>

        {{-- Mobile Menu --}}
        <div x-show="mobileMenu" x-cloak
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-2"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="md:hidden bg-white border-t border-gray-100 shadow-xl">
            <div class="px-4 py-4 space-y-1">
                <a href="{{ url('/') }}" @click="mobileMenu = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    Home
                </a>
                <a href="{{ url('/#kalkulator') }}" @click="mobileMenu = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 11h.01M12 11h.01M15 11h.01M4 7h16a1 1 0 011 1v10a1 1 0 01-1 1H4a1 1 0 01-1-1V8a1 1 0 011-1z"/></svg>
                    Kalkulator Zakat
                </a>
                <a href="{{ auth()->check() ? route('user.donasi.create') : route('login') }}" @click="mobileMenu = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Bayar Zakat
                </a>
                <a href="{{ url('/#tentang') }}" @click="mobileMenu = false"
                   class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-semibold text-gray-700 hover:bg-brand-50 hover:text-brand-700 transition-colors">
                    <svg class="w-5 h-5 text-brand-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Tentang
                </a>

                <div class="pt-3 mt-2 border-t border-gray-100 flex gap-3">
                    @auth
                        <a href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('user.dashboard') }}"
                           class="flex-1 text-center bg-gradient-to-r from-brand-600 to-brand-700 text-white text-sm font-bold px-4 py-3 rounded-xl">
                            Dashboard Saya
                        </a>
                    @else
                        <a href="{{ route('login') }}"
                           class="flex-1 text-center border border-brand-200 text-brand-700 text-sm font-bold px-4 py-3 rounded-xl">
                            Login
                        </a>
                        <a href="{{ route('register') }}"
                           class="flex-1 text-center bg-gradient-to-r from-brand-500 to-brand-700 text-white text-sm font-bold px-4 py-3 rounded-xl">
                            Daftar Gratis
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </header>

    {{-- ══════════════ CONTENT ══════════════ --}}
    <main>
        @yield('content')
    </main>

    {{-- ══════════════ FOOTER ══════════════ --}}
    <footer class="bg-gray-900 text-gray-400 relative overflow-hidden">
        <div class="absolute inset-0 hero-pattern opacity-30"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-8">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">

                {{-- Brand --}}
                <div class="lg:col-span-1">
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-500 to-brand-700 flex items-center justify-center">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4z"/>
                            </svg>
                        </div>
                        <span class="font-extrabold text-lg text-white">Zakat<span class="text-brand-400">Online</span></span>
                    </div>
                    <p class="text-sm leading-relaxed mb-5">
                        Platform zakat digital yang amanah, transparan, dan sesuai syariah. Memudahkan Anda menunaikan kewajiban zakat kapan saja, di mana saja.
                    </p>
                    <div class="flex items-center gap-2">
                        <span class="w-2 h-2 bg-brand-400 rounded-full animate-pulse"></span>
                        <span class="text-xs text-brand-300 font-medium">Sistem Aktif & Terpercaya</span>
                    </div>
                </div>

                {{-- Navigasi --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Navigasi</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li><a href="{{ url('/') }}" class="hover:text-brand-400 transition-colors">Home</a></li>
                        <li><a href="{{ url('/#kalkulator') }}" class="hover:text-brand-400 transition-colors">Kalkulator Zakat</a></li>
                        <li><a href="{{ auth()->check() ? route('user.donasi.create') : route('login') }}" class="hover:text-brand-400 transition-colors">Bayar Zakat</a></li>
                        <li><a href="{{ url('/#tentang') }}" class="hover:text-brand-400 transition-colors">Tentang Kami</a></li>
                        @guest
                        <li><a href="{{ route('register') }}" class="hover:text-brand-400 transition-colors">Daftar Akun</a></li>
                        @endguest
                    </ul>
                </div>

                {{-- Layanan --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Layanan Zakat</h4>
                    <ul class="space-y-2.5 text-sm">
                        <li class="flex items-center gap-2"><span>🌾</span> Zakat Fitrah</li>
                        <li class="flex items-center gap-2"><span>💰</span> Zakat Maal</li>
                        <li class="flex items-center gap-2"><span>💼</span> Zakat Profesi</li>
                        <li class="flex items-center gap-2"><span>🏪</span> Zakat Perniagaan</li>
                        <li class="flex items-center gap-2"><span>🥇</span> Zakat Emas & Perak</li>
                        <li class="flex items-center gap-2"><span>❤️</span> Sedekah</li>
                    </ul>
                </div>

                {{-- Kontak --}}
                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4">Hubungi Kami</h4>
                    <ul class="space-y-3 text-sm">
                        <li class="flex items-start gap-2.5">
                            <svg class="w-4 h-4 text-brand-400 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                            <span>Jl. Masjid Al-Ikhlas No. 12, Kemiling,<br>Kota Bandar Lampung 35158</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                            <span>(0721) 123-4567</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                            <span>info@zakatonline.id</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <svg class="w-4 h-4 text-brand-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span>Senin – Jumat: 08.00 – 16.00 WIB</span>
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Bottom Bar --}}
            <div class="border-t border-white/10 pt-6 flex flex-col sm:flex-row items-center justify-between gap-3">
                <p class="text-xs">&copy; {{ date('Y') }} Zakat Online. Seluruh hak cipta dilindungi.</p>
                <div class="flex items-center gap-5 text-xs">
                    <span class="flex items-center gap-1.5">🔒 Transaksi Aman</span>
                    <span class="flex items-center gap-1.5">🕌 Sesuai Syariah</span>
                    <span class="flex items-center gap-1.5">📊 Transparan</span>
                </div>
            </div>
        </div>
    </footer>

    {{-- Reveal on scroll --}}
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); observer.unobserve(e.target); } });
            }, { threshold: 0.12 });
            document.querySelectorAll('.reveal').forEach(el => observer.observe(el));
        });
    </script>
    @stack('scripts')
</body>
</html>
