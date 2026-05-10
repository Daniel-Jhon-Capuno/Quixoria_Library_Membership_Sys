<div class="fixed bottom-0 left-0 right-0 z-50 lg:hidden">
    <nav class="bg-slate-900/95 backdrop-blur-xl border-t border-slate-700/50 px-2 safe-area-bottom">
        <div class="max-w-4xl mx-auto">
            <div class="flex items-center justify-between py-2">
                <a href="{{ route('student.dashboard.index') }}" class="flex-1 text-center py-2 px-1 {{ request()->routeIs('student.dashboard.*') ? 'text-cyan-400' : 'text-slate-300' }} hover:text-white">
                    <svg class="mx-auto w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3v18h18V3H3zm4 4h10v10H7V7z"/></svg>
                    <div class="text-xs">Home</div>
                </a>

                <a href="{{ route('student.book-catalog.index') }}" class="flex-1 text-center py-2 px-1 {{ request()->routeIs('student.book-catalog.*') ? 'text-cyan-400' : 'text-slate-300' }} hover:text-white">
                    <svg class="mx-auto w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 20l9-5-9-5-9 5 9 5z"/></svg>
                    <div class="text-xs">Browse</div>
                </a>

                <a href="{{ route('student.active-borrows.index') }}" class="flex-1 text-center py-2 px-1 {{ request()->routeIs('student.active-borrows.*') ? 'text-cyan-400' : 'text-slate-300' }} hover:text-white">
                    <svg class="mx-auto w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7h18M3 12h18M3 17h18"/></svg>
                    <div class="text-xs">My Borrows</div>
                </a>

                <a href="{{ route('student.subscription.index') }}" class="flex-1 text-center py-2 px-1 {{ request()->routeIs('student.subscription.*') ? 'text-cyan-400' : 'text-slate-300' }} hover:text-white">
                    <svg class="mx-auto w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c1.657 0 3-1.567 3-3.5S13.657 1 12 1 9 2.567 9 4.5 10.343 8 12 8zM21 21v-2a4 4 0 00-4-4H7a4 4 0 00-4 4v2"/></svg>
                    <div class="text-xs">Plan</div>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex-1 text-center py-2 px-1 {{ request()->routeIs('profile.*') ? 'text-cyan-400' : 'text-slate-300' }} hover:text-white">
                    <svg class="mx-auto w-6 h-6 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A11.955 11.955 0 0112 15c2.486 0 4.782.76 6.879 2.05M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    <div class="text-xs">Account</div>
                </a>
            </div>
        </div>
    </nav>
</div>
