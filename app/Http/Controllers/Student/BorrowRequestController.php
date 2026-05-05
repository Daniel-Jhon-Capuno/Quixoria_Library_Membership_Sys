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


        if ($user->is_restricted) {
            return redirect()->back()->with('error', 'Your account is restricted. You cannot borrow books. Please visit the branch at Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City to resolve this.');
        }

        $subscription = $user->subscription;
        if (!$subscription || ($subscription->ends_at && $subscription->ends_at->lte(now()))) {
            Log::warning('Student borrow request failed - no active subscription', ['user_id' => $user->id, 'book_id' => $bookId]);
            return redirect()->back()->with('error', 'You must have an active subscription to borrow books.');
        }

        $activeBorrows = BorrowRequest::where('book_id', $bookId)
            ->whereIn('status', ['active', 'overdue'])
            ->count();

        $book = \App\Models\Book::findOrFail($bookId);
        if ($activeBorrows >= $book->total_copies) {
            Log::warning('Student borrow request failed - no available copies', ['user_id' => $user->id, 'book_id' => $bookId]);
            return redirect()->back()->with('error', 'This book is currently unavailable.');
        }

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();

        $tier = $subscription->membershipTier;
        if ($weeklyBorrows >= $tier->borrow_limit_per_week) {
            Log::warning('Student borrow request failed - weekly limit reached', ['user_id' => $user->id, 'book_id' => $bookId]);
            return redirect()->back()->with('error', 'You have reached your weekly borrow limit of ' . $tier->borrow_limit_per_week . ' books.');
        }

        $overdueCount = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->where('due_at', '<', now())
            ->count();

        if ($overdueCount > 0) {
            Log::warning('Student borrow request failed - overdue books', ['user_id' => $user->id, 'book_id' => $bookId]);
            return redirect()->back()->with('error', 'You have overdue books. Please return them before borrowing new books.');
        }

        $existingRequest = BorrowRequest::where('user_id', $user->id)
            ->where('book_id', $bookId)
            ->whereIn('status', ['pending', 'active', 'overdue'])
            ->first();

        if ($existingRequest) {
            Log::warning('Student borrow request failed - existing request', ['user_id' => $user->id, 'book_id' => $bookId]);
            return redirect()->back()->with('error', 'You already have an active request for this book.');
        }

        $borrowRequest = BorrowRequest::create([
            'user_id' => $user->id,
            'book_id' => $bookId,
            'status' => 'pending',
        ]);

        $staffMembers = \App\Models\User::where('role', 'staff')->get();
        foreach ($staffMembers as $staff) {
            $staff->notifyNow(new StaffNewRequestNotification($borrowRequest));
        }

        $weekStart = now()->startOfWeek();
        $weekEnd = now()->endOfWeek();
        $weeklyBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
            ->whereBetween('created_at', [$weekStart, $weekEnd])
            ->count();
        $tierLimit = $subscription->membershipTier->borrow_limit_per_week;

        event(new BorrowUsageUpdated($user->id, $weeklyBorrows, $tierLimit));

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

    public function receipt($id)
    {
        $user = Auth::user();
        try {
            $borrowRequest = BorrowRequest::with(['book', 'student', 'handler'])->findOrFail($id);

            if ($user->role === 'student' && $borrowRequest->user_id !== $user->id) {
                abort(403);
            }

            return view('student.borrow-requests.receipt', compact('borrowRequest'));
        } catch (\Throwable $e) {
            Log::error('Failed to render receipt', ['exception' => $e->getMessage(), 'borrow_request_id' => $id, 'user_id' => $user->id]);
            abort(500, 'Failed to render receipt.');
        }
    }

    public function receipts(Request $request)
    {
        $user = Auth::user();

        $borrowRequests = BorrowRequest::with(['book', 'handler'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'returned', 'overdue'])
            ->orderBy('borrowed_at', 'desc')
            ->paginate(12)
            ->withQueryString();

        return view('student.borrow-requests.receipts', compact('borrowRequests'));
    }
}