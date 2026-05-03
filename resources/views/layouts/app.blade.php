<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'Quixoria') }}</title>

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
              x-data="{ isDarkMode: true, sidebarCollapsed: false }"
              x-init="(()=>{ sidebarCollapsed = (localStorage.getItem('sidebarCollapsed') === 'true') || (window.innerWidth < 1024); window.addEventListener('resize', () => { if(window.innerWidth < 1024) sidebarCollapsed = true; }); })()"
            @theme-changed="isDarkMode = document.documentElement.classList.contains('dark')"
            @sidebar-toggled.window="sidebarCollapsed = $event.detail">
        
        <div class="min-h-screen" style="background-color: rgb(var(--bg-primary));">
            <!-- Sidebar -->
            <x-sidebar />

            <div :class="sidebarCollapsed ? 'ml-16' : 'ml-56'" class="flex flex-col min-h-screen transition-all duration-200">
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
                            <div class="relative group">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-slate-400 group-focus-within:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                    </svg>
                                </span>
                                <input type="text" 
                                       class="block w-64 pl-10 pr-3 py-2 bg-slate-900/50 border border-slate-700 rounded-lg text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 transition-all hidden md:block" 
                                       placeholder="Search...">
                            </div>

                            <!-- Theme Switcher - Segmented pill style -->
                            <div class="flex items-center p-1 bg-slate-900/50 border border-slate-700 rounded-xl">
                                <button class="flex items-center gap-2 px-3 py-1.5 rounded-lg bg-gradient-to-r from-pink-500 to-rose-500 text-white text-xs font-bold shadow-lg shadow-pink-500/25 hover:shadow-pink-400/30 transition-all duration-200 active:scale-95" onclick="setTheme('dark')">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                    </svg>
                                    Dark
                                </button>
                                <button class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-slate-400 hover:text-slate-200 text-xs font-bold transition-all duration-200 hover:bg-slate-800/50 active:scale-95" onclick="setTheme('light')">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707 .707M6.343 17.657l-.707 .707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 0A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                                    </svg>
                                    Light
                                </button>
                            </div>

                            <!-- Notifications Dropdown (DB-driven) -->
                            @include('partials.notifications')

                            <!-- Settings Dropdown -->
                            <div class="relative" x-data="{ settingsOpen: false }">
                                <button @click="settingsOpen = !settingsOpen" class="p-2.5 rounded-lg hover:bg-slate-800/50 border border-slate-700/50 hover:border-slate-600/50 transition-all duration-200 flex items-center justify-center relative group"
                                    style="color: rgb(var(--text-secondary));">
                                    <svg class="w-6 h-6 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31 .826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296 .07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </button>
                                
                                <div x-show="settingsOpen"
                                     @click.away="settingsOpen = false"
                                     class="absolute right-0 mt-2 w-56 bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700 z-50 p-2">
                                    <a href="{{ route('settings') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800/50 transition-all text-sm font-medium flex items-center gap-3 {{ request()->routeIs('settings') ? 'bg-cyan-600/30 border border-cyan-500/50 text-cyan-200' : 'text-slate-300 hover:text-white' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31 .826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296 .07 2.572-1.065z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 01-6 0z"></path>
                                        </svg>
                                        Settings
                                    </a>
                                    <a href="{{ route('profile.edit') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800/50 transition-all text-sm font-medium flex items-center gap-3 {{ request()->routeIs('profile.*') ? 'bg-cyan-600/30 border border-cyan-500/50 text-cyan-200' : 'text-slate-300 hover:text-white' }}">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Profile
                                    </a>
                                    <div class="border-t border-slate-700/50 my-2"></div>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-3 rounded-xl hover:bg-red-600/50 transition-all text-sm font-medium flex items-center gap-3 text-slate-300 hover:text-red-100">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m10 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                            </svg>
                                            Log out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </header>

                <!-- Page Content -->
                <main class="flex-1 px-6 py-8">
                    <div class="max-w-7xl mx-auto w-full">
                    @if(session('success'))
                        <div class="mb-4 p-4 bg-green-800 border border-green-700 text-green-100 rounded-md">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="mb-4 p-4 bg-red-800 border border-red-700 text-red-100 rounded-md">{{ session('error') }}</div>
                    @endif
                    {{ $slot }}
                    </div>
                </main>

                <!-- Footer -->
                <footer class="border-t" style="border-color: rgb(var(--border-primary)); background-color: rgb(var(--bg-primary));" class="px-6 py-4 mt-auto">
                    <div class="flex items-center justify-between text-sm" style="color: rgb(var(--text-secondary));">
                        <p>&copy; 2026 Quixoria. All rights reserved.</p>
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
            // Simple confirm popups for elements with data-confirm (non-admin)
            document.addEventListener('click', function(e) {
                const el = e.target.closest('[data-confirm]');
                if (!el) return;
                const msg = el.getAttribute('data-confirm') || 'Are you sure?';
                if (!confirm(msg)) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);
        </script>

        <!-- Admin confirmation modal (for admin-only destructive/quick-fix actions) -->
        <div id="admin-confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-slate-800 rounded-lg shadow-lg w-full max-w-lg p-6 border border-slate-700">
                <h3 class="text-lg font-medium text-gray-100 mb-2">Confirm Action</h3>
                <p id="admin-confirm-message" class="text-sm text-gray-200 mb-4">Are you sure?</p>
                <div class="flex justify-end space-x-2">
                    <button id="admin-confirm-cancel" class="px-4 py-2 bg-slate-700 text-gray-100 rounded">Cancel</button>
                    <button id="admin-confirm-accept" class="px-4 py-2 bg-red-600 text-white rounded">Confirm</button>
                </div>
            </div>
        </div>

        <script>
            (function(){
                let pendingForm = null;
                let pendingEl = null;
                const modal = document.getElementById('admin-confirm-modal');
                const msgEl = document.getElementById('admin-confirm-message');
                const btnCancel = document.getElementById('admin-confirm-cancel');
                const btnAccept = document.getElementById('admin-confirm-accept');

                document.addEventListener('click', function(e) {
                    const el = e.target.closest('[data-admin-confirm]');
                    if (!el) return;
                    e.preventDefault();
                    e.stopPropagation();

                    const message = el.getAttribute('data-admin-confirm') || 'Are you sure?';
                    const form = el.closest('form');
                    pendingForm = form;
                    pendingEl = el;
                    msgEl.textContent = message;
                    modal.classList.remove('hidden');
                }, true);

                btnCancel.addEventListener('click', function() {
                    pendingForm = null;
                    modal.classList.add('hidden');
                });

                btnAccept.addEventListener('click', function() {
                    if (pendingForm) {
                        pendingForm.submit();
                    } else if (pendingEl) {
                        // If the element is a link, navigate to its href
                        if (pendingEl.tagName && pendingEl.tagName.toLowerCase() === 'a' && pendingEl.getAttribute('href')) {
                            window.location.href = pendingEl.getAttribute('href');
                        } else {
                            // Fallback: if element has data-href or data-url, navigate
                            const h = pendingEl.getAttribute('data-href') || pendingEl.getAttribute('data-url') || pendingEl.getAttribute('href');
                            if (h) window.location.href = h;
                        }
                    }
                    pendingForm = null;
                    pendingEl = null;
                    modal.classList.add('hidden');
                });
            })();
        </script>
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
