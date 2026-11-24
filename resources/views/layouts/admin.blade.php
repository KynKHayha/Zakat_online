<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — Zakat Online</title>
    @include('layouts.partials.theme')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .admin-link { transition: all 0.22s ease; }
        .admin-link:hover { background: rgba(16, 185, 129, 0.12); color: #34d399; transform: translateX(2px); }
        .admin-link.active { background: linear-gradient(135deg, #10b981, #059669); color: white; box-shadow: 0 8px 22px -8px rgba(16,185,129,0.6); }
        .sidebar-dark::-webkit-scrollbar-track { background: #0f172a; }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-100"
    x-data="{
        sidebarOpen: false,
        toasts: [],
        addToast(msg, type='success') {
            const id = Date.now();
            this.toasts.push({id, msg, type});
            setTimeout(() => this.removeToast(id), 4500);
        },
        removeToast(id) { this.toasts = this.toasts.filter(t => t.id !== id); }
    }"
    x-init="$nextTick(() => {
        const flash = Alpine.store ? Alpine.store('adminFlash') : null;
        if (flash && flash.msg) { addToast(flash.msg, flash.type || 'success'); }
    })">

    <!-- Toast -->
    <div class="fixed top-4 right-4 z-[60] space-y-2 pointer-events-none" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl max-w-sm cursor-pointer border"
                :class="{'bg-emerald-600 border-emerald-500 text-white': toast.type==='success','bg-red-500 border-red-400 text-white': toast.type==='error','bg-amber-500 border-amber-400 text-white': toast.type==='warning'}"
                @click="removeToast(toast.id)">
                <span class="text-sm font-medium flex-1" x-text="toast.msg"></span>
                <button class="opacity-70 hover:opacity-100">&times;</button>
            </div>
        </template>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('adminFlash', { msg: '{{ addslashes(session('success')) }}', type: 'success' });
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('adminFlash', { msg: '{{ addslashes(session('error')) }}', type: 'error' });
        });
    </script>
    @endif
    @if(session('warning'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('adminFlash', { msg: '{{ addslashes(session('warning')) }}', type: 'warning' });
        });
    </script>
    @endif

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen=false"
        class="fixed inset-0 bg-black/60 z-30 lg:hidden backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
    </div>

    <!-- Sidebar (dark slate) -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-slate-900 flex flex-col transition-transform duration-300 lg:translate-x-0"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-700/50">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center shadow-lg flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-14a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 7.586V4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-white text-sm leading-tight">Zakat Online</h1>
                <p class="text-xs text-emerald-400">Admin / Amil Panel</p>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-3">Navigasi</p>

            <a href="{{ route('admin.dashboard') }}"
                class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 font-medium {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <a href="{{ route('admin.transaksi.index') }}"
                class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 font-medium {{ request()->routeIs('admin.transaksi.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                <span class="text-sm">Manajemen Transaksi</span>
                @php $pending = \App\Models\Transaksi::where('status','menunggu')->count(); @endphp
                @if($pending > 0)
                <span class="ml-auto bg-red-500 text-white text-[10px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 font-bold">{{ $pending }}</span>
                @endif
            </a>

            <a href="{{ route('admin.users.index') }}"
                class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 font-medium {{ request()->routeIs('admin.users.index') || request()->routeIs('admin.users.riwayat') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <span class="text-sm">Manajemen User</span>
            </a>

            <a href="{{ route('admin.users.utang') }}"
                class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 font-medium {{ request()->routeIs('admin.users.utang') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm">Status Pembayaran</span>
            </a>

            <a href="{{ route('admin.laporan.index') }}"
                class="admin-link flex items-center gap-3 px-3 py-2.5 rounded-xl text-slate-400 font-medium {{ request()->routeIs('admin.laporan.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                <span class="text-sm">Laporan Keuangan</span>
            </a>
        </nav>

        <!-- Admin Info -->
        <div class="p-4 border-t border-slate-700/50">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-emerald-400">Amil Zakat</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm text-red-400 hover:bg-slate-800 rounded-xl transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="lg:pl-64 flex flex-col min-h-screen">
        <!-- Header -->
        <header class="sticky top-0 z-20 bg-white border-b border-gray-200 shadow-sm">
            <div class="flex items-center justify-between px-4 md:px-6 py-3.5">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen=true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h2 class="font-bold text-gray-900 text-sm">@yield('page-title', 'Dashboard Admin')</h2>
                        <p class="text-xs text-gray-400 hidden sm:block">@yield('page-subtitle', 'Panel Amil Zakat')</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <span class="hidden sm:flex items-center gap-1.5 bg-emerald-50 text-emerald-700 text-xs font-semibold px-3 py-1.5 rounded-full border border-emerald-200">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        Admin Aktif
                    </span>
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </div>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-gray-200 py-3 px-6">
            <p class="text-xs text-gray-400 text-center">&copy; {{ date('Y') }} Zakat Online — Admin Panel</p>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>
