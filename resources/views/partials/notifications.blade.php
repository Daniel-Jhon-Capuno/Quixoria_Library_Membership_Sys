@if(auth()->check())
    @php
        $unreadNotifications = auth()->user()->unreadNotifications;
        $unreadCount = $unreadNotifications->count();
    @endphp

    <div x-data="{ showNotifications: false }" class="relative">
        <!-- Legitimate Notification Bell Icon -->
        <button @click="showNotifications = !showNotifications"
                class="p-2.5 rounded-lg hover:bg-slate-800/50 border border-slate-700/50 hover:border-slate-600/50 transition-all duration-200 flex items-center justify-center relative group"
                style="color: rgb(var(--text-secondary));">
            
            <svg class="w-6 h-6 group-hover:text-slate-300 transition-colors duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.73 21a2 2 0 0 1-3.46 0"></path>
                                </svg>

            <!-- Notification Badge - Perfectly positioned -->
            @if($unreadCount > 0)
                <span class="absolute -top-1 -right-1 block w-5 h-5 bg-red-600 ring-2 ring-slate-900/80 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-lg translate-x-1/4 -translate-y-1/4">
                    {{ min($unreadCount, 99) }}
                </span>
            @endif
        </button>

        <!-- Notification Dropdown -->
        <div x-show="showNotifications"
             @click.away="showNotifications = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 mt-2 w-96 bg-slate-800/95 backdrop-blur-xl rounded-2xl shadow-2xl ring-1 ring-slate-700/50 z-50 border border-slate-700">
            
            <div class="px-6 py-4 border-b border-slate-700/50">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-100">Notifications</h3>
                    @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.mark-read') }}" class="inline">
                            @csrf
                            <button type="submit" class="text-sm text-cyan-400 hover:text-cyan-300 font-medium transition-colors px-3 py-1 rounded-lg hover:bg-slate-700/50">
                                Mark all read
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="max-h-96 overflow-y-auto">
                @if($unreadCount === 0)
                    <div class="px-6 py-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1 1v1a3 3 0 01-3 3h-4a3 3 0 01-3-3V17h3a1 1 0 001 1h1z"></path>
                        </svg>
                        <h4 class="mt-4 text-sm font-medium text-gray-900">No notifications</h4>
                        <p class="mt-1 text-sm text-gray-500">You're all caught up</p>
                    </div>
                @else
                    @foreach($unreadNotifications->take(10) as $notification)
                        <a href="{{ route('notifications.go', $notification->id) }}" 
                           class="group px-6 py-4 border-b border-slate-700/50 hover:bg-slate-700/30 transition-all duration-200 flex items-start gap-4 last:border-b-0">
                            <div class="flex-shrink-0 w-12 h-12 rounded-xl flex items-center justify-center border @switch(data_get($notification->data, 'type', 'generic'))
@case('borrow_confirmed')
                                            @case('subscription_confirmed')
                                                bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border-emerald-500/30 group-hover:border-emerald-400/50
                                            @break
                                        @case('return_requested')
                                            bg-gradient-to-br from-orange-500/20 to-yellow-500/20 border-orange-500/30 group-hover:border-orange-400/50
                                            @break
                                        @case('deadline_reminder')
                                            @case('overdue')
                                                bg-gradient-to-br from-orange-500/20 to-red-500/20 border-orange-500/30 group-hover:border-orange-400/50
                                            @break
                                        @case('borrow_rejected')
                                            @case('subscription_rejected')
                                                bg-gradient-to-br from-red-500/20 to-rose-500/20 border-red-500/30 group-hover:border-red-400/50
                                            @break
                                        @case('reservation_ready')
                                            bg-gradient-to-br from-amber-500/20 to-orange-500/20 border-amber-500/30 group-hover:border-amber-400/50
                                            @break
                                        @default
                                            bg-gradient-to-br from-cyan-500/20 to-blue-500/20 border-cyan-500/30 group-hover:border-cyan-400/50
                                    @endswitch">
                                <svg class="w-5 h-5 text-inherit" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    @php $type = data_get($notification->data, 'type', 'generic'); @endphp
                                    @switch($type)
                                        @case('borrow_confirmed')
                                            @case('subscription_confirmed')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                                            @break
                                        @case('return_requested')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                            @break
                                        @case('deadline_reminder')
                                            @case('overdue')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            @break
                                        @case('borrow_rejected')
                                            @case('subscription_rejected')
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path>
                                            @break
                                        @case('reservation_ready')
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08 .402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                            @break
                                        @default
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1 1v1a3 3 0 01-3 3h-4a3 3 0 01-3-3V17h3a1 1 0 001 1h1z"></path>
                                    @endswitch
                                </svg>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-100 group-hover:text-white truncate">{{ data_get($notification->data, 'title') ?? 'New notification' }}</p>
                                <p class="text-sm text-slate-300 mt-1 line-clamp-2">{{ data_get($notification->data, 'message') ?? data_get($notification->data, 'body') ?? data_get($notification->data, 'text') ?? 'New update available' }}</p>
                                <p class="text-xs text-slate-500 mt-2">{{ $notification->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex-shrink-0 ml-2">
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>

            @if($unreadCount > 10)
                <div class="px-6 py-4 border-t border-slate-700/50 text-center">
                    <a href="#" class="text-sm text-cyan-400 hover:text-cyan-300 font-medium transition-colors">
                        View all {{ $unreadCount }} notifications
                    </a>
                </div>
            @endif
        </div>
    </div>
@endif

