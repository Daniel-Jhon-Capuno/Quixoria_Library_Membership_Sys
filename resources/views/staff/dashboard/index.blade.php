<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-100">Staff Dashboard</h1>
                <p class="text-sm mt-1 text-gray-400">Manage member requests and library operations</p>
            </div>
        </div>
    </x-slot>

    <div class="staff-dashboard space-y-8">
        {{-- Flash messages --}}
        @if(session('success'))
            <div class="rounded-2xl p-4 border border-emerald-500/30 bg-emerald-500/10 text-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-2xl p-4 border border-red-500/30 bg-red-500/10 text-red-200">
                {{ session('error') }}
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-2xl p-4 border border-amber-500/30 bg-amber-500/10 text-amber-200">
                <ul class="list-disc pl-5">
                    @foreach($errors->all() as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="rounded-3xl border border-cyan-400/20 bg-gradient-to-r from-cyan-500/10 via-slate-900 to-blue-500/10 p-6 md:p-8">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <p class="text-xs uppercase tracking-[0.3em] text-cyan-300/80">Operations Center</p>
                    <h2 class="text-xl md:text-2xl font-bold text-white mt-2">Today at a Glance</h2>
                    <p class="text-slate-300 mt-1">Prioritize urgent items first, then clear pending requests.</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('staff.borrow-requests.index') }}" class="px-4 py-2 rounded-xl border border-cyan-400/40 bg-cyan-500/20 hover:bg-cyan-500/30 text-cyan-100 text-sm font-semibold transition">View Requests</a>
                    <a href="{{ route('staff.deadline-dashboard.index') }}" class="px-4 py-2 rounded-xl border border-amber-400/40 bg-amber-500/20 hover:bg-amber-500/30 text-amber-100 text-sm font-semibold transition">Deadline Board</a>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">
            <div class="rounded-2xl border border-amber-400/20 bg-slate-800/70 p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-amber-300/80">Pending</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $pendingCount ?? 0 }}</p>
                <p class="text-sm text-slate-400 mt-1">Awaiting approval</p>
            </div>
            <div class="rounded-2xl border border-cyan-400/20 bg-slate-800/70 p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-cyan-300/80">Active</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $activeCount ?? 0 }}</p>
                <p class="text-sm text-slate-400 mt-1">Currently borrowed</p>
            </div>
            <div class="rounded-2xl border border-red-400/20 bg-slate-800/70 p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-red-300/80">Overdue</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $overdueCount ?? 0 }}</p>
                <p class="text-sm text-slate-400 mt-1">Urgent attention needed</p>
            </div>
            <div class="rounded-2xl border border-blue-400/20 bg-slate-800/70 p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-blue-300/80">Due Today</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $dueTodayCount ?? 0 }}</p>
                <p class="text-sm text-slate-400 mt-1">Return deadline today</p>
            </div>
            <div class="rounded-2xl border border-purple-400/20 bg-slate-800/70 p-5">
                <p class="text-xs uppercase tracking-[0.2em] text-purple-300/80">Returns</p>
                <p class="text-3xl font-bold text-white mt-2">{{ $returnRequestsCount ?? 0 }}</p>
                <p class="text-sm text-slate-400 mt-1">Awaiting staff review</p>
            </div>
        </section>

        @if(($overdueBorrows ?? collect())->count() > 0)
            <section data-overdue-list class="rounded-3xl border border-red-500/20 bg-red-500/5 overflow-hidden">
                <div class="px-6 py-4 border-b border-red-500/20 flex items-center justify-between gap-3">
                    <h3 class="text-lg font-semibold text-white">Overdue Items</h3>
                    <span class="text-xs font-semibold uppercase tracking-[0.2em] text-red-300">High Priority</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[700px]">
                        <thead class="bg-slate-900/50">
                            <tr>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-slate-400">Member</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-slate-400">Book</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-slate-400">Due Date</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-slate-400">Days Overdue</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-slate-400">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/60">
                            @foreach($overdueBorrows as $borrow)
                                <tr class="bg-slate-900/20 hover:bg-slate-800/40 transition">
                                    <td class="py-3 px-4 text-gray-100">{{ $borrow->student->name ?? $borrow->user->name ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-slate-300">{{ $borrow->book->title ?? 'N/A' }}</td>
                                    <td class="py-3 px-4 text-slate-300">{{ optional($borrow->due_at)->format('M d, Y') ?? 'N/A' }}</td>
                                    <td class="py-3 px-4">
                                        <span class="bg-red-500/20 text-red-200 px-2.5 py-1 rounded-full text-xs font-semibold">{{ now()->diffInDays($borrow->due_at) }} days</span>
                                    </td>
                                    <td class="py-3 px-4">
                                        <form method="POST" action="{{ route('staff.deadline-dashboard.ping', $borrow->id) }}" class="inline">
                                            @csrf
                                            <button type="submit" data-confirm="Send reminder to this student?" class="px-3 py-1.5 rounded-lg border border-red-400/30 bg-red-500/20 hover:bg-red-500/30 text-red-100 text-xs font-semibold transition">Send Reminder</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </section>
        @endif

        @include('staff.dashboard.return-requests')
        @include('staff.borrow-requests.partials.reject-return-modal')

        <section class="grid grid-cols-1 xl:grid-cols-3 gap-6">
            <div class="xl:col-span-2 rounded-3xl border border-slate-700/70 bg-slate-800/60 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-700/70 flex items-center justify-between">
                    <h3 class="font-semibold text-gray-100">Pending Borrow Requests</h3>
                    <span class="text-xs text-slate-400">{{ collect($pendingRequests ?? [])->count() }} waiting</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[760px]">
                        <thead class="bg-slate-900/40">
                            <tr>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-gray-400">Member</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-gray-400">Tier</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-gray-400">Book</th>
                                <th class="text-left font-medium text-xs uppercase tracking-wider py-3 px-4 text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/60">
                            @forelse($pendingRequests ?? [] as $borrowRequest)
                                <tr class="hover:bg-slate-700/30 transition">
                                    <td class="py-3 px-4 text-gray-100">{{ data_get($borrowRequest, 'student.name', data_get($borrowRequest, 'user.name', 'N/A')) }}</td>
                                    <td class="py-3 px-4">
                                        <span class="bg-slate-700 text-gray-100 px-2.5 py-1 rounded-full text-xs font-medium">
                                            {{ data_get($borrowRequest, 'student.subscription.membershipTier.name', data_get($borrowRequest, 'user.subscription.membershipTier.name', 'None')) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-4 text-gray-300">{{ data_get($borrowRequest, 'book.title', 'N/A') }}</td>
                                    <td class="py-3 px-4">
                                        <div class="flex items-center gap-3 text-sm">
                                            <a href="{{ route('staff.borrow-requests.confirm.form', data_get($borrowRequest, 'id')) }}" class="px-3 py-1.5 rounded-lg bg-emerald-500/20 border border-emerald-400/30 text-emerald-200 hover:bg-emerald-500/30 transition font-medium">Approve</a>
                                            <a href="{{ route('staff.borrow-requests.reject.form', data_get($borrowRequest, 'id')) }}" class="px-3 py-1.5 rounded-lg bg-red-500/20 border border-red-400/30 text-red-200 hover:bg-red-500/30 transition font-medium">Reject</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center py-8 text-gray-400">No pending requests</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-700/70 bg-slate-800/60 p-6">
                    <h4 class="font-semibold mb-4 text-gray-100">Quick Actions</h4>
                    <div class="space-y-3">
                        <a href="{{ route('staff.borrow-requests.index') }}" class="block w-full px-4 py-2.5 bg-cyan-500/20 hover:bg-cyan-500/30 border border-cyan-400/30 rounded-xl text-cyan-100 font-semibold transition text-center text-sm">View All Requests</a>
                        <button type="button" class="block w-full px-4 py-2.5 bg-red-500/20 hover:bg-red-500/30 border border-red-400/30 rounded-xl text-red-100 font-semibold transition text-center text-sm" onclick="document.querySelector('[data-overdue-list]')?.scrollIntoView({behavior: 'smooth'})">Jump to Overdue List</button>
                        <a href="{{ route('staff.deadline-dashboard.index') }}" class="block w-full px-4 py-2.5 bg-amber-500/20 hover:bg-amber-500/30 border border-amber-400/30 rounded-xl text-amber-100 font-semibold transition text-center text-sm">Open Deadline Dashboard</a>
                    </div>
                </div>

                @if(($dueTodayBorrows ?? collect())->count() > 0)
                    <div class="rounded-3xl border border-blue-400/20 bg-blue-500/5 p-6">
                        <h4 class="text-white font-semibold mb-4">Returns Due Today</h4>
                        <div class="space-y-3 max-h-80 overflow-y-auto pr-1">
                            @foreach($dueTodayBorrows as $borrow)
                                <div class="rounded-xl border border-slate-700 bg-slate-800/70 p-4">
                                    <p class="text-xs text-gray-400">{{ $borrow->student->name ?? $borrow->user->name ?? 'N/A' }}</p>
                                    <p class="text-gray-100 font-semibold mt-1">{{ $borrow->book->title ?? 'N/A' }}</p>
                                    <p class="text-xs text-blue-300 mt-1">Due today</p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>
    </div>
</x-app-layout>