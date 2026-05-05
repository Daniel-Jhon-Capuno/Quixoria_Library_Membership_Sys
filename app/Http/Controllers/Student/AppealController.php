<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Notifications\ReturnAppealScheduledNotification;
use App\Notifications\ReturnAppealSubmittedNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AppealController extends Controller
{
    public function store(Request $request, $id)
    {
        $request->validate([
            'appeal_reason' => 'required|string|max:1000',
            'appeal_scheduled_at' => 'required|date|after:now',
        ]);

        $user = Auth::user();

        if ($user->is_restricted) {
            return redirect()->back()->with('error', 'Your account is restricted. Please contact the admin.');
        }

        $borrowRequest = BorrowRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->where('status', 'return_rejected')
            ->where('appeal_status', 'none')
            ->firstOrFail();

        $borrowRequest->update([
            'appeal_reason' => $request->appeal_reason,
            'appeal_scheduled_at' => $request->appeal_scheduled_at,
            'appeal_status' => 'scheduled',
            'status' => 'appeal_scheduled',
        ]);

        // Notify staff
        $staffUsers = \App\Models\User::where('role', 'staff')->get();
        \Illuminate\Support\Facades\Notification::send(
            $staffUsers,
            new ReturnAppealSubmittedNotification($borrowRequest)
        );

        // Notify student
        $user->notifyNow(new ReturnAppealScheduledNotification($borrowRequest));

        return redirect()->back()->with('success', 'Appeal submitted! Please visit the branch on your scheduled date.');
    }

    public function reschedule(Request $request, $id)
    {
        $request->validate([
            'appeal_scheduled_at' => 'required|date|after:now',
        ]);

        $user = Auth::user();

        $borrowRequest = BorrowRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->where('status', 'appeal_no_show')
            ->where('appeal_status', 'scheduled') // only 1 reschedule allowed
            ->firstOrFail();

        $borrowRequest->update([
            'appeal_scheduled_at' => $request->appeal_scheduled_at,
            'appeal_status' => 'rescheduled',
            'status' => 'appeal_rescheduled',
        ]);

        // Notify staff
        $staffUsers = \App\Models\User::where('role', 'staff')->get();
        \Illuminate\Support\Facades\Notification::send(
            $staffUsers,
            new ReturnAppealSubmittedNotification($borrowRequest)
        );

        // Notify student
        $user->notifyNow(new ReturnAppealScheduledNotification($borrowRequest));

        return redirect()->back()->with('success', 'Reschedule submitted! Please make sure to show up this time.');
    }
}

