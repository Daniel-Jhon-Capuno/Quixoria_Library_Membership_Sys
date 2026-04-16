<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Count of pending borrow requests
        $pendingCount = BorrowRequest::where('status', 'pending')->count();

        // Count of active borrows
        $activeCount = BorrowRequest::where('status', 'approved')->count();

        // Count of overdue borrows
        $overdueCount = BorrowRequest::where('status', 'approved')
            ->where('due_at', '<', now())
            ->count();

        // Count of borrows due today
        $dueTodayCount = BorrowRequest::where('status', 'approved')
            ->whereDate('due_at', today())
            ->count();

        // Get overdue borrows (latest first)
        $overdueBorrows = BorrowRequest::where('status', 'approved')
            ->where('due_at', '<', now())
            ->with(['user', 'book'])
            ->latest('due_at')
            ->get();

        // Get pending requests (latest first)
        $pendingRequests = BorrowRequest::where('status', 'pending')
            ->with(['user', 'book', 'user.subscription.membershipTier'])
            ->latest('created_at')
            ->get();

        // Get borrows due today
        $dueTodayBorrows = BorrowRequest::where('status', 'approved')
            ->whereDate('due_at', today())
            ->with(['user', 'book'])
            ->get();

        return view('staff.dashboard.index', compact(
            'pendingCount',
            'activeCount',
            'overdueCount',
            'dueTodayCount',
            'overdueBorrows',
            'pendingRequests',
            'dueTodayBorrows'
        ));
    }

    public function confirm(Request $request, $id)
    {
        $borrowRequest = BorrowRequest::findOrFail($id);
        $borrowRequest->update(['status' => 'approved']);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Request confirmed successfully']);
        }

        return back()->with('success', 'Borrow request confirmed successfully');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|min:10|max:500',
        ]);

        $borrowRequest = BorrowRequest::findOrFail($id);
        $borrowRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Request rejected successfully']);
        }

        return back()->with('success', 'Borrow request rejected successfully');
    }
}
