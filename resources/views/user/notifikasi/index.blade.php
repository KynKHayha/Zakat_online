@extends('layouts.app')
@section('title', 'Notifikasi')
@section('page-title', 'Notifikasi')
@section('page-subtitle', 'Pemberitahuan status pembayaran zakat Anda')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="bg-white rounded-4xl shadow-soft border border-gray-100 overflow-hidden">
        <div class="px-5 sm:px-6 py-4 border-b border-gray-100 flex items-center justify-between gap-3">
            <h3 class="font-extrabold text-gray-900 flex items-center gap-2">
                <span class="text-lg">🔔</span> Semua Notifikasi
            </h3>
            <span class="chip bg-gray-100 text-gray-500 num">{{ $notifikasis->total() }} notifikasi</span>
        </div>

        @if($notifikasis->count())
            <div class="divide-y divide-gray-100">
                @foreach($notifikasis as $notif)
                    @php
                        $config = [
                            'sukses'    => ['border' => 'border-l-brand-500',    'bg' => 'bg-brand-50',    'icon' => '✅', 'iconBg' => 'bg-brand-100', 'text' => 'text-brand-700'],
                            'ditolak'   => ['border' => 'border-l-red-500',      'bg' => 'bg-red-50',      'icon' => '❌', 'iconBg' => 'bg-red-100',   'text' => 'text-red-700'],
                            'peringatan'=> ['border' => 'border-l-amber-500',    'bg' => 'bg-amber-50',    'icon' => '⚠️', 'iconBg' => 'bg-amber-100', 'text' => 'text-amber-700'],
                            'info'      => ['border' => 'border-l-blue-500',     'bg' => 'bg-blue-50',     'icon' => 'ℹ️', 'iconBg' => 'bg-blue-100',  'text' => 'text-blue-700'],
                        ];
                        $c = $config[$notif->tipe] ?? $config['info'];
                    @endphp
                    <div class="flex items-start gap-4 px-5 sm:px-6 py-5 border-l-4 {{ $c['border'] }} {{ !$notif->is_read ? $c['bg'] : '' }} transition-colors hover:bg-gray-50/70">
                        <div class="w-11 h-11 rounded-2xl {{ $c['iconBg'] }} flex items-center justify-center text-lg flex-shrink-0 mt-0.5">
                            {{ $c['icon'] }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-start justify-between gap-2">
                                <h4 class="font-bold text-gray-900 text-sm leading-snug">{{ $notif->judul }}</h4>
                                @if(!$notif->is_read)
                                    <span class="flex-shrink-0 bg-brand-600 text-white text-[10px] font-extrabold px-2 py-1 rounded-full tracking-wider">BARU</span>
                                @endif
                            </div>
                            <p class="text-sm text-gray-600 mt-1.5 leading-relaxed">{{ $notif->pesan }}</p>
                            <div class="flex items-center gap-3 mt-2.5 flex-wrap">
                                <span class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</span>
                                @if($notif->transaksi)
                                    <a href="{{ route('user.transaksi.show', $notif->transaksi->id) }}"
                                       class="text-xs {{ $c['text'] }} font-bold hover:underline">
                                        Lihat Transaksi &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="px-5 sm:px-6 py-4 border-t border-gray-100">
                {{ $notifikasis->links() }}
            </div>
        @else
            <div class="py-20 px-6 text-center">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4 text-4xl">🔔</div>
                <h3 class="text-gray-800 font-extrabold">Belum Ada Notifikasi</h3>
                <p class="text-gray-400 text-sm mt-1.5 max-w-xs mx-auto leading-relaxed">
                    Notifikasi akan muncul setelah admin memverifikasi transaksi Anda.
                </p>
            </div>
        @endif
    </div>
</div>
@endsection
