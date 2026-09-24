@extends('layouts.main')

@section('breadcrumbs')
    <div class="flex items-center gap-2 text-xs text-gray-500 mb-4">
        <a href="{{ route('dashboard') }}" class="hover:text-gray-800 transition">Beranda</a>
        <span>/</span>
        <span class="font-semibold text-gray-800">Pusat Notifikasi</span>
    </div>
@endsection

@section('content')
<div class="space-y-6">
    <!-- Header Banner -->
    <div class="kt-card p-6 bg-gradient-to-r from-slate-900 to-red-950 text-white shadow-md rounded-2xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="inline-flex items-center gap-2 px-2.5 py-1 rounded-full bg-red-500/20 text-red-300 text-xs font-bold uppercase tracking-wider mb-2">
                <i class="ki-filled ki-notification-status"></i> In-App Alerts
            </div>
            <h1 class="text-2xl font-black text-white">Pusat Notifikasi &amp; Pemberitahuan</h1>
            <p class="text-slate-300 text-xs sm:text-sm mt-1">
                Pantau seluruh pembaruan status naskah ilmiah, penugasan penelaahan, dan keputusan editorial Anda.
            </p>
        </div>
        @if($unreadCount > 0)
            <form action="{{ route('notifications.mark_all_read') }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-100 text-slate-900 font-bold text-xs shadow-md transition flex items-center gap-2 shrink-0 cursor-pointer">
                    <i class="ki-filled ki-check-circle text-green-600"></i> Tandai Semua Dibaca
                </button>
            </form>
        @endif
    </div>

    <!-- Filter & Notifications List -->
    <div class="kt-card bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <!-- Filter Tabs -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 flex-wrap gap-3">
            <div class="flex items-center gap-2">
                <a href="{{ route('notifications.index', ['filter' => 'all']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition {{ $filter !== 'unread' ? 'bg-slate-900 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Semua ({{ auth()->user()->notifications()->count() }})
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}" 
                   class="px-4 py-2 rounded-xl text-xs font-bold transition flex items-center gap-1.5 {{ $filter === 'unread' ? 'bg-red-600 text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' }}">
                    Belum Dibaca
                    @if($unreadCount > 0)
                        <span class="px-1.5 py-0.2 rounded-full text-[10px] {{ $filter === 'unread' ? 'bg-white text-red-600' : 'bg-red-600 text-white' }}">
                            {{ $unreadCount }}
                        </span>
                    @endif
                </a>
            </div>
            <span class="text-xs text-slate-400 font-mono">
                Menampilkan {{ $notifications->total() }} pemberitahuan
            </span>
        </div>

        <!-- List -->
        <div class="divide-y divide-slate-100">
            @forelse($notifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Pemberitahuan Sistem';
                    $message = $data['message'] ?? '';
                    $actionUrl = $data['action_url'] ?? null;
                    $type = $data['type'] ?? 'info';
                    $icon = $data['icon'] ?? 'ki-notification-status';

                    $badgeBg = match($type) {
                        'success' => 'bg-green-100 text-green-700 border-green-200',
                        'warning' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'danger'  => 'bg-red-100 text-red-700 border-red-200',
                        default   => 'bg-blue-100 text-blue-700 border-blue-200',
                    };
                @endphp
                <div class="p-5 hover:bg-slate-50 transition flex flex-col sm:flex-row sm:items-center justify-between gap-4 {{ $isUnread ? 'bg-red-50/20' : '' }}">
                    <div class="flex items-start gap-4">
                        <div class="size-10 rounded-2xl flex items-center justify-center shrink-0 border {{ $badgeBg }}">
                            <i class="ki-filled {{ $icon }} text-lg"></i>
                        </div>
                        <div class="space-y-1">
                            <div class="flex items-center gap-2">
                                <h3 class="text-sm font-bold text-slate-900 {{ $isUnread ? 'text-red-700 font-extrabold' : '' }}">
                                    {{ $title }}
                                </h3>
                                @if($isUnread)
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700 uppercase">
                                        Baru
                                    </span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-600 leading-relaxed max-w-3xl">
                                {{ $message }}
                            </p>
                            <span class="text-[11px] text-slate-400 block font-mono">
                                {{ $notification->created_at->translatedFormat('d F Y, H:i') }} WIB ({{ $notification->created_at->diffForHumans() }})
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 shrink-0 self-end sm:self-center">
                        <form action="{{ route('notifications.mark_as_read', $notification->id) }}" method="POST">
                            @csrf
                            @if($actionUrl)
                                <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-red-600 hover:bg-red-700 text-white font-bold text-xs transition flex items-center gap-1.5 cursor-pointer shadow-xs">
                                    <span>Buka</span> &rarr;
                                </button>
                            @elseif($isUnread)
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition cursor-pointer">
                                    Tandai Dibaca
                                </button>
                            @endif
                        </form>
                    </div>
                </div>
            @empty
                <div class="p-16 text-center text-slate-400 space-y-3">
                    <i class="ki-filled ki-notification-on text-5xl text-slate-300"></i>
                    <h3 class="text-base font-bold text-slate-700">Tidak ada notifikasi</h3>
                    <p class="text-xs text-slate-400 max-w-sm mx-auto">
                        @if($filter === 'unread')
                            Semua pemberitahuan sudah dibaca. Hebat!
                        @else
                            Belum ada riwayat aktivitas atau notifikasi sistem untuk akun Anda.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        @if($notifications->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
