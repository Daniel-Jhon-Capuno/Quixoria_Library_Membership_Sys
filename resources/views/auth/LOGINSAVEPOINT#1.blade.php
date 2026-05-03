
<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-8 text-cyan-400 bg-slate-900/30 p-4 rounded-2xl backdrop-blur-sm text-sm shadow-lg" :status="session('status')" />

    <div x-data="{ showPassword: false }">
        <form method="POST" action="{{ route('login') }}" class="space-y-6 bg-slate-900/40 backdrop-blur-xl rounded-3xl p-10 shadow-2xl border border-slate-700/30 max-w-md mx-auto">
            @csrf

            <!-- Email Address -->
            <div class="space-y-3">
                <label for="email" class="block text-sm font-semibold text-slate-300 tracking-tight">Email Address</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5 3a9 9 0 01-9-9"></path>
                        </svg>
                    </div>
                    <input id="email" class="block w-full pl-12 pr-4 py-5 bg-slate-900/90 hover:bg-slate-900 border border-slate-700/50 hover:border-slate-600 focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500/50 rounded-2xl text-lg placeholder-slate-500 transition-all duration-300 shadow-xl backdrop-blur-xl" 
                           type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" 
                           placeholder="Enter your email">
                </div>
                <x-input-error :messages="$errors->get('email')" class="text-red-400 text-sm mt-1" />
            </div>

            <!-- Password -->
            <div class="space-y-3">
                <label for="password" class="block text-sm font-semibold text-slate-300 tracking-tight">Password</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input x-ref="pass" id="password" class="block w-full pl-12 pr-12 py-5 bg-slate-900/90 hover:bg-slate-900 border border-slate-700/50 hover:border-slate-600 focus:ring-2 focus:ring-cyan-500/30 focus:border-cyan-500/50 rounded-2xl text-lg placeholder-slate-500 transition-all duration-300 shadow-xl backdrop-blur-xl" 
                           type="password" name="password" required autocomplete="current-password" 
                           placeholder="Enter your password">
                    <button type="button" @click="showPassword = !showPassword; $refs.pass.type = showPassword ? 'text' : 'password'" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <svg class="w-5 h-5 text-slate-500" x-show="!showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        <svg class="w-5 h-5 text-slate-500" x-show="showPassword" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 11-4.243 4.243L12 18a3 3 0 01-3.175-3.175"></path>
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="text-red-400 text-sm mt-1" />
            </div>

            <!-- Remember Me -->
            <div class="flex items-center mb-8">
                <label for="remember_me" class="flex items-center group cursor-pointer">
                    <input id="remember_me" type="checkbox" name="remember" class="rounded border-slate-700 bg-slate-900 text-cyan-500 focus:ring-cyan-500 h-5 w-5 shadow-sm transition-all duration-200 hover:scale-105">
                    <span class="ml-3 text-sm text-slate-400 font-medium select-none">Remember me</span>
                </label>
            </div>

            <div class="space-y-4">
                <button type="submit" class="group w-full bg-gradient-to-r from-cyan-600 to-blue-600 hover:from-cyan-500 hover:to-blue-500 shadow-xl hover:shadow-2xl transform hover:-translate-y-1 transition-all duration-300 text-lg py-6 rounded-3xl font-bold tracking-tight relative overflow-hidden">
                    <span class="relative z-10">Log in</span>
                    <div class="absolute inset-0 bg-gradient-to-r from-cyan-400 to-blue-400 opacity-0 group-hover:opacity-100 transition-opacity duration-300 blur-sm scale-110"></div>
                </button>

                <div class="text-center pt-6">
                    @if (Route::has('register'))
                        <p class="text-sm text-slate-500 mt-6">
                            Don't have an account?
                            <a href="{{ route('register') }}" class="font-semibold text-cyan-400 hover:text-cyan-300 transition-colors">Create one</a>
                        </p>
                    @endif
                </div>
            </div>
        </form>
    </div>
</x-guest-layout>

