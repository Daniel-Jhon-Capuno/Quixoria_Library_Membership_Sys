@if(auth()->check())
    @php
        $unreadNotifications = auth()->user()->unreadNotifications;
        $unreadCount = $unreadNotifications->count();
    @endphp

    <div x-data="{ showNotifications: false }" class="relative">
        <!-- Notification Bell Icon -->
        <button @click="showNotifications = !showNotifications"
                class="relative p-2 text-gray-600 hover:text-gray-900 focus:outline-none focus:text-gray-900 transition duration-150 ease-in-out">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM15 7v5h5l-5 5v-5zM4 12h8m0 0v8m0-8H4m8 0V4m0 8H4"></path>
            </svg>

            <!-- Unread Count Badge -->
            @if($unreadCount > 0)
            <span class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/2 -translate-y-1/2 bg-red-600 rounded-full">
                {{ $unreadCount }}
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
             class="absolute right-0 mt-2 w-80 bg-white rounded-md shadow-lg z-50"
             style="display: none;">

            <div class="py-2">
                <div class="px-4 py-2 border-b border-gray-200">
                    <h3 class="text-sm font-medium text-gray-900">Notifications</h3>
                </div>

                <div class="max-h-64 overflow-y-auto">
                    @if($unreadCount === 0)
                        <div class="px-4 py-8 text-center text-gray-500">
                            <p class="text-sm">No new notifications</p>
                        </div>
                    @else
                        @foreach($unreadNotifications->take(10) as $notification)
                            <div class="px-4 py-3 border-b border-gray-100 hover:bg-gray-50">
                                <div class="flex items-start">
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-gray-900">
                                            {{ data_get($notification->data, 'title') ?? 'Notification' }}
                                        </p>
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ data_get($notification->data, 'message') ?? data_get($notification->data, 'body') ?? data_get($notification->data, 'text') ?? '' }}
                                        </p>
                                        <p class="text-xs text-gray-400 mt-1">
                                            {{ $notification->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    @if(isset($notification->data['action_url']))
                                        <a href="{{ route('notifications.go', $notification->id) }}"
                                           class="ml-3 text-xs text-indigo-600 hover:text-indigo-800 font-medium">
                                            {{ $notification->data['action_text'] ?? 'View' }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>

                @if($unreadCount > 0)
                    <div class="px-4 py-2 border-t border-gray-200">
                        <form method="POST" action="{{ route('notifications.mark-read') }}">
                            @csrf
                            <button type="submit" class="text-sm text-indigo-600 hover:text-indigo-800 font-medium">
                                Mark all as read
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif