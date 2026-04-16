<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Reports</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Most Borrowed Books -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Most Borrowed Books</h3>
                        <p class="text-sm text-gray-600 mb-4">View books with highest borrow counts</p>
                        <a href="{{ route('admin.reports.most-borrowed-books') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">View Report</a>
                    </div>
                </div>

                <!-- Overdue Statistics -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Overdue Statistics</h3>
                        <p class="text-sm text-gray-600 mb-4">Overdue counts and averages by membership tier</p>
                        <a href="{{ route('admin.reports.overdue-statistics') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">View Report</a>
                    </div>
                </div>

                <!-- Subscription Revenue -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Subscription Revenue</h3>
                        <p class="text-sm text-gray-600 mb-4">Monthly revenue from subscriptions</p>
                        <a href="{{ route('admin.reports.subscription-revenue') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">View Report</a>
                    </div>
                </div>

                <!-- Student Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Student Activity</h3>
                        <p class="text-sm text-gray-600 mb-4">Borrow activity and overdue rates per student</p>
                        <a href="{{ route('admin.reports.student-activity') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">View Report</a>
                    </div>
                </div>

                <!-- Staff Activity -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Staff Activity</h3>
                        <p class="text-sm text-gray-600 mb-4">Requests handled by staff members</p>
                        <a href="{{ route('admin.reports.staff-activity') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">View Report</a>
                    </div>
                </div>

                <!-- Audit Logs -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Audit Logs</h3>
                        <p class="text-sm text-gray-600 mb-4">System activity and changes log</p>
                        <a href="{{ route('admin.reports.audit-logs') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700">View Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>