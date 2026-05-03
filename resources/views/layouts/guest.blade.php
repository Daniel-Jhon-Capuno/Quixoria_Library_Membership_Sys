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

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="min-h-screen flex items-center justify-center px-4 py-12 sm:px-6 lg:px-8" 
             style="background: linear-gradient(135deg, rgb(15, 35, 60) 0%, rgb(5, 15, 30) 50%, rgb(20, 45, 70) 100%);">
            
            <div class="w-full max-w-md space-y-8">
                <!-- Logo & Branding -->
                <div class="mx-auto max-w-md w-24 h-24 rounded-2xl flex items-center justify-center mb-8 shadow-2xl"
                     style="background: linear-gradient(to bottom right, rgba(100, 200, 255, 0.3), rgba(0, 255, 200, 0.3)); border: 1px solid rgba(100, 200, 255, 0.2);">
                    <svg class="w-16 h-16 text-cyan-400 drop-shadow-lg" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                        <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
                    </svg>
                </div>

                <div class="bg-slate-900/90 backdrop-blur-xl shadow-2xl rounded-3xl border border-slate-700/50 p-10 space-y-6">
                    {{ $slot }}
                </div>

                <!-- Footer Links -->
                <div class="text-center space-y-4">
                    <div class="flex justify-center gap-8 text-sm">
                        @if (Route::has('password.request'))
                            <a href="{{ route('password.request') }}" class="group text-slate-400 hover:text-cyan-400 transition-colors font-medium">
                                <span>Forgot Password?</span>
                                <div class="h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform origin-center w-full mt-1"></div>
                            </a>
                        @endif
                        <a href="{{ route('register') }}" class="group text-slate-400 hover:text-cyan-400 transition-colors font-medium">
                            <span>Create Account</span>
                            <div class="h-0.5 bg-gradient-to-r from-transparent via-cyan-400 to-transparent scale-x-0 group-hover:scale-x-100 transition-transform origin-center w-full mt-1"></div>
                        </a>
                    </div>
                    <div class="text-xs text-slate-500">
                        © {{ date('Y') }} Quixoria. All rights reserved.
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

