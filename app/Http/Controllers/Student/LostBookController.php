<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\EscalationLog;
use App\Notifications\LostBookReportedNotification;
use Illuminate\Http\Request;

class LostBookController extends Controller
{
    /**
     * Show lost book report confirmation modal
     */
    public function confirm($borrowRequestId)
    {
        $borrow = BorrowRequest::findOrFail($borrowRequestId);

        // Ensure student can only report their own borrows
        if ($borrow->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Can only report active/overdue books
        if (!in_array($borrow->status, ['active', 'overdue'])) {
            return response()->json(['error' => 'Can only report active or overdue books.'], 400);
        }

        // Calculate fees
        $daysOverdue = max(0, now()->diffInDays($borrow->due_at));
        $lateFeeCents = $borrow->late_fee_charged ?? 0; // Already calculated
        $replacementFeeCents = ($borrow->book->price_cents ?? 0) * 100; // Book price in cents
        $totalCents = $lateFeeCents + $replacementFeeCents;

        return response()->json([
            'borrow_id' => $borrow->id,
            'book_title' => $borrow->book->title,
            'book_price_pesos' => ($borrow->book->price_cents ?? 0) / 100,
            'late_fee_pesos' => $lateFeeCents / 100,
            'replacement_fee_pesos' => $replacementFeeCents / 100,
            'total_pesos' => $totalCents / 100,
            'days_overdue' => $daysOverdue,
        ]);
    }

    /**
     * Report a book as lost
     */
    public function report(Request $request, $borrowRequestId)
    {
        $borrow = BorrowRequest::with(['student', 'book'])->findOrFail($borrowRequestId);

        // Ensure student can only report their own borrows
        if ($borrow->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Can only report active/overdue books
        if (!in_array($borrow->status, ['active', 'overdue'])) {
            return response()->json(['error' => 'Can only report active or overdue books.'], 400);
        }

        // Calculate fees
        $lateFeeCents = $borrow->late_fee_charged ?? 0;
        $replacementFeeCents = ($borrow->book->price_cents ?? 0) * 100;
        $totalCents = $lateFeeCents + $replacementFeeCents;

        // Update borrow request
        $borrow->update([
            'status' => 'lost',
            'escalation_level' => 'lost',
            'replacement_fee_cents' => $replacementFeeCents,
            'replacement_fee_paid' => false,
        ]);

        // Mark user as having unpaid fees
        $borrow->student->update([
            'has_unpaid_fees' => true,
            'is_restricted' => true,
            'restriction_reason' => 'Lost book reported',
            'restricted_at' => now(),
        ]);

        // Reduce book inventory
        $borrow->book->update([
            'total_copies' => max(0, $borrow->book->total_copies - 1),
            'available_copies' => max(0, $borrow->book->available_copies - 1),
        ]);

        // Create escalation log
        EscalationLog::create([
            'borrow_request_id' => $borrow->id,
            'level' => 'lost',
            'note' => 'Student reported book as lost',
            'created_by' => auth()->id(),
        ]);

        // Send notifications
        $borrow->student->notify(new \App\Notifications\LostBookReportedNotification($borrow));
        
        // Notify staff
        $staffUsers = \App\Models\User::where('role', 'staff')->get();
        foreach ($staffUsers as $staff) {
            $staff->notify(new \App\Notifications\LostBookReportedStaffNotification($borrow));
        }

        // Notify admin
        $adminUsers = \App\Models\User::where('role', 'admin')->get();
        foreach ($adminUsers as $admin) {
            $admin->notify(new \App\Notifications\LostBookReportedAdminNotification($borrow));
        }

        return response()->json([
            'message' => 'Book reported as lost successfully.',
            'redirect' => route('student.active-borrows.index'),
        ]);
    }
}
