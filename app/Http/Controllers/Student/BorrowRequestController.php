<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Notifications\StaffNewRequestNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Log;
use App\Events\BorrowUsageUpdated;

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

        // 1. Check active subscription (guard against null ends_at)
        $subscription = $user->subscription;
        if (!$subscription || ($subscription->ends_at && $subscription->ends_at->lte(now()))) {
            Log::warning('Student borrow request failed - no active subscription', ['user_id' => $user->id, 'book_id' => $bookId]);
            return redirect()->back()->with('error', 'You must have an active subscription to borrow books.');
        }

        // 2. Check book availability
        $activeBorrows = BorrowRequest::where('book_id', $bookId)
            ->whereIn('status', ['active', 'overdue'])
            ->count();

        $book = \App\Models\Book::findOrFail($bookId);
        if ($activeBorrows >= $book->total_copies) {
            Log::warning('Student borrow request failed - no available copies', ['user_id' => $user->id, 'book_id' => $bookId, 'activeBorrows' => $activeBorrows, 'total_copies' => $book->total_copies]);
            return redirect()->back()->with('error', 'This book is currently unavailable.');
        }

        // 3. Check weekly borrow limit - use created_at for pending requests
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $tier = $subscription->membershipTier;
        if ($weeklyBorrows >= $tier->borrow_limit_per_week) {
            Log::warning('Student borrow request failed - weekly limit reached', ['user_id' => $user->id, 'book_id' => $bookId, 'weeklyBorrows' => $weeklyBorrows, 'limit' => $tier->borrow_limit_per_week]);
            return redirect()->back()->with('error', 'You have reached your weekly borrow limit of ' . $tier->borrow_limit_per_week . ' books.');
        }

        // (monthly limits removed — enforcement is weekly-only)

        // 4. Check for overdue books
        $overdueCount = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->where('due_at', '<', now())
            ->count();

        if ($overdueCount > 0) {
            Log::warning('Student borrow request failed - overdue books', ['user_id' => $user->id, 'book_id' => $bookId, 'overdueCount' => $overdueCount]);
            return redirect()->back()->with('error', 'You have overdue books. Please return them before borrowing new books.');
        }

        // 5. Check for existing pending/active request for same book
        $existingRequest = BorrowRequest::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'active', 'overdue'])
            ->first();

        if ($existingRequest) {
            Log::warning('Student borrow request failed - existing request', ['user_id' => $user->id, 'book_id' => $bookId, 'existing_request_id' => $existingRequest->id]);
            return redirect()->back()->with('error', 'You already have an active request for this book.');
        }

        // All checks passed - create the borrow request
        $borrowRequest = BorrowRequest::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'status' => 'pending',
        ]);

        // Notify all staff members (synchronously)
        $staffMembers = \App\Models\User::where('role', 'staff')->get();
        foreach ($staffMembers as $staff) {
            $staff->notifyNow(new StaffNewRequestNotification($borrowRequest));
        }

        // Broadcast updated usage to the student so UI updates in real-time
        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $tierLimit = $subscription->membershipTier->borrow_limit_per_week;

        $event = new BorrowUsageUpdated($user->id, $weeklyBorrows, $tierLimit);
        event($event);

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

    // Return a printable receipt for a borrow request
    public function receipt($id)
    {
        $user = Auth::user();
        try {
            $borrowRequest = BorrowRequest::with(['book', 'student', 'handler'])->findOrFail($id);

            // Authorization: allow if owner or staff/admin
            if ($user->role === 'student' && $borrowRequest->user_id !== $user->id) {
                abort(403);
            }

            return view('student.borrow-requests.receipt', compact('borrowRequest'));
        } catch (\Throwable $e) {
            Log::error('Failed to render receipt', ['exception' => $e->getMessage(), 'borrow_request_id' => $id, 'user_id' => $user->id]);
            abort(500, 'Failed to render receipt.');
        }
    }

    // List all receipts for the authenticated student
    public function receipts(Request $request)
    {
        $user = Auth::user();

        $query = BorrowRequest::with(['book', 'handler'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'returned', 'overdue'])
            ->orderBy('borrowed_at', 'desc');

        $borrowRequests = $query->paginate(12)->withQueryString();

        return view('student.borrow-requests.receipts', compact('borrowRequests'));
    }
}