<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold" style="color: rgb(var(--text-primary));">Dashboard</h1>
                <p class="text-sm mt-1" style="color: rgb(var(--text-secondary));">Welcome back! Here's what's happening in your library.</p>
            </div>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 rounded-lg text-white font-medium transition bg-primary hover:bg-primary-dark">
                    Documentation
                </button>
                <button class="px-4 py-2 rounded-lg text-white font-medium transition bg-accent hover:bg-blue-600">
                    Download Report
                </button>
            </div>
        </div>
    </x-slot>

    <!-- Stats Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <x-stat-card 
            title="Total Books" 
            value="{{ $totalBooks ?? 0 }}"
            subtitle="Active in library"
            color="primary"
            :trend="['direction' => 'up', 'value' => '12']" />

        <x-stat-card 
            title="Active Members" 
            value="{{ $totalStudents ?? 0 }}"
            subtitle="With valid subscriptions"
            color="secondary"
            :trend="['direction' => 'up', 'value' => '8']" />

        <x-stat-card 
            title="Active Borrows" 
            value="{{ $pendingRequests ?? 0 }}"
            subtitle="Currently borrowed"
            color="accent"
            :trend="['direction' => 'up', 'value' => '5']" />

        <x-stat-card 
            title="Overdue Items" 
            value="{{ $overdueBorrows ?? 0 }}"
            subtitle="Require attention"
            color="danger"
            :trend="['direction' => 'up', 'value' => '3']" />
    </div>

    <!-- Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        <!-- Performance Chart -->
        <div>
            <x-chart-card title="System Performance" subtitle="Total Transactions Over Time" chartId="performanceChart">
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('performanceChart').getContext('2d');
                        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
                        gradient.addColorStop(0, 'rgba(100, 200, 255, 0.3)');
                        gradient.addColorStop(1, 'rgba(100, 200, 255, 0)');

                        new Chart(ctx, {
                            type: 'line',
                            data: {
                                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                                datasets: [{
                                    label: 'Transactions',
                                    data: [100, 70, 80, 75, 60, 85, 70, 65, 60, 75, 85, 100],
                                    borderColor: '#64c8ff',
                                    backgroundColor: gradient,
                                    borderWidth: 3,
                                    fill: true,
                                    tension: 0.4,
                                    pointRadius: 5,
                                    pointBackgroundColor: '#00ffc8',
                                    pointBorderColor: '#64c8ff',
                                    pointHoverRadius: 7,
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        labels: {
                                            color: '#c8f0ff',
                                            font: { size: 12 }
                                        }
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: { color: 'rgba(100, 150, 200, 0.1)' },
                                        ticks: { color: '#78b4dc' }
                                    },
                                    x: {
                                        grid: { display: false },
                                        ticks: { color: '#78b4dc' }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </x-chart-card>
        </div>

        <!-- Membership Tier Distribution Chart -->
        <div>
            <x-chart-card title="Member Distribution" subtitle="By Subscription Tier" chartId="tierChart">
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const ctx = document.getElementById('tierChart').getContext('2d');
                        new Chart(ctx, {
                            type: 'doughnut',
                            data: {
                                labels: ['Free', 'Premium', 'Platinum', 'Diamond'],
                                datasets: [{
                                    label: 'Members',
                                    data: [120, 250, 180, 95],
                                    backgroundColor: ['rgba(100, 200, 255, 0.8)', 'rgba(0, 255, 200, 0.8)', 'rgba(150, 50, 255, 0.8)', 'rgba(255, 200, 0, 0.8)'],
                                    borderColor: ['#64c8ff', '#00ffc8', '#9632ff', '#ffc800'],
                                    borderWidth: 2,
                                    hoverOffset: 4
                                }]
                            },
                            options: {
                                responsive: true,
                                maintainAspectRatio: true,
                                plugins: {
                                    legend: {
                                        display: true,
                                        position: 'bottom',
                                        labels: {
                                            color: '#c8f0ff',
                                            font: { size: 12 },
                                            padding: 15
                                        }
                                    }
                                }
                            }
                        });
                    });
                </script>
            </x-chart-card>
        </div>
    </div>

    <!-- Revenue and Stats Row -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <div class="rounded-xl p-6 shadow-card"
             style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
            <h3 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Revenue</h3>
            <p class="text-4xl font-bold mb-2" style="color: rgb(var(--accent-primary));">${{ number_format($monthlyRevenue ?? 0, 2) }}</p>
            <p class="text-sm mb-6" style="color: rgb(var(--text-secondary));">This month</p>
            <div class="space-y-3">
                <div class="flex justify-between items-center text-sm">
                    <span style="color: rgb(var(--text-secondary));">Subscriptions</span>
                    <span class="font-semibold" style="color: rgb(var(--text-primary));">{{ $totalActiveSubscriptions ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span style="color: rgb(var(--text-secondary));">Active Members</span>
                    <span class="font-semibold" style="color: rgb(var(--text-primary));">{{ $activeStudents ?? 0 }}</span>
                </div>
                <div class="flex justify-between items-center text-sm">
                    <span style="color: rgb(var(--text-secondary));">Available Books</span>
                    <span class="font-semibold" style="color: rgb(var(--text-primary));">{{ $availableBooks ?? 0 }}</span>
                </div>
            </div>
        </div>

        <div class="rounded-xl p-6 shadow-card"
             style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
            <h3 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Quick Stats</h3>
            <div class="space-y-4">
                <div>
                    <p class="text-sm" style="color: rgb(var(--text-secondary));">Books Borrowed Today</p>
                    <p class="text-2xl font-bold mt-1" style="color: rgb(var(--accent-secondary));">{{ rand(15, 45) }}</p>
                </div>
                <div>
                    <p class="text-sm" style="color: rgb(var(--text-secondary));">New Registrations</p>
                    <p class="text-2xl font-bold mt-1" style="color: rgb(var(--accent-secondary));">{{ rand(5, 15) }}</p>
                </div>
                <div>
                    <p class="text-sm" style="color: rgb(var(--text-secondary));">System Health</p>
                    <p class="text-2xl font-bold mt-1" style="color: rgb(var(--accent-primary));">99.8%</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl p-6 shadow-card"
             style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
            <h3 class="font-semibold mb-4" style="color: rgb(var(--text-primary));">Status</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <span style="color: rgb(var(--text-secondary));">Server Status</span>
                    <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: rgba(0, 255, 200, 0.2); color: rgb(var(--accent-primary));">Online</span>
                </div>
                <div class="flex items-center justify-between">
                    <span style="color: rgb(var(--text-secondary));">Database</span>
                    <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: rgba(100, 200, 255, 0.2); color: rgb(var(--accent-secondary));">Connected</span>
                </div>
                <div class="flex items-center justify-between">
                    <span style="color: rgb(var(--text-secondary));">API Response</span>
                    <span class="px-2 py-1 rounded text-xs font-medium" style="background-color: rgba(0, 255, 200, 0.2); color: rgb(var(--accent-primary));">Fast</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="rounded-xl p-6 shadow-card"
         style="background-color: rgb(var(--surface-primary)); border: 1px solid rgb(var(--border-primary));">
        <h3 class="font-semibold mb-6" style="color: rgb(var(--text-primary)); font-size: 1.125rem;">Recent Activity</h3>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr style="border-bottom-color: rgb(var(--border-primary));" class="border-b">
                        <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Member</th>
                        <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Action</th>
                        <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Item</th>
                        <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Date</th>
                        <th class="text-left font-medium text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestBorrowRequests ?? [] as $request)
                        <tr class="border-b transition" style="border-color: rgb(var(--border-primary) / 0.6); color: rgb(var(--text-primary));" class="hover:bg-dark-bg/50">
                            <td class="py-3 px-4">{{ $request->user->name ?? 'N/A' }}</td>
                            <td class="py-3 px-4" style="color: rgb(var(--text-secondary));">{{ ucfirst($request->status ?? 'Pending') }}</td>
                            <td class="py-3 px-4" style="color: rgb(var(--text-secondary));">{{ $request->book->title ?? 'N/A' }}</td>
                            <td class="text-sm py-3 px-4" style="color: rgb(var(--text-secondary));">{{ $request->created_at->format('M d') ?? 'N/A' }}</td>
                            <td class="py-3 px-4">
                                <span class="@if($request->status === 'approved') bg-secondary/20 text-secondary @elseif($request->status === 'pending') bg-accent/20 text-accent @else bg-danger/20 text-red-400 @endif px-2 py-1 rounded text-xs font-medium">
                                    {{ ucfirst($request->status ?? 'Unknown') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-6" style="color: rgb(var(--text-secondary));">No recent activity</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>