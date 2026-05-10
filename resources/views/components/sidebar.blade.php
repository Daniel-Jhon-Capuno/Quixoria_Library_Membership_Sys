@props(['user' => null])

<div x-data="{ profileOpen: false, sidebarCollapsed: false, toggleSidebar() { this.sidebarCollapsed = !this.sidebarCollapsed; localStorage.setItem('sidebarCollapsed', this.sidebarCollapsed); try { fetch('{{ route('user.preferences.sidebar-collapsed') }}', { method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }, body: JSON.stringify({ sidebar_collapsed: this.sidebarCollapsed }) }); } catch(e){} }, }"
    x-init="sidebarCollapsed = (localStorage.getItem('sidebarCollapsed') !== null ? (localStorage.getItem('sidebarCollapsed') === 'true') : {{ json_encode(auth()->user()->sidebar_collapsed ?? false) }})"
    x-bind:class="(sidebarCollapsed && window.innerWidth >= 1024) ? 'h-screen w-24 shadow-xl z-50 sidebar collapsed transition-all duration-300' : 'h-screen w-64 shadow-xl z-50 sidebar transition-all duration-300'" 
     style="background: rgb(var(--surface-primary)); overflow-y: auto;" 
     x-cloak
     @mouseenter="if(window.innerWidth >= 1024) sidebarCollapsed = false"
     @mouseleave="if(window.innerWidth >= 1024) sidebarCollapsed = true">
    
        <div class="px-4 py-6 border-b flex items-center cursor-pointer group" 
            style="border-color: rgba(40, 100, 150, 0.3);" 
            @click="if(window.innerWidth >= 1024) { toggleSidebar(); window.dispatchEvent(new CustomEvent('sidebar-toggled', { detail: sidebarCollapsed })); }">
        
                <div class="flex items-center gap-3 overflow-hidden flex-shrink-0">
                    <!-- Logo - Always left-aligned -->
                    <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 border border-slate-600/30" style="background-color: rgb(var(--surface-primary));">
                        <img src="/images/logo.png" alt="Quixoria" class="w-6 h-6 object-contain rounded">
                    </div>
            
            <!-- Text - Right side, only on expanded -->
            <div x-show="!sidebarCollapsed" x-transition.opacity class="flex flex-col min-w-0">
                <h2 class="font-bold text-lg group-hover:text-cyan-400" style="color: rgb(var(--text-primary));">Quixoria</h2>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">Reading adventure</p>
            </div>
        </div>
    </div>
    <style>
        /* On small screens, make the expanded sidebar take full width, but keep collapsed small */
        @media (max-width: 1023px) {
            .sidebar { left: 0 !important; top: 0 !important; }
            .sidebar:not(.collapsed) { width: 100% !important; }
            .sidebar.collapsed { width: 5rem !important; }
        }
    </style>

    <nav class="px-2 md:px-4 py-6 space-y-2 flex-1">
        @php $role = auth()->user()->role; @endphp

        @if($role === 'admin')
            <x-sidebar-link href="{{ route('admin.dashboard.index') }}" icon="chart-bar" label="Dashboard" />
            <x-sidebar-link href="{{ route('admin.users.index') }}" icon="users" label="User Management" />
            <x-sidebar-link href="{{ route('admin.books.index') }}" icon="book-open" label="Books" />
            <x-sidebar-link href="{{ route('admin.tiers.index') }}" icon="star" label="Membership Tiers" />
            @php
                $pendingSubsCount = \App\Models\Subscription::where('status', 'pending')->count();
                $isSubsActive = request()->routeIs('admin.subscriptions.*');
            @endphp
            <a href="{{ url('admin/subscriptions') }}" class="{{ $isSubsActive ? 'bg-slate-700/80 text-white border-l-2 border-cyan-400' : 'text-slate-300 hover:text-white' }} flex items-center gap-3 p-3 md:px-4 md:py-3 rounded-xl transition-all duration-200 hover:bg-slate-800/50 group relative overflow-hidden">
                <svg class="w-5 h-5 flex-shrink-0 opacity-75 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h18v13a2 2 0 01-2 2H5a2 2 0 01-2-2V3z"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10"></path>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity class="font-medium flex-1 whitespace-nowrap transition-colors">Subscriptions</span>
                {{-- compact pending badge (mini) to indicate there are pending items --}}
                @if($pendingSubsCount)
                    <span id="sidebarPendingMini" class="absolute top-3 right-3 inline-flex items-center justify-center w-4 h-4 rounded-full bg-yellow-600 text-white text-[10px] font-semibold">{{ $pendingSubsCount > 9 ? '9+' : $pendingSubsCount }}</span>
                @else
                    <span id="sidebarPendingMini" class="hidden"></span>
                @endif
            </a>
            {{-- Payments management link --}}
            <x-sidebar-link href="{{ route('admin.payments.index') }}" icon="credit-card" label="Payment Management" />
            {{-- Escalation Cases Management --}}
            @php
                $escalationCount = \App\Models\BorrowRequest::whereIn('status', ['lost', 'severely_overdue', 'unresolved', 'presumed_lost'])->count();
                $isEscalationActive = request()->routeIs('admin.escalations.*');
            @endphp
            <a href="{{ route('admin.escalations.index') }}" class="{{ $isEscalationActive ? 'bg-slate-700/80 text-white border-l-2 border-cyan-400' : 'text-slate-300 hover:text-white' }} flex items-center gap-3 p-3 md:px-4 md:py-3 rounded-xl transition-all duration-200 hover:bg-slate-800/50 group relative overflow-hidden">
                <svg class="w-5 h-5 flex-shrink-0 opacity-75 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4v2m0 4v2M6 7a3 3 0 110 6 3 3 0 010-6zm0 8a3 3 0 110 6 3 3 0 010-6zm12-8a3 3 0 110 6 3 3 0 010-6zm0 8a3 3 0 110 6 3 3 0 010-6z"></path>
                </svg>
                <span x-show="!sidebarCollapsed" x-transition.opacity class="font-medium flex-1 whitespace-nowrap transition-colors">Escalations</span>
                {{-- Badge showing number of open cases --}}
                @if($escalationCount)
                    <span class="absolute top-3 right-3 inline-flex items-center justify-center w-4 h-4 rounded-full bg-red-600 text-white text-[10px] font-semibold">{{ $escalationCount > 9 ? '9+' : $escalationCount }}</span>
                @else
                    <span class="hidden"></span>
                @endif
            </a>
            {{-- Pending submenu removed; use Subscriptions page to view pending items. --}}
            <x-sidebar-link href="{{ route('admin.reports.index') }}" icon="document-chart-bar" label="Reports" />
@elseif($role === 'staff')
    <x-sidebar-link href="{{ route('staff.dashboard.index') }}" icon="chart-bar" label="Dashboard" activePattern="staff.dashboard.*" />
    <x-sidebar-link href="{{ route('staff.borrow-requests.index') }}" icon="inbox" label="Requests" activePattern="staff.borrow-requests.*" />
    <x-sidebar-link href="{{ route('staff.deadline-dashboard.index') }}" icon="calendar" label="Deadline Dashboard" activePattern="staff.deadline-dashboard.*" />
    <x-sidebar-link href="{{ route('staff.appeals.index') }}" icon="document-chart-bar" label="Return Appeals" activePattern="staff.appeals.*" />
        @else
            <x-sidebar-link href="{{ route('student.dashboard.index') }}" icon="chart-bar" label="Dashboard" />
            <x-sidebar-link href="{{ route('student.book-catalog.index') }}" icon="book-open" label="Browse Books" />
            <x-sidebar-link href="{{ route('student.active-borrows.index') }}" icon="inbox" label="My Borrows" />
            <x-sidebar-link href="{{ route('student.subscription.index') }}" icon="star" label="My Subscription" />
            <x-sidebar-link href="{{ route('student.receipts.index') }}" icon="receipt" label="Receipts" />
        @endif
    </nav>

    <div class="px-3 py-4 border-t" style="border-color: rgba(40, 100, 150, 0.3);">
        <button @click="profileOpen = !profileOpen" 
            @click.outside="profileOpen = false"
            class="w-full flex items-center gap-3 p-2 md:px-3 md:py-2 rounded-lg transition hover:bg-slate-800/50" 
                :class="sidebarCollapsed ? 'justify-center p-2' : ''"
                style="background: rgba(100, 200, 255, 0.06); border: 1px solid rgba(100, 200, 255, 0.02);">
            
            <div :class="sidebarCollapsed ? 'mx-auto' : ''" class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0 bg-gradient-to-br from-slate-700/80 to-slate-800/60 border border-slate-600/50">
                <span class="w-6 h-6 font-bold text-sm flex items-center justify-center text-white leading-none tracking-tight uppercase">{{ strtoupper(substr(auth()->user()->name ?? 'User', 0, 2)) }}</span>
            </div>
            
            <div x-show="!sidebarCollapsed" x-transition.opacity class="flex-1 min-w-0">
                <p class="font-medium text-sm truncate" style="color: rgb(var(--text-primary));">{{ auth()->user()->name }}</p>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">{{ ucfirst(auth()->user()->role) }}</p>
            </div>

            <svg x-show="!sidebarCollapsed" 
                 class="w-4 h-4 flex-shrink-0 transition-transform duration-200" 
                 :class="profileOpen ? 'rotate-180' : ''"
                 style="color: rgb(var(--text-secondary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div x-show="profileOpen && !sidebarCollapsed" 
             x-transition 
             class="absolute bottom-full left-3 right-3 mb-2 space-y-1 bg-slate-900 rounded-lg border border-slate-700 p-2 shadow-2xl z-50">
            
            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 rounded-lg hover:bg-slate-800 transition text-sm flex items-center gap-2" style="color: rgb(var(--text-primary));">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <span>Edit Profile</span>
            </a>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 rounded-lg hover:bg-slate-800 transition text-sm flex items-center gap-2" style="color: rgb(var(--accent-primary));">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                    </svg>
                    <span>Logout</span>
                </button>
            </form>
        </div>
    </div>
</div>
