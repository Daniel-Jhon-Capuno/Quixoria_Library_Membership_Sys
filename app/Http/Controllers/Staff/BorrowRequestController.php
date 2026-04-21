<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\Reservation;
use App\Models\Transaction;
use App\Notifications\BorrowRequestConfirmedNotification;
use App\Notifications\BorrowRequestRejectedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\BorrowUsageUpdated;

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
                        AND s.ends_at > NOW()
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
        Log::info('Staff confirm called', ['id' => $id, 'user_id' => auth()->id()]);
        $borrowRequest = BorrowRequest::with(['student', 'book', 'student.subscription.membershipTier'])->findOrFail($id);
        Log::info('BorrowRequest loaded', ['borrowRequest_id' => $borrowRequest->id, 'status' => $borrowRequest->status]);

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
            if (!$student->subscription || $student->subscription->ends_at <= now()) {
                $message = 'Student does not have an active subscription.';
                Log::warning('Borrow confirm validation failed - subscription', ['borrow_request_id' => $borrowRequest->id, 'student_id' => $student->id, 'reason' => $message]);
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
                Log::warning('Borrow confirm validation failed - no copies', ['borrow_request_id' => $borrowRequest->id, 'book_id' => $book->id, 'activeBorrows' => $activeBorrows, 'total_copies' => $book->total_copies]);
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // 3. Check weekly borrow limit - count by created_at since borrowed_at is not set yet for pending
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            $weeklyBorrows = BorrowRequest::where('user_id', $student->id)
                ->whereIn('status', ['active', 'overdue', 'pending'])
                ->whereBetween('created_at', [$weekStart, $weekEnd])
                ->where('id', '!=', $borrowRequest->id)
                ->count();

            if ($weeklyBorrows >= $tier->borrow_limit_per_week) {
                $message = 'Student has reached their weekly borrow limit.';
                Log::warning('Borrow confirm validation failed - weekly limit', ['borrow_request_id' => $borrowRequest->id, 'student_id' => $student->id, 'weeklyBorrows' => $weeklyBorrows, 'limit' => $tier->borrow_limit_per_week]);
                if (request()->expectsJson()) {
                    return response()->json(['success' => false, 'message' => $message], 422);
                }
                return back()->with('error', $message);
            }

            // 3b. Check monthly borrow limit (if configured)
            if (!empty($tier->books_per_month)) {
                $monthStart = now()->startOfMonth();
                $monthEnd = now()->endOfMonth();
                $monthlyBorrows = BorrowRequest::where('user_id', $student->id)
                    ->whereIn('status', ['active', 'overdue', 'pending'])
                    ->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('id', '!=', $borrowRequest->id)
                    ->count();

                if ($monthlyBorrows >= $tier->books_per_month) {
                    $message = 'Student has reached their monthly borrow limit.';
                    Log::warning('Borrow confirm validation failed - monthly limit', ['borrow_request_id' => $borrowRequest->id, 'student_id' => $student->id, 'monthlyBorrows' => $monthlyBorrows, 'limit' => $tier->books_per_month]);
                    if (request()->expectsJson()) {
                        return response()->json(['success' => false, 'message' => $message], 422);
                    }
                    return back()->with('error', $message);
                }
            }

            // 4. Check for overdue books
            $overdueCount = BorrowRequest::where('user_id', $student->id)
                ->whereIn('status', ['active', 'overdue'])
                ->where('due_at', '<', now())
                ->count();

            if ($overdueCount > 0) {
                $message = 'Student has overdue books and cannot borrow new books.';
                Log::warning('Borrow confirm validation failed - overdue books', ['borrow_request_id' => $borrowRequest->id, 'student_id' => $student->id, 'overdueCount' => $overdueCount]);
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

            // Send confirmation notification to student (synchronously)
            $student->notifyNow(new BorrowRequestConfirmedNotification($borrowRequest));

            DB::commit();
            Log::info('Borrow request confirmed', ['borrow_request_id' => $borrowRequest->id, 'handled_by' => Auth::id()]);

            // Broadcast updated usage to the student so their UI updates in real-time
                try {
                $weekStart = now()->startOfWeek();
                $weekEnd = now()->endOfWeek();
                $weeklyBorrows = BorrowRequest::where('user_id', $student->id)
                    ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
                    ->whereBetween('created_at', [$weekStart, $weekEnd])
                    ->count();
                $tierLimit = $student->subscription->membershipTier->borrow_limit_per_week;

                $monthlyBorrows = 0;
                $monthlyLimit = null;
                if (!empty($student->subscription->membershipTier->books_per_month)) {
                    $monthStart = now()->startOfMonth();
                    $monthEnd = now()->endOfMonth();
                    $monthlyBorrows = BorrowRequest::where('user_id', $student->id)
                        ->whereIn('status', ['pending', 'active', 'returned', 'overdue'])
                        ->whereBetween('created_at', [$monthStart, $monthEnd])
                        ->count();
                    $monthlyLimit = $student->subscription->membershipTier->books_per_month;
                }

                $event = new BorrowUsageUpdated($student->id, $weeklyBorrows, $tierLimit);
                $event->monthlyBorrows = $monthlyBorrows;
                $event->monthlyLimit = $monthlyLimit;
                $event->atMonthlyLimit = $monthlyLimit ? ($monthlyBorrows >= $monthlyLimit) : false;
                event($event);
            } catch (\Throwable $e) {
                Log::warning('Failed to broadcast borrow usage update', ['exception' => $e->getMessage()]);
            }
            if (request()->expectsJson()) {
                return response()->json(['success' => true, 'message' => 'Borrow request confirmed successfully.']);
            }
            return redirect()->route('staff.borrow-requests.index')->with('success', 'Borrow request confirmed successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            $message = 'Failed to confirm borrow request: ' . $e->getMessage();
            Log::error('Exception during borrow confirm', ['borrow_request_id' => $id, 'exception' => $e->getMessage()]);
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => $message], 500);
            }
            return back()->with('error', $message);
        }
    }

    // Show confirmation page for approving a borrow request
    public function showConfirm($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book', 'student.subscription.membershipTier'])->findOrFail($id);
        return view('staff.borrow-requests.confirm', compact('borrowRequest'));
    }

    // Show rejection page for staff to input reason
    public function showReject($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book'])->findOrFail($id);
        return view('staff.borrow-requests.reject', compact('borrowRequest'));
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500',
        ]);

        $borrowRequest = BorrowRequest::with('student')->findOrFail($id);

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

        // Send rejection notification to student (synchronously)
        $borrowRequest->student->notifyNow(new BorrowRequestRejectedNotification($borrowRequest));

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
                    'processed_by' => Auth::id(),
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

    // Allow staff to download or re-download a receipt PDF for a borrow request
    public function downloadReceipt($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book', 'handler'])->findOrFail($id);

        // Only staff should reach this route (route group enforces role), but double-check
        if (auth()->user()->role !== 'staff' && auth()->user()->role !== 'admin') {
            abort(403);
        }

        try {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.borrow-requests.receipt', ['borrowRequest' => $borrowRequest]);
                return response($pdf->output(), 200)
                    ->header('Content-Type', 'application/pdf')
                    ->header('Content-Disposition', 'attachment; filename="receipt-' . $borrowRequest->id . '.pdf"');
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to generate receipt PDF for staff download', ['exception' => $e->getMessage(), 'borrow_request_id' => $borrowRequest->id]);
        }

        // Fallback: render HTML receipt in browser
        return view('student.borrow-requests.receipt', compact('borrowRequest'));
    }
}