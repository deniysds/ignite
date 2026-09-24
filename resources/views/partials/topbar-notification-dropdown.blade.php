@php
    $unreadCount = auth()->check() ? auth()->user()->unreadNotifications()->count() : 0;
    $recentNotifications = auth()->check() ? auth()->user()->notifications()->latest()->take(6)->get() : collect();
@endphp

<div class="relative" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <!-- Bell Button -->
    <button @click="open = !open" 
            type="button" 
            class="relative flex items-center justify-center size-9 rounded-xl hover:bg-slate-100 text-slate-600 hover:text-slate-900 transition cursor-pointer" 
            title="Pemberitahuan & Notifikasi">
        <i class="ki-filled ki-notification-status text-xl"></i>
        @if($unreadCount > 0)
            <span class="absolute top-1.5 right-1.5 flex size-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                <span class="relative inline-flex rounded-full size-2.5 bg-red-600"></span>
            </span>
        @endif
    </button>

    <!-- Dropdown Menu -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="absolute right-0 mt-2 w-80 sm:w-96 rounded-2xl bg-white shadow-xl border border-slate-200 py-2 z-50 overflow-hidden" 
         style="display: none;">
        
        <!-- Header -->
        <div class="px-4 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-800">Notifikasi</h4>
                @if($unreadCount > 0)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-red-100 text-red-700">
                        {{ $unreadCount }} baru
                    </span>
                @endif
            </div>
            @if($unreadCount > 0)
                <form action="{{ route('notifications.mark_all_read') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="text-[11px] font-semibold text-slate-500 hover:text-red-600 transition cursor-pointer">
                        Tandai semua dibaca
                    </button>
                </form>
            @endif
        </div>

        <!-- Notification List -->
        <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
            @forelse($recentNotifications as $notification)
                @php
                    $isUnread = is_null($notification->read_at);
                    $data = $notification->data;
                    $title = $data['title'] ?? 'Pemberitahuan Sistem';
                    $message = $data['message'] ?? '';
                    $actionUrl = $data['action_url'] ?? '#';
                    $type = $data['type'] ?? 'info';
                    $icon = $data['icon'] ?? 'ki-notification-status';

                    $badgeBg = match($type) {
                        'success' => 'bg-green-100 text-green-700 border-green-200',
                        'warning' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'danger'  => 'bg-red-100 text-red-700 border-red-200',
                        default   => 'bg-blue-100 text-blue-700 border-blue-200',
                    };
                @endphp
                <div class="p-3.5 hover:bg-slate-50 transition relative group {{ $isUnread ? 'bg-red-50/30' : '' }}">
                    <div class="flex items-start gap-3">
                        <div class="size-8 rounded-xl flex items-center justify-center shrink-0 border {{ $badgeBg }}">
                            <i class="ki-filled {{ $icon }} text-sm"></i>
                        </div>
                        <div class="grow min-w-0">
                            <form action="{{ route('notifications.mark_as_read', $notification->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-left w-full cursor-pointer">
                                    <div class="flex items-center justify-between gap-1 mb-0.5">
                                        <h5 class="text-xs font-bold text-slate-900 truncate {{ $isUnread ? 'text-red-700' : '' }}">
                                            {{ $title }}
                                        </h5>
                                        @if($isUnread)
                                            <span class="size-2 rounded-full bg-red-600 shrink-0"></span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-600 line-clamp-2 leading-relaxed">
                                        {{ $message }}
                                    </p>
                                    <span class="text-[10px] text-slate-400 mt-1 block font-mono">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="ki-filled ki-notification-on text-3xl mb-2 text-slate-300 block"></i>
                    <p class="text-xs font-semibold text-slate-600">Belum ada notifikasi</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pemberitahuan aktivitas editorial akan muncul di sini.</p>
                </div>
            @endforelse
        </div>

        <!-- Footer -->
        <div class="px-4 py-2.5 bg-slate-50 border-t border-slate-100 text-center">
            <a href="{{ route('notifications.index') }}" class="text-xs font-bold text-red-600 hover:text-red-700 transition">
                Lihat Semua Notifikasi &rarr;
            </a>
        </div>
    </div>
</div>
