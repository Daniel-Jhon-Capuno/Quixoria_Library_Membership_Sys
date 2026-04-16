<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'LibraryHub') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Chart.js -->
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>

        <!-- Alpine.js for reactivity -->
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased" 
          style="background-color: rgb(var(--bg-primary)); color: rgb(var(--text-primary));"
          x-data="{ isDarkMode: true }"
          @theme-changed="isDarkMode = document.documentElement.classList.contains('dark')">
        
        <div class="min-h-screen" style="background-color: rgb(var(--bg-primary));">
            <!-- Sidebar -->
            <x-sidebar />

            <div class="ml-56 flex flex-col min-h-screen">
                <!-- Top Navigation Bar -->
                <header class="sticky top-0 z-40" style="background-color: rgb(var(--surface-primary)); border-bottom-color: rgb(var(--border-primary));" class="border-b">
                    <div class="px-6 py-4 flex items-center justify-between">
                        <div class="flex-1">
                            @isset($header)
                                {{ $header }}
                            @endisset
                        </div>
                        <div class="flex items-center gap-4">
                            <!-- Search -->
                            <div class="relative hidden md:block" x-data="{ searchOpen: false }">
                                <input type="text" 
                                       placeholder="Search..." 
                                       @focus="searchOpen = true"
                                       @click.away="searchOpen = false"
                                       style="background-color: rgb(var(--bg-primary)); border-color: rgb(var(--border-primary)); color: rgb(var(--text-primary));"
                                       class="rounded-lg py-2 pl-10 pr-4 text-sm placeholder-cyan-600 focus:outline-none focus:border-cyan-400 focus:ring-1 focus:ring-cyan-400/50 w-64 border">
                                <svg class="absolute left-3 top-2.5 w-4 h-4" style="color: rgb(var(--text-secondary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                </svg>
                            </div>

                            <!-- Theme Switcher -->
                            <x-theme-switcher />

                            <!-- Notifications Dropdown -->
                            <div class="relative" x-data="{ notificationOpen: false }">
                                <button @click="notificationOpen = !notificationOpen" 
                                        class="relative p-2 transition hover:opacity-80"
                                        style="color: rgb(var(--text-secondary));">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
                                    </svg>
                                    <span class="absolute top-1 right-1 w-2 h-2 bg-cyan-400 rounded-full"></span>
                                </button>
                                <div x-show="notificationOpen"
                                     @click.away="notificationOpen = false"
                                     style="background-color: rgb(var(--surface-primary)); border-color: rgb(var(--border-primary)); color: rgb(var(--text-primary));"
                                     class="absolute right-0 mt-2 w-64 rounded-lg shadow-lg border z-50 overflow-hidden">
                                    <div class="p-4 border-b" style="border-color: rgb(var(--border-primary));">
                                        <h3 class="font-semibold text-sm">Notifications</h3>
                                    </div>
                                    <div class="max-h-96 overflow-y-auto">
                                        <div class="p-4 hover:opacity-80 transition cursor-pointer" style="background-color: rgb(var(--bg-secondary));">
                                            <p class="text-sm font-medium">New book added</p>
                                            <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">2 hours ago</p>
                                        </div>
                                        <div class="p-4 hover:opacity-80 transition cursor-pointer" style="background-color: rgb(var(--bg-secondary));">
                                            <p class="text-sm font-medium">Borrow request approved</p>
                                            <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">5 hours ago</p>
                                        </div>
                                        <div class="p-4 hover:opacity-80 transition cursor-pointer" style="background-color: rgb(var(--bg-secondary));">
                                            <p class="text-sm font-medium">Your subscription expires soon</p>
                                            <p class="text-xs mt-1" style="color: rgb(var(--text-secondary));">1 day ago</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Settings Dropdown -->
                            <div class="relative" x-data="{ settingsOpen: false }">
                                <button @click="settingsOpen = !settingsOpen"
                                        class="p-2 transition hover:opacity-80"
                                        style="color: rgb(var(--text-secondary));">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                <div x-show="settingsOpen"
                                     @click.away="settingsOpen = false"
                                     style="background-color: rgb(var(--surface-primary)); border-color: rgb(var(--border-primary)); color: rgb(var(--text-primary));"
                                     class="absolute right-0 mt-2 w-48 rounded-lg shadow-lg border z-50 overflow-hidden">
                                    <div class="p-4 border-b" style="border-color: rgb(var(--border-primary));">
                                        <h3 class="font-semibold text-sm">Settings</h3>
                                    </div>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-2 hover:opacity-80 transition" style="background-color: rgb(var(--bg-secondary));">
                                        <p class="text-sm">Profile Settings</p>
                                    </a>
                                    <a href="#" class="block px-4 py-2 hover:opacity-80 transition" style="background-color: rgb(var(--bg-secondary));">
                                        <p class="text-sm">Preferences</p>
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="border-t" style="border-color: rgb(var(--border-primary));">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2 hover:opacity-80 transition text-sm text-left"
                                                style="color: rgb(var(--accent-primary));">
                                            Logout
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-6 py-8">
                    {{ $slot }}
                </main>

                <!-- Footer -->
                <footer class="border-t" style="border-color: rgb(var(--border-primary)); background-color: rgb(var(--bg-primary));" class="px-6 py-4 mt-auto">
                    <div class="flex items-center justify-between text-sm" style="color: rgb(var(--text-secondary));">
                        <p>&copy; 2026 LibraryHub. All rights reserved.</p>
                        <div class="flex gap-6">
                            <a href="#" class="transition" style="color: rgb(var(--text-secondary));" class="hover:text-slate-300">Privacy</a>
                            <a href="#" class="transition" style="color: rgb(var(--text-secondary));" class="hover:text-slate-300">Terms</a>
                            <a href="#" class="transition" style="color: rgb(var(--text-secondary));" class="hover:text-slate-300">Support</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        <!-- Mobile Menu Toggle Script -->
        <script>
            // Theme initialization
            document.addEventListener('DOMContentLoaded', function() {
                const savedTheme = localStorage.getItem('library-theme') || 'dark';
                setTheme(savedTheme);
            });

            function setTheme(theme) {
                const html = document.documentElement;
                if (theme === 'light') {
                    html.classList.add('light-mode');
                    html.classList.remove('dark');
                } else {
                    html.classList.remove('light-mode');
                    html.classList.add('dark');
                }
                localStorage.setItem('library-theme', theme);
            }
        </script>
    </body>
</html>
