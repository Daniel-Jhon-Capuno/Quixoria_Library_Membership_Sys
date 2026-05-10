<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Quixoria') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;700;800&display=swap" rel="stylesheet" />
        <style>
            :root {
                --surface-primary: 20 45 70;
            }
        </style>
    </head>
    <body class="font-sans antialiased"
          style="background-color: rgb(var(--bg-primary)); color: rgb(var(--text-primary));"
          x-data="{ isDarkMode: true, sidebarCollapsed: false, mobileMenuOpen: false }"
          x-init="(()=>{
              sidebarCollapsed = (localStorage.getItem('sidebarCollapsed') === 'true') || (window.innerWidth < 1024);
              window.addEventListener('resize', () => {
                  if(window.innerWidth < 1024) { sidebarCollapsed = true; mobileMenuOpen = false; }
              });
          })()"
          @theme-changed="isDarkMode = document.documentElement.classList.contains('dark')"
          @sidebar-toggled.window="sidebarCollapsed = $event.detail">

        <div class="min-h-screen" style="background-color: rgb(var(--bg-primary));">

            <!-- Mobile Overlay -->
            <div x-show="mobileMenuOpen"
                 @click="mobileMenuOpen = false"
                 class="fixed inset-0 bg-black/60 z-40 lg:hidden"
                 x-transition.opacity></div>

            <!-- Sidebar -->
            <div :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'"
                 class="fixed top-0 left-0 h-full lg:translate-x-0 transition-transform duration-300 z-50">
                <x-sidebar />
            </div>

            <!-- Top Navigation Bar -->
            <header :class="sidebarCollapsed ? 'lg:pl-24' : 'lg:pl-64'" class="sticky top-0 z-40 border-b border-slate-700/50 w-full"
                    style="background-color: rgb(var(--surface-primary));">
                <div class="px-4 md:px-6 py-4 flex items-center justify-between gap-4">

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen"
                            class="lg:hidden p-2 rounded-lg hover:bg-slate-800/50 border border-slate-700/50 transition-all"
                            style="color: rgb(var(--text-secondary));">
                        <!-- Show hamburger when closed -->
                        <svg x-show="!mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <!-- Show X when open -->
                        <svg x-show="mobileMenuOpen" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>

                    <div class="flex-1">
                        @isset($header)
                            {{ $header }}
                        @endisset
                    </div>

                    <div class="flex items-center gap-2 md:gap-4">
                        <!-- Search - hidden on mobile -->
                        <div class="relative group hidden md:block">
                            <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-4 h-4 text-slate-400 group-focus-within:text-cyan-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </span>
                            <input type="text"
                                   class="block w-64 pl-10 pr-3 py-2 bg-slate-900/50 border border-slate-700 rounded-lg text-sm text-slate-200 placeholder-slate-500 focus:outline-none focus:ring-1 focus:ring-cyan-500 focus:border-cyan-500 transition-all"
                                   placeholder="Search...">
                        </div>

                        <!-- Theme Switcher - hidden on mobile -->
                        <div class="hidden md:block">
                            <x-theme-switcher />
                        </div>

                        <!-- Notifications -->
                        @include('partials.notifications')

                        <!-- Settings Dropdown -->
                        <div class="relative" x-data="{ settingsOpen: false }">
                            <button @click="settingsOpen = !settingsOpen"
                                    class="p-2.5 rounded-lg hover:bg-slate-800/50 border border-slate-700/50 hover:border-slate-600/50 transition-all duration-200 flex items-center justify-center relative group"
                                    style="color: rgb(var(--text-secondary));">
                                <svg class="w-6 h-6 group-hover:text-slate-300 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 01-6 0z"></path>
                                </svg>
                            </button>

                            <div x-show="settingsOpen"
                                 @click.away="settingsOpen = false"
                                 class="absolute right-0 mt-2 w-56 bg-slate-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-slate-700 z-50 p-2">

                                {{-- Theme switcher - mobile only --}}
                                <div class="md:hidden mb-2">
                                    <x-theme-switcher />
                                </div>

                                <a href="{{ route('settings') }}" class="block px-4 py-3 rounded-xl hover:bg-slate-800/50 transition-all text-sm font-medium flex items-center gap-3 {{ request()->routeIs('settings') ? 'bg-cyan-600/30 border border-cyan-500/50 text-cyan-200' : 'text-slate-300 hover:text-white' }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
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
                                    <button data-confirm="Are you sure you want to log out?" type="submit" class="w-full text-left px-4 py-3 rounded-xl hover:bg-red-600/50 transition-all text-sm font-medium flex items-center gap-3 text-slate-300 hover:text-red-100">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                                        </svg>
                                        <span>Logout</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <div :class="sidebarCollapsed ? 'lg:ml-24' : 'lg:ml-64'" class="flex flex-col min-h-screen transition-all duration-200 ml-0">
                <!-- Page Content -->
                <main class="flex-1 px-4 md:px-6 py-6 md:py-8 page-transition" aria-live="polite">
                    <div class="max-w-7xl mx-auto w-full">
                        @if(session('success'))
                            <div class="mb-4 p-4 bg-green-800 border border-green-700 text-green-100 rounded-md">{{ session('success') }}</div>
                        @endif
                        @if(session('error'))
                            <div class="mb-4 p-4 bg-red-800 border border-red-700 text-red-100 rounded-md">{{ session('error') }}</div>
                        @endif
                        @isset($slot)
                            {{ $slot }}
                        @else
                            @yield('content')
                        @endisset
                    </div>
                </main>

                <!-- Footer -->
                <footer class="border-t border-slate-700/50 px-4 md:px-6 py-4 mt-auto"
                        style="background-color: rgb(var(--bg-primary));">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-2 text-sm"
                         style="color: rgb(var(--text-secondary));">
                        <p>&copy; 2026 Quixoria. All rights reserved.</p>
                        <div class="flex gap-6">
                            <a href="#" class="hover:text-slate-300 transition">Privacy</a>
                            <a href="#" class="hover:text-slate-300 transition">Terms</a>
                            <a href="#" class="hover:text-slate-300 transition">Support</a>
                        </div>
                    </div>
                </footer>
            </div>
        </div>

        {{-- Mobile bottom nav for small screens --}}
        @include('partials.mobile-nav')

        <!-- Global confirm modal + loader -->
        <div id="confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div id="confirm-modal-card" class="bg-slate-800 rounded-lg shadow-lg w-full max-w-md mx-4 p-6 border border-slate-700 transform scale-95 opacity-0 transition-all duration-300">
                <h3 class="text-lg font-semibold text-gray-100 mb-2">Please confirm</h3>
                <p id="confirm-modal-message" class="text-sm text-gray-200 mb-4">Are you sure?</p>
                <div class="flex justify-end gap-2">
                    <button id="confirm-modal-cancel" class="px-4 py-2 bg-slate-700 text-gray-100 rounded">Cancel</button>
                    <button id="confirm-modal-accept" class="px-4 py-2 bg-cyan-600 text-white rounded">Confirm</button>
                </div>
            </div>
        </div>

        <div id="global-loader" class="hidden fixed inset-0 z-60 flex items-center justify-center bg-black bg-opacity-60">
            <div class="flex flex-col items-center gap-3">
                <div class="loader ease-linear rounded-full border-4 border-t-4 border-slate-200 h-12 w-12"></div>
                <div class="text-sm text-white">Processing…</div>
            </div>
        </div>

        <style>
            .loader { border-top-color: rgba(6,182,212,1); animation: spin 1s linear infinite; }
            @keyframes spin { to { transform: rotate(360deg); } }
            #confirm-modal .show { opacity: 1; }
            #confirm-modal-card.show { transform: scale(1); opacity: 1; }
        </style>

        <script>
            (function(){
                const modal = document.getElementById('confirm-modal');
                const card = document.getElementById('confirm-modal-card');
                const msgEl = document.getElementById('confirm-modal-message');
                const btnCancel = document.getElementById('confirm-modal-cancel');
                const btnAccept = document.getElementById('confirm-modal-accept');
                const loader = document.getElementById('global-loader');

                let pendingAction = null;

                function showModal(message, action) {
                    msgEl.textContent = message || 'Are you sure?';
                    pendingAction = action || null;
                    modal.classList.remove('hidden');
                    // animate pop-out
                    requestAnimationFrame(() => {
                        card.classList.add('show');
                    });
                }

                function hideModal() {
                    card.classList.remove('show');
                    setTimeout(() => { modal.classList.add('hidden'); }, 220);
                }

                function showLoader() { loader.classList.remove('hidden'); }

                function hideLoader() { loader.classList.add('hidden'); }

                document.addEventListener('click', function(e) {
                    const el = e.target.closest('[data-confirm]');
                    if (!el) return;
                    e.preventDefault();
                    e.stopPropagation();

                    const message = el.getAttribute('data-confirm') || 'Are you sure?';

                    // prepare action depending on element
                    const form = el.closest('form');
                    const href = el.getAttribute('href');

                    pendingAction = function() {
                        // show loader before proceeding
                        showLoader();
                        if (form) {
                            form.submit();
                        } else if (href) {
                            // small delay to allow loader to display
                            setTimeout(() => { window.location.href = href; }, 80);
                        } else {
                            // fallback: click the element
                            el.click();
                        }
                    };

                    showModal(message, pendingAction);
                }, true);

                btnCancel.addEventListener('click', function() {
                    hideModal();
                });

                btnAccept.addEventListener('click', function() {
                    hideModal();
                    if (typeof pendingAction === 'function') pendingAction();
                });
            })();
        </script>

        <!-- Page transitions -->
        <style>
            main.page-transition{ transition: opacity 300ms ease, transform 300ms ease; }
            main.page-transition.page-enter{ opacity: 0; transform: translateY(8px); }
            main.page-transition.page-enter.page-ready{ opacity: 1; transform: translateY(0); }
            main.page-transition.page-exit{ opacity: 0; transform: translateY(-8px); }
        </style>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const main = document.querySelector('main.page-transition');
                if (!main) return;

                // Enter animation on load
                main.classList.add('page-enter');
                requestAnimationFrame(() => {
                    main.classList.add('page-ready');
                });

                // Clean up classes after animation completes
                setTimeout(() => {
                    main.classList.remove('page-enter', 'page-ready');
                }, 350);

                // Intercept internal link clicks to play exit animation
                document.addEventListener('click', function (e) {
                    const a = e.target.closest('a');
                    if (!a) return;
                    // ignore links that open in new tab or have explicit opt-out
                    if (a.target || a.hasAttribute('data-no-transition') || a.getAttribute('href')?.startsWith('#') ) return;
                    // only handle same-origin navigations
                    try {
                        const url = new URL(a.href, location.href);
                        if (url.origin !== location.origin) return;
                    } catch (err) { return; }

                    e.preventDefault();
                    main.classList.add('page-exit');
                    setTimeout(() => { window.location.href = a.href; }, 300);
                }, true);
            });
        </script>

        <!-- Admin confirmation modal -->
        <div id="admin-confirm-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-slate-800 rounded-lg shadow-lg w-full max-w-lg mx-4 p-6 border border-slate-700">
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
                        const h = pendingEl.getAttribute('data-href') || pendingEl.getAttribute('data-url') || pendingEl.getAttribute('href');
                        if (h) window.location.href = h;
                    }
                    pendingForm = null;
                    pendingEl = null;
                    modal.classList.add('hidden');
                });
            })();
        </script>

        <script>
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
                document.body.dispatchEvent(new CustomEvent('theme-changed', { detail: theme, bubbles: true }));
            }
        </script>
    </body>
</html>
