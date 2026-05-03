<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class ActiveBorrowController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = BorrowRequest::with(['book'])
            ->where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue']);

        // Sorting options
        $sortBy = $request->get('sort', 'soonest');

        switch ($sortBy) {
            case 'soonest':
                $query->orderBy('due_at', 'asc');
                break;
            case 'furthest':
                $query->orderBy('due_at', 'desc');
                break;
            case 'overdue':
                $query->orderByRaw("CASE WHEN due_at < NOW() THEN 0 ELSE 1 END, due_at ASC");
                break;
            default:
                $query->orderBy('due_at', 'asc');
        }

        $activeBorrows = $query->get();

        return view('student.active-borrows.index', compact('activeBorrows', 'sortBy'));
    }

    public function renew($id)
    {
        $user = Auth::user();

        $borrowRequest = BorrowRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->whereIn('status', ['active', 'overdue'])
            ->firstOrFail();

        $tier = $user->subscription->membershipTier;

        // Check if renewal is allowed
        if ($borrowRequest->renewals_used >= $tier->renewal_limit) {
            return redirect()->back()->with('error', 'You have reached the maximum number of renewals for this book.');
        }

        // Check if the book is overdue (some libraries don't allow renewal of overdue books)
        if ($borrowRequest->due_at < now()) {
            return redirect()->back()->with('error', 'Overdue books cannot be renewed. Please return the book first.');
        }

        // Calculate new due date
        $newDueDate = $borrowRequest->due_at->addDays($tier->borrow_duration_days);

        // Update the borrow request
        $borrowRequest->update([
            'due_at' => $newDueDate,
            'renewals_used' => $borrowRequest->renewals_used + 1,
        ]);

        // Log the renewal action
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'renew',
            'model_type' => 'App\\Models\\BorrowRequest',
            'model_id' => $borrowRequest->id,
            'changes' => json_encode([
                'old_due_date' => $borrowRequest->getOriginal('due_at'),
                'new_due_date' => $newDueDate->format('Y-m-d'),
                'renewals_used' => $borrowRequest->renewals_used,
                'book_title' => $borrowRequest->book->title
            ]),
        ]);

        return redirect()->back()->with('success', "Book renewal successful. New due date: {$newDueDate->format('M j, Y')}");
    }

    public function returnRequest($id)
    {
        $user = Auth::user();

        $borrowRequest = BorrowRequest::where('user_id', $user->id)
            ->where('id', $id)
            ->whereIn('status', ['active', 'overdue'])
            ->firstOrFail();

$borrowRequest->update([
            'status' => 'return_requested',
        ]);

        // Notify staff
        $staffUsers = \App\Models\User::where('role', 'staff')->get();
        Notification::send($staffUsers, new \App\Notifications\StaffNewReturnRequestNotification($borrowRequest));

        // Log the return request
        AuditLog::create([
            'user_id' => $user->id,
            'action' => 'return_request',
            'model_type' => 'App\\Models\\BorrowRequest',
            'model_id' => $borrowRequest->id,
            'changes' => json_encode([
                'book_title' => $borrowRequest->book->title,
                'due_date' => $borrowRequest->due_at->format('Y-m-d'),
            ])
        ]);

        return redirect()->back()->with('success', 'Return request sent to staff. They will process it shortly.');
    }
}

