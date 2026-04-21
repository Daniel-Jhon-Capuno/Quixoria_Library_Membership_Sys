<nav x-data="{ open: false, showNotifications: false }" class="bg-slate-950/95 border-b border-slate-800 backdrop-blur-xl sticky top-0 z-40">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">
            <div class="flex items-center gap-4">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
                        <div class="h-10 w-10 rounded-2xl bg-gradient-to-br from-cyan-400 to-violet-500 flex items-center justify-center shadow-lg shadow-cyan-500/20">
                            <span class="text-sm font-black text-slate-950">L</span>
                        </div>
                        <span class="text-white font-semibold tracking-wide">LibrarySYS</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-4 sm:flex items-center">
                    @if(auth()->user()->role === 'student')
                        <x-nav-link :href="route('student.dashboard.index')" :active="request()->routeIs('student.dashboard.*')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @elseif(auth()->user()->role === 'staff')
                        <x-nav-link :href="route('staff.dashboard.index')" :active="request()->routeIs('staff.dashboard.*')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @elseif(auth()->user()->role === 'admin')
                        <x-nav-link :href="route('admin.dashboard.index')" :active="request()->routeIs('admin.dashboard.*')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @else
                        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                            {{ __('Dashboard') }}
                        </x-nav-link>
                    @endif

                    @if(auth()->user()->role === 'student')
                        <x-nav-link :href="route('student.book-catalog.index')" :active="request()->routeIs('student.book-catalog.*')">
                            {{ __('Books') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.receipts.index')" :active="request()->routeIs('student.receipts.*')">
                            {{ __('Receipts') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.active-borrows.index')" :active="request()->routeIs('student.active-borrows.*')">
                            {{ __('My Books') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.subscription.index')" :active="request()->routeIs('student.subscription.*')">
                            {{ __('Subscription') }}
                        </x-nav-link>
                        <x-nav-link :href="route('student.borrow-requests.index')" :active="request()->routeIs('student.borrow-requests.*')">
                            {{ __('My Requests') }}
                        </x-nav-link>
                    @endif
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                @include('partials.notifications')

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-slate-700 text-sm leading-4 font-medium rounded-full text-slate-100 bg-slate-900 hover:bg-slate-800 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        @if(auth()->user()->role === 'student')
                            <x-dropdown-link :href="route('student.receipts.index')">
                                {{ __('Receipts') }}
                            </x-dropdown-link>
                        @endif

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-full text-slate-300 hover:text-white hover:bg-slate-800 focus:outline-none transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-950/95 border-t border-slate-800">
        <div class="pt-2 pb-3 space-y-1 px-4">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
            @if(auth()->user()->role === 'student')
                <x-responsive-nav-link :href="route('student.book-catalog.index')" :active="request()->routeIs('student.book-catalog.*')">
                    {{ __('Books') }}
                </x-responsive-nav-link>
                    <x-responsive-nav-link :href="route('student.receipts.index')" :active="request()->routeIs('student.receipts.*')">
                        {{ __('Receipts') }}
                    </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.active-borrows.index')" :active="request()->routeIs('student.active-borrows.*')">
                    {{ __('My Books') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.subscription.index')" :active="request()->routeIs('student.subscription.*')">
                    {{ __('Subscription') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('student.borrow-requests.index')" :active="request()->routeIs('student.borrow-requests.*')">
                    {{ __('My Requests') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <div class="pt-4 pb-1 border-t border-slate-800 px-4">
            <div class="font-medium text-base text-slate-100">{{ Auth::user()->name }}</div>
            <div class="font-medium text-sm text-slate-400">{{ Auth::user()->email }}</div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
