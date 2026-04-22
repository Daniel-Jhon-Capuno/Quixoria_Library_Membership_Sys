<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Overdue Statistics Report</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Date Range Filter -->
            <div class="bg-surface overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6">
                    <form method="GET" action="{{ route('admin.reports.overdue-statistics') }}" class="flex flex-wrap gap-4 items-end">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-300">Start Date</label>
                            <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-300">End Date</label>
                            <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="mt-1 block w-full bg-slate-900 border border-slate-700 text-gray-100 rounded-md shadow-sm">
                        </div>
                        <div>
                            <button type="submit" class="btn-primary inline-flex items-center px-4 py-2">Filter</button>
                        </div>
                        <div>
                            <a href="{{ route('admin.reports.overdue-statistics') }}" class="btn-secondary inline-flex items-center px-4 py-2">Clear Filters</a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Report Data -->
            <div class="bg-surface overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    <div class="mb-6">
                        <div class="flex items-center justify-between">
                            <h4 class="text-lg font-medium">Overdue by Tier</h4>
                            <div>
                                <a href="{{ route('admin.reports.export', ['report' => 'overdue-statistics', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'csv']) }}" class="inline-flex items-center px-3 py-1 bg-slate-800 text-white rounded">Export CSV</a>
                                <a href="{{ route('admin.reports.export', ['report' => 'overdue-statistics', 'start_date' => $startDate, 'end_date' => $endDate, 'format' => 'pdf']) }}" class="inline-flex items-center px-3 py-1 bg-slate-800 text-white rounded ml-2">Export PDF</a>
                            </div>
                        </div>
                        <canvas id="overdueChart" class="mt-4" height="120"></canvas>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-700">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Membership Tier</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Overdue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Average Days Overdue</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Total Users</th>
                                </tr>
                            </thead>
                            <tbody class="bg-transparent divide-y divide-slate-700">
                                @forelse($statistics as $stat)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-100">{{ $stat->tier_name }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $stat->total_overdue }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ number_format($stat->avg_days_overdue, 1) }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $stat->total_users }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-300">No data available for the selected period</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Back Button -->
            <div class="mt-6">
                <a href="{{ route('admin.reports.index') }}" class="btn-secondary inline-flex items-center px-4 py-2">Back to Reports</a>
            </div>
        </div>
    </div>
</x-app-layout>

@push('scripts')
<script>
    (function(){
        const labels = {!! json_encode($chartLabels ?? []) !!};
        const data = {!! json_encode($chartData ?? []) !!};
        const ctx = document.getElementById('overdueChart');
        if (!ctx) return;
        new Chart(ctx.getContext('2d'), {
            type: 'bar',
            data: { labels: labels, datasets: [{ label: 'Overdues', data: data, backgroundColor: 'rgba(239,68,68,0.8)' }] },
            options: { responsive: true, plugins: { legend: { display: false } } }
        });
    })();
</script>
@endpush