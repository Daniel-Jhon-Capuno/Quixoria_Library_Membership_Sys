@extends('layouts.app')

@section('content')
<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-white mb-2">Escalation Dashboard</h1>
            <p class="text-slate-400">Manage overdue books and lost item cases</p>
        </div>

        <!-- Tab Navigation -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl mb-8">
            <div class="flex border-b border-slate-700/50 overflow-x-auto">
                <a href="?tab=lost" class="px-6 py-4 text-sm font-semibold whitespace-nowrap {{ $tab === 'lost' ? 'border-b-2 border-cyan-500 text-cyan-400' : 'text-slate-400 hover:text-slate-300' }}">
                    📚 Lost Books ({{ $counts['lost'] }})
                </a>
                <a href="?tab=severely_overdue" class="px-6 py-4 text-sm font-semibold whitespace-nowrap {{ $tab === 'severely_overdue' ? 'border-b-2 border-cyan-500 text-cyan-400' : 'text-slate-400 hover:text-slate-300' }}">
                    ⚠️ Severely Overdue ({{ $counts['severely_overdue'] }})
                </a>
                <a href="?tab=unresolved" class="px-6 py-4 text-sm font-semibold whitespace-nowrap {{ $tab === 'unresolved' ? 'border-b-2 border-cyan-500 text-cyan-400' : 'text-slate-400 hover:text-slate-300' }}">
                    🚨 Unresolved ({{ $counts['unresolved'] }})
                </a>
                <a href="?tab=presumed_lost" class="px-6 py-4 text-sm font-semibold whitespace-nowrap {{ $tab === 'presumed_lost' ? 'border-b-2 border-cyan-500 text-cyan-400' : 'text-slate-400 hover:text-slate-300' }}">
                    📕 Presumed Lost ({{ $counts['presumed_lost'] }})
                </a>
            </div>
        </div>

        <!-- Cases Table -->
        <div class="bg-slate-800/50 backdrop-blur-xl border border-slate-700/50 rounded-2xl overflow-hidden">
            @if($cases->count())
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-slate-900/50 border-b border-slate-700/50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Case ID</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Student</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Book</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Status</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Days Overdue</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Total Due</th>
                                <th class="px-6 py-4 text-left text-xs font-semibold text-slate-300">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-700/50">
                            @foreach($cases as $case)
                                <tr class="hover:bg-slate-700/30 transition-colors">
                                    <td class="px-6 py-4 text-sm text-slate-300">#{{ $case->id }}</td>
                                    <td class="px-6 py-4 text-sm text-white font-medium">{{ $case->student->name }}</td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ $case->book->title }}</td>
                                    <td class="px-6 py-4 text-sm">
                                        @if($case->status === 'lost')
                                            <span class="px-3 py-1 bg-red-900/30 text-red-300 rounded-full text-xs font-bold">Lost</span>
                                        @elseif($case->status === 'severely_overdue')
                                            <span class="px-3 py-1 bg-orange-900/30 text-orange-300 rounded-full text-xs font-bold">Severely Overdue</span>
                                        @elseif($case->status === 'unresolved')
                                            <span class="px-3 py-1 bg-red-900/50 text-red-200 rounded-full text-xs font-bold">Unresolved</span>
                                        @elseif($case->status === 'presumed_lost')
                                            <span class="px-3 py-1 bg-purple-900/30 text-purple-300 rounded-full text-xs font-bold">Presumed Lost</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-300">{{ $case->due_at->diffInDays(now()) }} days</td>
                                    <td class="px-6 py-4 text-sm font-semibold text-white">
                                        ₱{{ ((($case->late_fee_charged ?? 0) + ($case->replacement_fee_cents ?? 0)) / 100) }}
                                    </td>
                                    <td class="px-6 py-4 text-sm">
                                        <a href="{{ route('admin.escalations.show', $case->id) }}" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white rounded-lg font-medium transition-all">
                                            Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="px-6 py-4 border-t border-slate-700/50">
                    {{ $cases->links() }}
                </div>
            @else
                <div class="px-6 py-12 text-center">
                    <p class="text-slate-400 text-lg">No cases in this category.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
