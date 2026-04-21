@props(['user' => null])

<div class="fixed left-0 top-0 h-screen w-56 shadow-xl z-50" style="background: linear-gradient(to bottom, rgb(20, 45, 70), rgb(10, 20, 35)); overflow-y-auto;">
    <!-- Logo Area -->
    <div class="px-6 py-8 border-b" style="border-color: rgb(var(--border-primary)); border-color: rgba(40, 100, 150, 0.3);">
        <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: linear-gradient(to bottom right, rgba(100, 200, 255, 0.2), rgba(0, 255, 200, 0.2));">
                <svg class="w-6 h-6" style="color: rgb(var(--accent-primary));" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4z"></path>
                    <path fill-rule="evenodd" d="M3 10a1 1 0 011-1h12a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6z" clip-rule="evenodd"></path>
                </svg>
            </div>
            <div>
                <h2 class="font-bold text-lg" style="color: rgb(var(--text-primary));">LibraryHub</h2>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">Membership System</p>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="px-3 py-6 space-y-2 flex-1">
        @if(auth()->user()->role === 'admin')
            <x-sidebar-link href="{{ route('admin.dashboard.index') }}" icon="chart-bar" label="Dashboard" />
            <x-sidebar-link href="{{ route('admin.users.index') }}" icon="users" label="User Management" />
            <x-sidebar-link href="{{ route('admin.books.index') }}" icon="book-open" label="Books" />
            <x-sidebar-link href="{{ route('admin.tiers.index') }}" icon="star" label="Membership Tiers" />
            <x-sidebar-link href="{{ route('admin.subscriptions.index') }}" icon="shopping-cart" label="Subscriptions" />
            <x-sidebar-link href="{{ route('admin.reports.index') }}" icon="document-chart-bar" label="Reports" />
        @elseif(auth()->user()->role === 'staff')
            <x-sidebar-link href="{{ route('staff.dashboard.index') }}" icon="chart-bar" label="Dashboard" />
            <x-sidebar-link href="{{ route('staff.borrow-requests.index') }}" icon="inbox" label="Requests" />
            <x-sidebar-link href="{{ route('staff.deadline-dashboard.index') }}" icon="calendar" label="Deadline Dashboard" />
        @else
            <x-sidebar-link href="{{ route('student.dashboard.index') }}" icon="chart-bar" label="Dashboard" />
            <x-sidebar-link href="{{ route('student.book-catalog.index') }}" icon="book-open" label="Browse Books" />
            <x-sidebar-link href="{{ route('student.active-borrows.index') }}" icon="inbox" label="My Borrows" />
            <x-sidebar-link href="{{ route('student.subscription.index') }}" icon="star" label="My Subscription" />
            <x-sidebar-link href="{{ route('student.receipts.index') }}" icon="document-text" label="Receipts" />
        @endif
    </nav>

    <!-- User Info -->
    <div class="px-3 py-4 border-t" style="border-color: rgba(40, 100, 150, 0.3);">
        <button onclick="document.getElementById('profile-menu').classList.toggle('hidden')" 
                class="w-full flex items-center gap-3 p-3 rounded-lg transition" style="background: rgba(100, 200, 255, 0.1); border: 1px solid rgba(100, 200, 255, 0.2);">
            <div class="w-10 h-10 rounded-lg flex items-center justify-center flex-shrink-0" style="background: linear-gradient(to bottom right, rgba(100, 200, 255, 0.3), rgba(0, 255, 200, 0.3));">
                <span class="font-bold text-sm" style="color: rgb(var(--accent-primary));">{{ substr(auth()->user()->name, 0, 1) }}</span>
            </div>
            <div class="text-left flex-1 min-w-0">
                <p class="font-medium text-sm truncate" style="color: rgb(var(--text-primary));">{{ auth()->user()->name }}</p>
                <p class="text-xs" style="color: rgb(var(--text-secondary));">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
            <svg class="w-4 h-4 flex-shrink-0" style="color: rgb(var(--text-secondary));" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div id="profile-menu" class="hidden mt-2 space-y-1">
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded transition text-sm" style="color: rgb(var(--text-secondary)); background: rgba(100, 200, 255, 0.05);">
                Edit Profile
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded transition text-sm" style="color: rgb(var(--accent-primary)); background: rgba(0, 255, 200, 0.05);">
                    Logout
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Main content should have margin -->
<script>
    document.addEventListener('click', function(event) {
        const menu = document.getElementById('profile-menu');
        const button = event.target.closest('button');
        if (!button?.onclick) {
            menu?.classList.add('hidden');
        }
    });
</script>

