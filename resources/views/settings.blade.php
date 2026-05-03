<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Settings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-800/50 backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <div class="grid md:grid-cols-2 gap-8">
                        <!-- Account Settings -->
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-6">Account</h3>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Email Address</label>
                                    <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-4 text-lg font-medium text-white">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Role</label>
                                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-800 border border-slate-600 rounded-xl text-sm font-bold text-slate-200 shadow-lg">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Theme Preferences -->
                        <div>
                            <h3 class="text-2xl font-bold text-white mb-6">Preferences</h3>
                            <div class="space-y-6">
                                <div x-data="{ currentTheme: 'dark' }" x-init="currentTheme = localStorage.getItem('library-theme') || 'dark'">
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Theme</label>
                                    <div class="flex gap-3">
                                        <button @click="setTheme('dark'); currentTheme='dark'" class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 border-slate-700 hover:border-slate-600 transition-all duration-200" :class="currentTheme === 'dark' ? 'bg-slate-800/50 border-cyan-500 shadow-cyan-500/20 shadow-lg text-white' : 'bg-slate-900/30 text-slate-400'">
                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                                            </svg>
                                            Dark Mode
                                        </button>
                                        <button @click="setTheme('light'); currentTheme='light'" class="flex-1 flex items-center gap-3 p-4 rounded-xl border-2 border-slate-700 hover:border-slate-600 transition-all duration-200" :class="currentTheme === 'light' ? 'bg-slate-800/50 border-cyan-500 shadow-cyan-500/20 shadow-lg text-white' : 'bg-slate-900/30 text-slate-400'">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707 .707M6.343 17.657l-.707 .707m12.728 0l-.707-.707M6.343 6.343l-.707-.707m12.728 0A9 9 0 115.636 5.636m12.728 12.728A9 9 0 015.636 5.636" />
                                            </svg>
                                            Light Mode
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Notification Preferences -->
            <div class="bg-slate-800/50 backdrop-blur-xl shadow-sm sm:rounded-2xl border border-slate-700/50">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-white mb-6">Notifications</h3>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-lg font-semibold text-slate-200 mb-4">Email Notifications</h4>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5">
                                    <span class="ml-3 text-sm text-slate-300">Borrow confirmations & due dates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5">
                                    <span class="ml-3 text-sm text-slate-300">Subscription updates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5">
                                    <span class="ml-3 text-sm text-slate-300">Late fees & penalties</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-slate-200 mb-4">Browser Notifications</h4>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5">
                                    <span class="ml-3 text-sm text-slate-300">Real-time updates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5">
                                    <span class="ml-3 text-sm text-slate-300">Urgent alerts (overdue)</span>
                                </label>
                            </div>
                            <div class="mt-6 p-4 bg-slate-900/50 rounded-xl border border-slate-700">
                                <h5 class="text-sm font-semibold text-slate-200 mb-2">Test Notification</h5>
                                <button class="w-full px-4 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white rounded-lg font-medium transition-all" onclick="testNotification()">
                                    Send Test Notification
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
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

        function testNotification() {
            if (Notification.permission === 'granted') {
                new Notification('Test Notification', {
                    body: 'Settings test notification received!',
                    icon: '/favicon.ico'
                });
            } else {
                alert('Please allow browser notifications to test.');
            }
        }

        // Request notification permission on load
        document.addEventListener('DOMContentLoaded', function() {
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
        });
    </script>
</x-app-layout>

