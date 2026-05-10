@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-2xl p-8">
            <!-- Header -->
            <div class="text-center mb-8 pb-8 border-b border-gray-200 dark:border-slate-700">
                <h1 class="text-3xl font-bold dark:text-white">QUIXORIA LIBRARY</h1>
                <p class="text-gray-600 dark:text-slate-400 mt-2">CASE RESOLUTION RECEIPT</p>
            </div>

            <!-- Receipt Details -->
            <div class="space-y-6 mb-8">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-slate-400">Receipt #</p>
                        <p class="font-bold dark:text-white">{{ $resolution->receipt_number }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-slate-400">Date</p>
                        <p class="font-bold dark:text-white">{{ $resolution->created_at->format('M d, Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-slate-400">Resolved by</p>
                        <p class="font-bold dark:text-white">{{ $resolution->admin->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600 dark:text-slate-400">Payment Method</p>
                        <p class="font-bold dark:text-white capitalize">{{ $resolution->method }}</p>
                    </div>
                </div>

                <!-- Student Info -->
                <div class="bg-gray-50 dark:bg-slate-900/50 p-4 rounded-lg">
                    <p class="text-sm text-gray-600 dark:text-slate-400 mb-2">Student</p>
                    <p class="font-bold dark:text-white">{{ $resolution->borrowRequest->student->name }}</p>
                </div>

                <!-- Book Info -->
                <div>
                    <p class="text-sm text-gray-600 dark:text-slate-400 mb-2">Book</p>
                    <p class="font-bold dark:text-white">{{ $resolution->borrowRequest->book->title }}</p>
                    <p class="text-sm text-gray-600 dark:text-slate-400">Days Overdue: {{ $resolution->borrowRequest->due_at->diffInDays($resolution->borrowRequest->returned_at) }}</p>
                </div>

                <!-- Payment Breakdown -->
                <div class="border-t border-gray-200 dark:border-slate-700 pt-4">
                    <p class="text-sm text-gray-600 dark:text-slate-400 mb-3 font-semibold">Payment Breakdown</p>
                    <div class="space-y-2 mb-4 pb-4 border-b border-gray-200 dark:border-slate-700">
                        <div class="flex justify-between">
                            <span class="dark:text-slate-300">Late Fees:</span>
                            <span class="font-bold dark:text-white">₱{{ (($resolution->borrowRequest->late_fee_charged ?? 0) / 100) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="dark:text-slate-300">Replacement:</span>
                            <span class="font-bold dark:text-white">₱{{ (($resolution->borrowRequest->replacement_fee_cents ?? 0) / 100) }}</span>
                        </div>
                        <div class="flex justify-between text-lg">
                            <span class="font-bold dark:text-white">TOTAL PAID:</span>
                            <span class="font-bold text-green-600">₱{{ ($resolution->amount_cents / 100) }}</span>
                        </div>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-700/50 p-4 rounded-lg">
                    <p class="text-green-900 dark:text-green-300 font-bold">Account Status: CLEARED ✅</p>
                </div>

                <!-- Notes -->
                @if($resolution->notes)
                    <div>
                        <p class="text-sm text-gray-600 dark:text-slate-400 mb-2">Notes</p>
                        <p class="dark:text-slate-300">{{ $resolution->notes }}</p>
                    </div>
                @endif

                <!-- Library Address -->
                <div class="bg-gray-50 dark:bg-slate-900/50 p-4 rounded-lg text-sm text-gray-600 dark:text-slate-400">
                    <p class="font-bold mb-2">📍 QUIXORIA LIBRARY</p>
                    <p>Ground Floor, City Library and</p>
                    <p>Information Center building,</p>
                    <p>203 C. Bangoy St,</p>
                    <p>Davao City 8000</p>
                </div>
            </div>

            <!-- Print Button -->
            <div class="text-center pt-8 border-t border-gray-200 dark:border-slate-700">
                <button onclick="window.print()" class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                    🖨️ Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>
@endsection
