<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-100 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Most Borrowed Books -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Most Borrowed Books</h3>
                        <p class="text-sm text-gray-300 mb-4">View books with highest borrow counts</p>
                        <a href="{{ route('admin.reports.most-borrowed-books') }}" class="btn-primary inline-flex items-center px-4 py-2">View Report</a>
                    </div>
                </div>

                <!-- Overdue Statistics -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Overdue Statistics</h3>
                        <p class="text-sm text-gray-300 mb-4">Overdue counts and averages by membership tier</p>
                        <a href="{{ route('admin.reports.overdue-statistics') }}" class="btn-primary inline-flex items-center px-4 py-2">View Report</a>
                    </div>
                </div>

                <!-- Subscription Revenue -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Subscription Revenue</h3>
                        <p class="text-sm text-gray-300 mb-4">Monthly revenue from subscriptions</p>
                        <a href="{{ route('admin.reports.subscription-revenue') }}" class="btn-primary inline-flex items-center px-4 py-2">View Report</a>
                    </div>
                </div>

                <!-- Student Activity -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Student Activity</h3>
                        <p class="text-sm text-gray-300 mb-4">Borrow activity and overdue rates per student</p>
                        <a href="{{ route('admin.reports.student-activity') }}" class="btn-primary inline-flex items-center px-4 py-2">View Report</a>
                    </div>
                </div>

                <!-- Staff Activity -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Staff Activity</h3>
                        <p class="text-sm text-gray-300 mb-4">Requests handled by staff members</p>
                        <a href="{{ route('admin.reports.staff-activity') }}" class="btn-primary inline-flex items-center px-4 py-2">View Report</a>
                    </div>
                </div>

                <!-- Audit Logs -->
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-lg font-medium text-gray-100 mb-4">Audit Logs</h3>
                        <p class="text-sm text-gray-300 mb-4">System activity and changes log</p>
                        <a href="{{ route('admin.reports.audit-logs') }}" class="btn-primary inline-flex items-center px-4 py-2">View Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>