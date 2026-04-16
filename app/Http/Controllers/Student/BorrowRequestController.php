<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Notifications\StaffNewRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class BorrowRequestController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = BorrowRequest::with(['book'])
            ->where('user_id', $user->id);

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $borrowRequests = $query->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('student.borrow-requests.index', compact('borrowRequests'));
    }

    public function store($bookId)
    {
        $user = Auth::user();

        // 1. Check active subscription
        if (!$user->subscription || $user->subscription->expires_at <= now()) {
            return redirect()->back()->with('error', 'You must have an active subscription to borrow books.');
        }

        // 2. Check book availability
        $activeBorrows = BorrowRequest::where('book_id', $bookId)
            ->whereIn('status', ['active', 'overdue'])
            ->count();

        $book = \App\Models\Book::findOrFail($bookId);
        if ($activeBorrows >= $book->total_copies) {
            return redirect()->back()->with('error', 'This book is currently unavailable.');
        }

        // 3. Check weekly borrow limit
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['active', 'returned', 'overdue'])
            ->whereBetween('borrowed_at', [$weekStart, $weekEnd])
            ->count();

        $tier = $user->subscription->membershipTier;
        if ($weeklyBorrows >= $tier->borrow_limit_per_week) {
            return redirect()->back()->with('error', 'You have reached your weekly borrow limit of ' . $tier->borrow_limit_per_week . ' books.');
        }

        // 4. Check for overdue books
        $overdueCount = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->where('due_at', '<', now())
            ->count();

        if ($overdueCount > 0) {
            return redirect()->back()->with('error', 'You have overdue books. Please return them before borrowing new books.');
        }

        // 5. Check for existing pending/active request for same book
        $existingRequest = BorrowRequest::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'confirmed', 'active', 'overdue'])
            ->first();

        if ($existingRequest) {
            return redirect()->back()->with('error', 'You already have an active request for this book.');
        }

        // All checks passed - create the borrow request
        $borrowRequest = BorrowRequest::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'status' => 'pending',
        ]);

        // Notify all staff members
        $staffMembers = \App\Models\User::where('role', 'staff')->get();
        Notification::send($staffMembers, new StaffNewRequestNotification($borrowRequest));

        return redirect()->back()->with('success', 'Your borrow request has been submitted successfully. Staff will review it shortly.');
    }

    public function destroy($id)
    {
        $user = Auth::user();

        $borrowRequest = BorrowRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->firstOrFail();

        if ($borrowRequest->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending requests can be cancelled.');
        }

        $borrowRequest->delete();

        return redirect()->back()->with('success', 'Your borrow request has been cancelled.');
    }
}