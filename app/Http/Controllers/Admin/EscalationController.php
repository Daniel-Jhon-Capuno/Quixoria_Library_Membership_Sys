<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\EscalationLog;
use App\Models\User;
use App\Notifications\PermanentBanNotification;
use App\Notifications\TemporaryUnblockNotification;
use Illuminate\Http\Request;

class EscalationController extends Controller
{
    /**
     * Show escalation dashboard with tabs
     */
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'lost');

        $query = BorrowRequest::with(['student', 'book'])->whereIn('status', ['lost', 'severely_overdue', 'unresolved', 'presumed_lost']);

        if ($tab === 'lost') {
            $cases = $query->where('status', 'lost')->paginate(15);
        } elseif ($tab === 'severely_overdue') {
            $cases = $query->where('status', 'severely_overdue')->paginate(15);
        } elseif ($tab === 'unresolved') {
            $cases = $query->where('status', 'unresolved')->paginate(15);
        } elseif ($tab === 'presumed_lost') {
            $cases = $query->where('status', 'presumed_lost')->paginate(15);
        } else {
            $cases = $query->paginate(15);
        }

        $counts = [
            'lost' => BorrowRequest::where('status', 'lost')->count(),
            'severely_overdue' => BorrowRequest::where('status', 'severely_overdue')->count(),
            'unresolved' => BorrowRequest::where('status', 'unresolved')->count(),
            'presumed_lost' => BorrowRequest::where('status', 'presumed_lost')->count(),
        ];

        return view('admin.escalations.index', compact('cases', 'tab', 'counts'));
    }

    /**
     * Show case detail page
     */
    public function show($borrowRequestId)
    {
        $borrow = BorrowRequest::with(['student', 'book', 'escalationLogs.creator', 'resolution.admin'])->findOrFail($borrowRequestId);

        return view('admin.escalations.show', compact('borrow'));
    }

    /**
     * Mark case as resolved and unblock account
     */
    public function resolve(Request $request, $borrowRequestId)
    {
        $request->validate([
            'amount_cents' => 'required|integer|min:0',
            'method' => 'required|string|in:cash,gcash,bank',
            'notes' => 'nullable|string|max:500',
        ]);

        $borrow = BorrowRequest::with(['student', 'book'])->findOrFail($borrowRequestId);

        // Update borrow request
        $borrow->update([
            'status' => 'resolved',
            'escalation_level' => null,
            'replacement_fee_paid' => true,
            'resolved_at' => now(),
            'resolved_by' => auth()->id(),
        ]);

        // Create resolution record
        $receiptNumber = 'RCPT-' . now()->format('YmdHis') . '-' . $borrow->id;
        $borrow->resolution()->create([
            'admin_id' => auth()->id(),
            'amount_cents' => $request->amount_cents,
            'method' => $request->method,
            'notes' => $request->notes,
            'receipt_number' => $receiptNumber,
        ]);

        // Unblock student account
        $borrow->student->update([
            'has_unpaid_fees' => false,
            'is_restricted' => false,
            'restriction_reason' => null,
            'restricted_at' => null,
        ]);

        // Create escalation log
        EscalationLog::create([
            'borrow_request_id' => $borrow->id,
            'level' => 'resolved',
            'note' => "Case resolved by {$request->notes}. Amount received: ₱" . ($request->amount_cents / 100),
            'created_by' => auth()->id(),
        ]);

        // Notify student
        $borrow->student->notify(new \App\Notifications\CaseResolvedNotification($borrow, $request->amount_cents));

        return response()->json([
            'message' => 'Case resolved successfully.',
            'receipt_number' => $receiptNumber,
        ]);
    }

    /**
     * Grant temporary access for N days
     */
    public function temporaryUnblock(Request $request, $borrowRequestId)
    {
        $request->validate([
            'days' => 'required|integer|min:1|max:90',
            'reason' => 'required|string|max:500',
        ]);

        $borrow = BorrowRequest::with(['student', 'book'])->findOrFail($borrowRequestId);

        // Temporarily unblock
        $unblockUntil = now()->addDays($request->days);
        $borrow->student->update([
            'is_restricted' => false,
            'temporary_unblock_until' => $unblockUntil,
        ]);

        // Create escalation log
        EscalationLog::create([
            'borrow_request_id' => $borrow->id,
            'level' => 'temporary_unblock',
            'note' => "Temporary unblock for {$request->days} days. Reason: {$request->reason}",
            'created_by' => auth()->id(),
        ]);

        // Notify student
        $borrow->student->notify(new TemporaryUnblockNotification($borrow, $request->days));

        return response()->json(['message' => 'Temporary access granted.']);
    }

    /**
     * Reset escalation timer
     */
    public function resetEscalation(Request $request, $borrowRequestId)
    {
        $request->validate([
            'due_at' => 'required|date|after:today',
            'reason' => 'required|string|max:500',
        ]);

        $borrow = BorrowRequest::with(['student', 'book'])->findOrFail($borrowRequestId);

        // Reset escalation
        $borrow->update([
            'escalation_level' => null,
            'status' => 'active',
            'due_at' => $request->due_at,
        ]);

        // Create escalation log
        EscalationLog::create([
            'borrow_request_id' => $borrow->id,
            'level' => 'escalation_reset',
            'note' => "Escalation reset. New due date: {$request->due_at}. Reason: {$request->reason}",
            'created_by' => auth()->id(),
        ]);

        // Notify student
        $borrow->student->notify(new \App\Notifications\EscalationResetNotification($borrow));

        return response()->json(['message' => 'Escalation reset successfully.']);
    }

    /**
     * Permanently ban student
     */
    public function permanentBan(Request $request, $userId)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $user = User::findOrFail($userId);

        // Ban student
        $user->update([
            'is_permanently_banned' => true,
            'ban_reason' => $request->reason,
            'banned_at' => now(),
            'banned_by' => auth()->id(),
            'is_restricted' => true,
        ]);

        // Notify student
        $user->notify(new PermanentBanNotification($request->reason));

        // Notify all admins
        $admins = User::where('role', 'admin')->where('id', '!=', auth()->id())->get();
        foreach ($admins as $admin) {
            $admin->notify(new \App\Notifications\StudentPermanentlyBannedNotification($user, $request->reason));
        }

        return response()->json(['message' => 'Student permanently banned.']);
    }
}
