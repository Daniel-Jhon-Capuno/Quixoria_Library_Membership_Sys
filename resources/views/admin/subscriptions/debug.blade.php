@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <h2 class="text-2xl font-semibold">Debug: {{ $user->name }} ({{ $user->email }})</h2>

    <div class="mt-4 bg-white shadow rounded p-4">
        <h3 class="font-medium">Current subscription</h3>
        @if($current)
            <p>Status: <strong>{{ $current->status }}</strong></p>
            <p>Tier: <strong>{{ optional($current->membershipTier)->name ?? 'N/A' }}</strong></p>
            <p>Weekly limit: <strong>{{ optional($current->membershipTier)->borrow_limit_per_week ?? 'N/A' }}</strong></p>
            <p>Monthly allowance (info only): <strong>{{ optional($current->membershipTier)->books_per_month ?? 'N/A' }}</strong></p>
            <p>Ends at: <strong>{{ $current->ends_at }}</strong></p>
        @else
            <p>No active subscription found.</p>
        @endif
    </div>

    <div class="mt-4 bg-white shadow rounded p-4">
        <h3 class="font-medium">Weekly borrows</h3>
        <p>Count this week: <strong>{{ $weeklyBorrows }}</strong></p>
    </div>

    <div class="mt-4 bg-white shadow rounded p-4">
        <h3 class="font-medium">All subscriptions (newest first)</h3>
        <table class="w-full mt-2 table-auto">
            <thead>
                <tr>
                    <th class="text-left">ID</th>
                    <th class="text-left">Status</th>
                    <th class="text-left">Tier</th>
                    <th class="text-left">Weekly Limit</th>
                    <th class="text-left">Monthly (info)</th>
                    <th class="text-left">Created</th>
                </tr>
            </thead>
            <tbody>
                @foreach($subscriptions as $s)
                    <tr>
                        <td>{{ $s->id }}</td>
                        <td>{{ $s->status }}</td>
                        <td>{{ optional($s->membershipTier)->name }}</td>
                        <td>{{ optional($s->membershipTier)->borrow_limit_per_week }}</td>
                        <td>{{ optional($s->membershipTier)->books_per_month }}</td>
                        <td>{{ $s->created_at }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="mt-4 bg-white shadow rounded p-4">
        <h3 class="font-medium">All membership tiers</h3>
        <ul class="mt-2">
            @foreach($membershipTiers as $t)
                <li>{{ $t->name }} — weekly: {{ $t->borrow_limit_per_week }} (id: {{ $t->id }})</li>
            @endforeach
        </ul>
    </div>
</div>
@endsection
