<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\User;
use App\Notifications\AppealCompletedNotification;
use App\Notifications\AppealNoShowNotification;
use App\Notifications\AppealFailedNotification;
use App\Notifications\AppealFailedAdminNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AppealDashboardController extends Controller
{
    public function index()
    {
        $appeals = BorrowRequest::with(['student', 'book'])
            ->whereIn('status', [
                'appeal_scheduled',
                'appeal_rescheduled',
                'appeal_no_show',
                'appeal_failed',
                'appeal_completed',
            ])
            ->orderBy('appeal_scheduled_at', 'asc')
            ->paginate(20);

        return view('staff.appeals.index', compact('appeals'));
    }

    public function complete($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book'])->findOrFail($id);

        if (!in_array($borrowRequest->status, ['appeal_scheduled', 'appeal_rescheduled'])) {
            return back()->with('error', 'This appeal cannot be marked as completed.');
        }

        $borrowRequest->update([
            'status' => 'returned',
            'appeal_status' => 'completed',
            'returned_at' => now(),
            'handled_by' => Auth::id(),
        ]);

        $borrowRequest->student->notifyNow(new AppealCompletedNotification($borrowRequest));

        return back()->with('success', 'Appeal marked as completed. Book returned successfully.');
    }

    public function noShow($id)
    {
        $borrowRequest = BorrowRequest::with(['student', 'book'])->findOrFail($id);

        if (!in_array($borrowRequest->status, ['appeal_scheduled', 'appeal_rescheduled'])) {
            return back()->with('error', 'This appeal cannot be marked as no show.');
        }

        // 2nd no show = appeal_failed = restrict account
        if ($borrowRequest->status === 'appeal_rescheduled') {
            $borrowRequest->update([
                'status' => 'appeal_failed',
                'appeal_status' => 'failed',
                'handled_by' => Auth::id(),
            ]);

            // Restrict the student account
            $borrowRequest->student->update([
                'is_restricted' => true,
                'restriction_reason' => 'Failed to show up for return appeal twice for book: ' . $borrowRequest->book->title,
                'restricted_at' => now(),
            ]);

            // Notify student
            $borrowRequest->student->notifyNow(new AppealFailedNotification($borrowRequest));

            // Notify all admins
            $admins = User::where('role', 'admin')->get();
            Notification::send($admins, new AppealFailedAdminNotification($borrowRequest));

            return back()->with('success', 'Appeal failed. Student account has been restricted.');
        }

        // 1st no show
        $borrowRequest->update([
            'status' => 'appeal_no_show',
            'handled_by' => Auth::id(),
        ]);

        $borrowRequest->student->notifyNow(new AppealNoShowNotification($borrowRequest));

        return back()->with('success', 'Marked as no show. Student has been notified to reschedule.');
    }
}

