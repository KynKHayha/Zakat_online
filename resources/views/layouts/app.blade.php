<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Zakat Online</title>
    @include('layouts.partials.theme')
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        .sidebar-link { transition: all 0.22s ease; }
        .sidebar-link:hover { background: rgba(5, 150, 105, 0.08); color: #059669; transform: translateX(2px); }
        .sidebar-link.active { background: linear-gradient(135deg, #047857, #10b981); color: white; box-shadow: 0 8px 20px -6px rgba(5, 150, 105, 0.45); }
        .toast-enter { animation: slideInRight 0.3s ease; }
        @keyframes slideInRight { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    </style>
    @stack('styles')
</head>
<body class="h-full bg-gray-50"
    x-data="{
        sidebarOpen: false,
        toasts: [],
        notifikasis: [],
        unreadCount: 0,
        addToast(msg, type='success') {
            const id = Date.now();
            this.toasts.push({id, msg, type});
            setTimeout(() => this.removeToast(id), 4500);
        },
        removeToast(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        },
        async loadNotifikasi() {
            try {
                const r = await fetch('{{ route('user.notifikasi.terbaru') }}', {headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content}});
                const d = await r.json();
                this.notifikasis = d.notifikasis;
                this.unreadCount = d.unread;
            } catch(e) {}
        }
    }"
    x-init="
        loadNotifikasi();
        setInterval(() => loadNotifikasi(), 30000);
        $nextTick(() => {
            const flash = Alpine.store ? Alpine.store('flash') : null;
            if (flash && flash.msg) { addToast(flash.msg, flash.type || 'success'); }
        });
    ">

    <!-- Toast Container -->
    <div class="fixed top-4 right-4 z-[60] space-y-2 pointer-events-none" x-cloak>
        <template x-for="toast in toasts" :key="toast.id">
            <div class="pointer-events-auto toast-enter flex items-center gap-3 px-5 py-3.5 rounded-2xl shadow-2xl max-w-sm cursor-pointer border"
                :class="{
                    'bg-emerald-600 border-emerald-500 text-white': toast.type === 'success',
                    'bg-red-500 border-red-400 text-white': toast.type === 'error',
                    'bg-amber-500 border-amber-400 text-white': toast.type === 'warning',
                    'bg-blue-500 border-blue-400 text-white': toast.type === 'info'
                }"
                @click="removeToast(toast.id)">
                <span x-text="toast.type === 'success' ? '✓' : (toast.type === 'error' ? '✕' : '!')" class="text-lg font-bold"></span>
                <span x-text="toast.msg" class="text-sm font-medium flex-1"></span>
                <button @click.stop="removeToast(toast.id)" class="opacity-60 hover:opacity-100 text-lg leading-none">&times;</button>
            </div>
        </template>
    </div>

    @if(session('success'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('flash', { msg: '{{ addslashes(session('success')) }}', type: 'success' });
        });
    </script>
    @endif
    @if(session('error'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('flash', { msg: '{{ addslashes(session('error')) }}', type: 'error' });
        });
    </script>
    @endif
    @if(session('warning'))
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('flash', { msg: '{{ addslashes(session('warning')) }}', type: 'warning' });
        });
    </script>
    @endif

    <!-- Mobile Overlay -->
    <div x-show="sidebarOpen" x-cloak @click="sidebarOpen = false"
        class="fixed inset-0 bg-black/50 z-30 lg:hidden backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-end="opacity-0">
    </div>

    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-xl flex flex-col transition-transform duration-300 ease-in-out lg:translate-x-0 print:hidden"
        :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">

        <!-- Logo -->
        <div class="flex items-center gap-3 px-5 py-4 border-b border-gray-100">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 flex items-center justify-center shadow-md shadow-emerald-200 flex-shrink-0">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-14a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 7.586V4z" clip-rule="evenodd"/>
                </svg>
            </div>
            <div>
                <h1 class="font-bold text-gray-900 text-sm leading-tight">Zakat Online</h1>
                <p class="text-xs text-emerald-600">Muzakki Portal</p>
            </div>
        </div>

        <!-- Nav -->
        <nav class="flex-1 px-3 py-5 space-y-0.5 overflow-y-auto">
            <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest px-3 mb-3">Menu Utama</p>

            <a href="{{ route('user.dashboard') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-gray-600 {{ request()->routeIs('user.dashboard') ? 'active' : '' }}">
                <svg class="w-4.5 h-4.5 w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                <span class="text-sm">Dashboard</span>
            </a>

            <a href="{{ route('user.donasi.create') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-gray-600 {{ request()->routeIs('user.donasi.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="text-sm">Bayar Zakat</span>
            </a>

            <a href="{{ route('user.riwayat') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-gray-600 {{ request()->routeIs('user.riwayat') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                <span class="text-sm">Riwayat Zakat</span>
            </a>

            <a href="{{ route('user.notifikasi') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-gray-600 {{ request()->routeIs('user.notifikasi') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                <span class="text-sm">Notifikasi</span>
                <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount"
                    class="ml-auto bg-red-500 text-white text-[10px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center px-1 font-bold">
                </span>
            </a>

            <a href="{{ route('user.profil') }}"
                class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium text-gray-600 {{ request()->routeIs('user.profil') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-sm">Profil Saya</span>
            </a>
        </nav>

        <!-- User Card -->
        <div class="p-4 border-t border-gray-100">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-emerald-50">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-sm flex-shrink-0">
                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-gray-800 truncate">{{ auth()->user()->name }}</p>
                    <p class="text-[11px] text-emerald-600 truncate">Muzakki</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST" class="mt-2">
                @csrf
                <button type="submit" class="w-full flex items-center justify-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-red-50 rounded-xl transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                    Keluar
                </button>
            </form>
        </div>
    </aside>

    <!-- Main -->
    <div class="lg:pl-64 flex flex-col min-h-screen print:pl-0">
        <!-- Header -->
        <header class="sticky top-0 z-20 bg-white border-b border-gray-100 shadow-sm print:hidden">
            <div class="flex items-center justify-between px-4 md:px-6 py-3.5">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden p-2 rounded-lg hover:bg-gray-100 transition-colors">
                        <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    </button>
                    <div>
                        <h2 class="font-semibold text-gray-900 text-sm">@yield('page-title', 'Dashboard')</h2>
                        <p class="text-xs text-gray-400 hidden sm:block">@yield('page-subtitle', 'Selamat datang')</p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Bell -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open; loadNotifikasi()" class="relative p-2 rounded-xl hover:bg-gray-100 transition-colors">
                            <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                            <span x-show="unreadCount > 0" x-cloak x-text="unreadCount > 9 ? '9+' : unreadCount"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full min-w-[18px] h-[18px] flex items-center justify-center font-bold px-1">
                            </span>
                        </button>
                        <!-- Dropdown -->
                        <div x-show="open" x-cloak @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 translate-y-1"
                            x-transition:enter-end="opacity-100 translate-y-0"
                            class="absolute right-0 mt-2 w-80 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-3 bg-gradient-to-r from-emerald-700 to-emerald-500 flex items-center justify-between">
                                <h3 class="text-white font-semibold text-sm">Notifikasi</h3>
                                <span x-show="unreadCount > 0" x-cloak x-text="unreadCount + ' baru'" class="bg-white/20 text-white text-xs px-2 py-0.5 rounded-full"></span>
                            </div>
                            <div class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                                <template x-for="n in notifikasis" :key="n.id">
                                    <a href="{{ route('user.notifikasi') }}"
                                        class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 transition-colors"
                                        :class="!n.is_read ? 'bg-emerald-50/60' : ''">
                                        <div class="w-9 h-9 rounded-full flex items-center justify-center text-base flex-shrink-0"
                                            :class="n.tipe==='sukses' ? 'bg-emerald-100' : n.tipe==='ditolak' ? 'bg-red-100' : 'bg-blue-100'">
                                            <span x-text="n.icon"></span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-semibold text-gray-800 truncate" x-text="n.judul"></p>
                                            <p class="text-xs text-gray-500 line-clamp-2 mt-0.5" x-text="n.pesan"></p>
                                            <p class="text-[10px] text-gray-400 mt-1" x-text="n.created_at"></p>
                                        </div>
                                        <span x-show="!n.is_read" class="w-2 h-2 bg-emerald-500 rounded-full flex-shrink-0 mt-1"></span>
                                    </a>
                                </template>
                                <div x-show="notifikasis.length === 0" class="py-10 text-center">
                                    <p class="text-gray-400 text-sm">Tidak ada notifikasi</p>
                                </div>
                            </div>
                            <div class="px-4 py-2.5 bg-gray-50 border-t">
                                <a href="{{ route('user.notifikasi') }}" class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">
                                    Lihat semua &rarr;
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Avatar -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-2 p-1 rounded-xl hover:bg-gray-100 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600 flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                            </div>
                            <svg class="w-4 h-4 text-gray-400 hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-cloak @click.outside="open = false"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 scale-95"
                            x-transition:enter-end="opacity-100 scale-100"
                            class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl border border-gray-100 overflow-hidden z-50">
                            <div class="px-4 py-2.5 border-b border-gray-100">
                                <p class="text-xs font-semibold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                                <p class="text-[10px] text-emerald-600">Muzakki</p>
                            </div>
                            <a href="{{ route('user.profil') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm text-gray-700 hover:bg-gray-50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                Profil
                            </a>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="w-full flex items-center gap-2 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-4 md:p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')
</body>
</html>
