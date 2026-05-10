<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Settings</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-slate-800/50 backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <div class="flex items-center justify-between mb-8">
                        <h3 class="text-2xl font-bold text-white">Account</h3>
                        <button onclick="document.getElementById('editProfileModal').classList.remove('hidden')" class="px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-800 hover:from-slate-600 hover:to-slate-700 text-slate-200 rounded-lg font-medium transition-all">
                            Edit Profile
                        </button>
                    </div>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Full Name</label>
                                    <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-4 text-lg font-medium text-white account-name-display">
                                        {{ auth()->user()->name ?? 'User' }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Email Address</label>
                                    <div class="bg-slate-900/50 border border-slate-700 rounded-xl p-4 text-lg font-medium text-white account-email-display">
                                        {{ auth()->user()->email }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div>
                            <div class="space-y-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Role</label>
                                    <div class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-slate-700 to-slate-800 border border-slate-600 rounded-xl text-sm font-bold text-slate-200 shadow-lg">
                                        {{ ucfirst(auth()->user()->role) }}
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-300 mb-3">Account Status</label>
                                    <div class="flex items-center gap-2">
                                        <span class="relative flex h-3 w-3">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                        </span>
                                        <span class="text-green-300 font-medium">Active</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Theme Preferences -->
            <div class="bg-slate-800/50 backdrop-blur-xl overflow-hidden shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-white mb-6">Preferences</h3>
                    <div x-data="{ currentTheme: 'dark' }" x-init="currentTheme = localStorage.getItem('library-theme') || 'dark'">
                        <label class="block text-sm font-semibold text-slate-300 mb-4">Theme</label>
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

            <!-- Notification Preferences -->
            <div class="bg-slate-800/50 backdrop-blur-xl shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-white mb-6">Notifications</h3>
                    <div class="grid md:grid-cols-2 gap-8">
                        <div>
                            <h4 class="text-lg font-semibold text-slate-200 mb-4">Email Notifications</h4>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5" checked>
                                    <span class="ml-3 text-sm text-slate-300">Borrow confirmations & due dates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5" checked>
                                    <span class="ml-3 text-sm text-slate-300">Subscription updates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5" checked>
                                    <span class="ml-3 text-sm text-slate-300">Late fees & penalties</span>
                                </label>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-lg font-semibold text-slate-200 mb-4">Browser Notifications</h4>
                            <div class="space-y-4">
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5" checked>
                                    <span class="ml-3 text-sm text-slate-300">Real-time updates</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5" checked>
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
            <div class="bg-slate-800/50 backdrop-blur-xl shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-white mb-6">Security</h3>
                    <div class="space-y-6">
                        <!-- Change Password -->
                        <div class="p-6 bg-slate-900/30 rounded-xl border border-slate-700/50">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-white">Change Password</h4>
                                    <p class="text-sm text-slate-400 mt-1">Update your password regularly to keep your account secure</p>
                                </div>
                                <svg class="w-6 h-6 text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                </svg>
                            </div>
                            <button onclick="document.getElementById('changePasswordModal').classList.remove('hidden')" class="px-6 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white rounded-lg font-medium transition-all">
                                Update Password
                            </button>
                        </div>

                        <!-- Two-Factor Authentication -->
                        <div class="p-6 bg-slate-900/30 rounded-xl border border-slate-700/50">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-white">Two-Factor Authentication</h4>
                                    <p class="text-sm text-slate-400 mt-1">Add an extra layer of security to your account</p>
                                </div>
                                <span class="px-3 py-1 bg-red-900/30 text-red-300 text-xs font-bold rounded-full border border-red-700/50">Disabled</span>
                            </div>
                            <button onclick="alert('2FA setup coming soon!')" class="px-6 py-2 bg-gradient-to-r from-amber-600 to-orange-600 hover:from-amber-700 hover:to-orange-700 text-white rounded-lg font-medium transition-all">
                                Enable 2FA
                            </button>
                        </div>

                        <!-- Active Sessions -->
                        <div class="p-6 bg-slate-900/30 rounded-xl border border-slate-700/50">
                            <h4 class="text-lg font-semibold text-white mb-4">Active Sessions</h4>
                            <p class="text-sm text-slate-400 mb-4">Manage devices that are logged into your account</p>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg border border-slate-700/50">
                                    <div>
                                        <p class="text-white font-medium">Current Device</p>
                                        <p class="text-xs text-slate-400">Last active: Just now</p>
                                    </div>
                                    <span class="px-2 py-1 bg-green-900/30 text-green-300 text-xs font-bold rounded">Active</span>
                                </div>
                            </div>
                            <button class="mt-4 px-6 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-all">
                                Sign Out All Other Sessions
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy & Data -->
            <div class="bg-slate-800/50 backdrop-blur-xl shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-white mb-6">Privacy & Data</h3>
                    <div class="space-y-6">
                        <!-- Profile Visibility -->
                        <div class="p-6 bg-slate-900/30 rounded-xl border border-slate-700/50">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-white">Profile Visibility</h4>
                                    <p class="text-sm text-slate-400 mt-1">Control who can see your profile information</p>
                                </div>
                                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.658 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                            </div>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" class="w-4 h-4" checked>
                                    <span class="ml-3 text-slate-300">Private (only staff can view)</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="radio" name="visibility" class="w-4 h-4">
                                    <span class="ml-3 text-slate-300">Public (anyone can view)</span>
                                </label>
                            </div>
                        </div>

                        <!-- Data Export -->
                        <div class="p-6 bg-slate-900/30 rounded-xl border border-slate-700/50">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-white">Export Personal Data</h4>
                                    <p class="text-sm text-slate-400 mt-1">Download a copy of your account data in JSON format</p>
                                </div>
                                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                </svg>
                            </div>
                            <button class="px-6 py-2 bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700 text-white rounded-lg font-medium transition-all">
                                Download My Data
                            </button>
                        </div>

                        <!-- Delete Account -->
                        <div class="p-6 bg-red-900/20 rounded-xl border border-red-700/50">
                            <div class="flex items-start justify-between mb-4">
                                <div>
                                    <h4 class="text-lg font-semibold text-red-300">Delete Account</h4>
                                    <p class="text-sm text-red-200/70 mt-1">Permanently delete your account and all associated data</p>
                                </div>
                                <svg class="w-6 h-6 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </div>
                            <button onclick="document.getElementById('deleteAccountModal').classList.remove('hidden')" class="px-6 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-medium transition-all">
                                Delete Account
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Communication Preferences -->
            <div class="bg-slate-800/50 backdrop-blur-xl shadow-sm sm:rounded-2xl border border-slate-700/50 mb-8">
                <div class="p-8">
                    <h3 class="text-2xl font-bold text-white mb-6">Communication</h3>
                    <div class="space-y-4">
                        <label class="flex items-center p-4 bg-slate-900/30 rounded-lg border border-slate-700/50 cursor-pointer hover:border-slate-600 transition-all">
                            <input type="checkbox" class="w-4 h-4 rounded" checked>
                            <span class="ml-3">
                                <span class="text-white font-medium">Marketing Emails</span>
                                <p class="text-xs text-slate-400">Receive updates about new books and promotions</p>
                            </span>
                        </label>
                        <label class="flex items-center p-4 bg-slate-900/30 rounded-lg border border-slate-700/50 cursor-pointer hover:border-slate-600 transition-all">
                            <input type="checkbox" class="w-4 h-4 rounded" checked>
                            <span class="ml-3">
                                <span class="text-white font-medium">Weekly Digest</span>
                                <p class="text-xs text-slate-400">Get a weekly summary of your library activity</p>
                            </span>
                        </label>
                        <label class="flex items-center p-4 bg-slate-900/30 rounded-lg border border-slate-700/50 cursor-pointer hover:border-slate-600 transition-all">
                            <input type="checkbox" class="w-4 h-4 rounded">
                            <span class="ml-3">
                                <span class="text-white font-medium">SMS Notifications</span>
                                <p class="text-xs text-slate-400">Receive urgent alerts via text message</p>
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Modals -->
            <!-- Change Password Modal -->
            <div id="changePasswordModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 max-w-md w-full shadow-2xl">
                    <h3 class="text-2xl font-bold text-white mb-6">Change Password</h3>
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Current Password</label>
                            <input type="password" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">New Password</label>
                            <input type="password" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Confirm Password</label>
                            <input type="password" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all">
                        </div>
                    </div>
                    <div class="flex gap-3">
                        <button onclick="document.getElementById('changePasswordModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-all">
                            Cancel
                        </button>
                        <button class="flex-1 px-4 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white rounded-lg font-medium transition-all">
                            Update
                        </button>
                    </div>
                </div>
            </div>

            <!-- Delete Account Modal -->
            <div id="deleteAccountModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-slate-800 rounded-2xl border border-red-700/50 p-8 max-w-md w-full shadow-2xl">
                    <h3 class="text-2xl font-bold text-red-400 mb-2">Delete Account?</h3>
                    <p class="text-slate-300 mb-6">This action cannot be undone. All your data will be permanently deleted.</p>
                    <div class="mb-6 p-4 bg-red-900/20 border border-red-700/50 rounded-lg">
                        <p class="text-sm text-red-200">Type your email to confirm:</p>
                        <input type="text" placeholder="your@email.com" class="w-full mt-2 px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all">
                    </div>
                    <div class="flex gap-3">
                        <button onclick="document.getElementById('deleteAccountModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-all">
                            Cancel
                        </button>
                        <button class="flex-1 px-4 py-2 bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white rounded-lg font-medium transition-all">
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <!-- Edit Profile Modal -->
            <div id="editProfileModal" class="hidden fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                <div class="bg-slate-800 rounded-2xl border border-slate-700 p-8 max-w-md w-full shadow-2xl">
                    <h3 class="text-2xl font-bold text-white mb-6">Edit Profile</h3>
                    <form id="editProfileForm" onsubmit="handleProfileUpdate(event)">
                        @csrf
                        @method('PATCH')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Full Name</label>
                                <input type="text" name="name" value="{{ auth()->user()->name ?? '' }}" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all" required>
                                <span class="text-red-400 text-sm hidden" id="nameError"></span>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Email Address</label>
                                <input type="email" name="email" value="{{ auth()->user()->email }}" class="w-full px-4 py-2 bg-slate-900/50 border border-slate-600 rounded-lg text-white placeholder-slate-500 focus:border-cyan-500 focus:ring-1 focus:ring-cyan-500 transition-all" required>
                                <span class="text-red-400 text-sm hidden" id="emailError"></span>
                            </div>
                        </div>
                        <div class="flex gap-3">
                            <button type="button" onclick="document.getElementById('editProfileModal').classList.add('hidden')" class="flex-1 px-4 py-2 bg-slate-700 hover:bg-slate-600 text-white rounded-lg font-medium transition-all">
                                Cancel
                            </button>
                            <button type="submit" class="flex-1 px-4 py-2 bg-gradient-to-r from-cyan-600 to-cyan-700 hover:from-cyan-700 hover:to-cyan-800 text-white rounded-lg font-medium transition-all" id="saveProfileBtn">
                                Save Changes
                            </button>
                        </div>
                    </form>
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
            document.body.dispatchEvent(new CustomEvent('theme-changed', { detail: theme, bubbles: true }));
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

        // Handle profile update form submission
        function handleProfileUpdate(event) {
            event.preventDefault();
            const form = document.getElementById('editProfileForm');
            const formData = new FormData(form);
            const saveBtn = document.getElementById('saveProfileBtn');
            const originalBtnText = saveBtn.textContent;
            
            // Disable button and show loading state
            saveBtn.disabled = true;
            saveBtn.textContent = 'Saving...';

            fetch('{{ route("profile.update") }}', {
                method: 'POST',
                credentials: 'same-origin',
                body: formData,
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(JSON.stringify(data.errors || { message: 'Update failed' }));
                    });
                }
                return response.json();
            })
            .then(data => {
                // Update the displayed name and email on the page
                const nameInput = form.querySelector('input[name="name"]');
                const emailInput = form.querySelector('input[name="email"]');
                
                // Find and update the display areas
                const nameDisplays = document.querySelectorAll('.account-name-display');
                const emailDisplays = document.querySelectorAll('.account-email-display');
                
                nameDisplays.forEach(el => el.textContent = nameInput.value);
                emailDisplays.forEach(el => el.textContent = emailInput.value);
                
                showSuccessMessage('Profile updated successfully!');
                document.getElementById('editProfileModal').classList.add('hidden');
            })
            .catch(error => {
                console.error('Error:', error);
                const errorData = JSON.parse(error.message);
                let errorMsg = 'Failed to update profile';
                if (errorData.errors) {
                    errorMsg = Object.values(errorData.errors)[0][0] || errorMsg;
                }
                showErrorMessage(errorMsg);
            })
            .finally(() => {
                saveBtn.disabled = false;
                saveBtn.textContent = originalBtnText;
            });
        }

        // Modal close handlers
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                document.getElementById('changePasswordModal').classList.add('hidden');
                document.getElementById('deleteAccountModal').classList.add('hidden');
                document.getElementById('editProfileModal').classList.add('hidden');
            }
        });

        // Close modals on background click
        document.getElementById('changePasswordModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('deleteAccountModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        document.getElementById('editProfileModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.classList.add('hidden');
            }
        });

        // Notification functions
        function showSuccessMessage(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg animate-pulse z-50';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        function showErrorMessage(message) {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-4 right-4 bg-red-600 text-white px-6 py-3 rounded-lg shadow-lg animate-pulse z-50';
            toast.textContent = message;
            document.body.appendChild(toast);
            setTimeout(() => toast.remove(), 3000);
        }

        // Request notification permission on load
        document.addEventListener('DOMContentLoaded', function() {
            if (Notification.permission === 'default') {
                Notification.requestPermission();
            }
        });
    </script>
</x-app-layout>

