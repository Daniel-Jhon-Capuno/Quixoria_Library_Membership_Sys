<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DeadlineDashboardController extends Controller
{
    public function index()
    {
        $now = now();
        $endOfToday = $now->copy()->endOfDay();
        $endOf3Days = $now->copy()->addDays(3)->endOfDay();
        $endOfWeek = $now->copy()->addDays(7)->endOfDay();

        $activeBorrows = BorrowRequest::with(['student', 'book'])
            ->whereIn('status', ['active', 'overdue'])
            ->get();

        $overdue = $activeBorrows->filter(function ($borrow) {
            return $borrow->due_at < now();
        });

        $dueToday = $activeBorrows->filter(function ($borrow) use ($now, $endOfToday) {
            return $borrow->due_at >= $now && $borrow->due_at <= $endOfToday;
        });

        $due3Days = $activeBorrows->filter(function ($borrow) use ($endOfToday, $endOf3Days) {
            return $borrow->due_at > $endOfToday && $borrow->due_at <= $endOf3Days;
        });

        $dueThisWeek = $activeBorrows->filter(function ($borrow) use ($endOf3Days, $endOfWeek) {
            return $borrow->due_at > $endOf3Days && $borrow->due_at <= $endOfWeek;
        });

        return view('staff.deadline-dashboard.index', compact(
            'overdue',
            'dueToday',
            'due3Days',
            'dueThisWeek'
        ));
    }

    public function ping($borrowRequestId)
    {
        $borrowRequest = BorrowRequest::with('student')->findOrFail($borrowRequestId);

        if (!in_array($borrowRequest->status, ['active', 'overdue'])) {
            return back()->with('error', 'Can only send reminders for active or overdue borrows.');
        }

        // Check if we recently sent a reminder (prevent spam)
        $lastReminder = $borrowRequest->student->notifications()
            ->where('type', 'App\Notifications\DeadlineReminderNotification')
            ->where('data->borrow_request_id', $borrowRequest->id)
            ->where('created_at', '>', now()->subHours(24))
            ->first();

        if ($lastReminder) {
            return back()->with('error', 'A reminder was already sent to this student in the last 24 hours.');
        }

        // Send the notification
        $borrowRequest->student->notify(new DeadlineReminderNotification($borrowRequest));

        return back()->with('success', 'Deadline reminder sent to student.');
    }
}