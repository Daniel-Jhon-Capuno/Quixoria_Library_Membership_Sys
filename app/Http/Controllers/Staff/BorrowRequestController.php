<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\Reservation;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BorrowRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = BorrowRequest::with(['student', 'book', 'student.subscription.membershipTier']);

        // Status filter
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // For pending requests, sort by priority level DESC, then created_at ASC
        if ($request->status === 'pending' || !$request->has('status')) {
            $query->orderByRaw("
                CASE
                    WHEN status = 'pending' THEN 0
                    ELSE 1
                END,
                CASE
                    WHEN status = 'pending' THEN (
                        SELECT COALESCE(mt.priority_level, 0)
                        FROM subscriptions s
                        JOIN membership_tiers mt ON s.membership_tier_id = mt.id
                        WHERE s.user_id = borrow_requests.user_id
                        AND s.status = 'active'
                        AND s.expires_at > NOW()
                    )
                    ELSE 0
                END DESC,
                CASE
                    WHEN status = 'pending' THEN borrow_requests.created_at
                    ELSE borrow_requests.created_at
                END ASC
            ");
        } else {
            $query->orderBy('created_at', 'desc');
        }

        $borrowRequests = $query->paginate(20);

        return view('staff.borrow-requests.index', compact('borrowRequests'));
    }

    public function confirm($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book', 'student.subscription.membershipTier'])->findOrFail($id);

        // Validate status is pending
        if ($borrowRequest->status !== 'pending') {
            $message = 'Only pending requests can be confirmed.';
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        DB::beginTransaction();
        try {
            $student = $borrowRequest->student;
            $book = $borrowRequest->book;
            $tier = $student->subscription?->membershipTier;

            // 1. Check active subscription
            if (!$student->subscription || $student->subscription->expires_at <= now()) {
                $message = 'Student does not have an active subscription.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // 2. Check available copies
            $activeBorrows = BorrowRequest::where('book_id', $book->id)
                ->whereIn('status', ['active', 'overdue'])
                ->count();

            if ($activeBorrows >= $book->total_copies) {
                $message = 'No available copies of this book.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // 3. Check weekly borrow limit
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            $weeklyBorrows = BorrowRequest::where('user_id', $student->id)
                ->whereIn('status', ['active', 'returned', 'overdue'])
                ->whereBetween('borrowed_at', [$weekStart, $weekEnd])
                ->count();

            if ($weeklyBorrows >= $tier->borrow_limit_per_week) {
                $message = 'Student has reached their weekly borrow limit.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // 4. Check for overdue books
            $overdueCount = BorrowRequest::where('user_id', $student->id)
                ->whereIn('status', ['active', 'overdue'])
                ->where('due_at', '<', now())
                ->count();

            if ($overdueCount > 0) {
                $message = 'Student has overdue books and cannot borrow new books.';
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // All validations passed - confirm the request
            $borrowRequest->update([
                'status' => 'active',
                'handled_by' => Auth::id(),
                'borrowed_at' => now(),
                'due_at' => now()->addDays($tier->borrow_duration_days),
            ]);

            DB::commit();
            
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Borrow request confirmed successfully.']);
            }
            return back()->with('success', 'Borrow request confirmed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Failed to confirm borrow request: ' . $e->getMessage();
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->with('error', $message);
        }
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $borrowRequest = BorrowRequest::findOrFail($id);

        if ($borrowRequest->status !== 'pending') {
            $message = 'Only pending requests can be rejected.';
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 422);
            }
            return back()->with('error', $message);
        }

        $borrowRequest->update([
            'status' => 'rejected',
            'handled_by' => Auth::id(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Borrow request rejected successfully.']);
        }
        return back()->with('success', 'Borrow request rejected.');
    }

    public function checkIn($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book', 'student.subscription.membershipTier'])->findOrFail($id);

        if ($borrowRequest->status !== 'active' && $borrowRequest->status !== 'overdue') {
            return back()->with('error', 'Only active or overdue borrows can be checked in.');
        }

        DB::beginTransaction();
        try {
            $returnedAt = now();
            $updates = [
                'status' => 'returned',
                'returned_at' => $returnedAt,
            ];

            // Check if late and calculate late fee
            if ($returnedAt > $borrowRequest->due_at) {
                $daysLate = $returnedAt->diffInDays($borrowRequest->due_at);
                $lateFeePerDay = $borrowRequest->student->subscription->membershipTier->late_fee_per_day;
                $totalLateFee = $daysLate * $lateFeePerDay;

                $updates['late_fee_charged'] = $totalLateFee;

                // Create transaction for late fee
                Transaction::create([
                    'user_id' => $borrowRequest->user_id,
                    'borrow_request_id' => $borrowRequest->id,
                    'type' => 'late_fee',
                    'amount' => $totalLateFee,
                    'description' => "Late fee for {$daysLate} days",
                ]);
            }

            $borrowRequest->update($updates);

            // Trigger reservation check for this book
            $this->checkReservations($borrowRequest->book_id);

            DB::commit();
            return back()->with('success', 'Book checked in successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Failed to check in book: ' . $e->getMessage());
        }
    }

    private function checkReservations($bookId)
    {
        // Find the next reservation for this book
        $nextReservation = Reservation::where('book_id', $bookId)
            ->where('status', 'active')
            ->orderBy('created_at')
            ->first();

        if ($nextReservation) {
            // Notify the student that the book is now available
            // You could send a notification here
            // $nextReservation->student->notify(new BookAvailableNotification($nextReservation));
        }
    }
}