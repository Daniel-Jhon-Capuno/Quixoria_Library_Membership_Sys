<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get active subscription
        $subscription = $user->subscription;

        // Get active borrows with countdown
        $activeBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->with('book')
            ->latest('due_at')
            ->get();

        // Get overdue borrows
        $overdueBorrows = BorrowRequest::where('user_id', $user->id)
            ->whereIn('status', ['active', 'overdue'])
            ->where('due_at', '<', now())
            ->with('book')
            ->get();

        // Get active reservations only (fixes count showing 0)
        $reservations = $user->reservations()->active()->with('book')->latest()->take(5)->get();

        // Get total borrowed count
        $totalBorrowed = BorrowRequest::where('user_id', $user->id)->count();

        // Calculate late fees
        $lateFees = BorrowRequest::where('user_id', $user->id)
            ->where('late_fee_charged', '>', 0)
            ->sum('late_fee_charged')
            - BorrowRequest::where('user_id', $user->id)
            ->where('late_fee_waived', true)
            ->sum('late_fee_charged');

        // Get recommended books (popular books not currently borrowed by user)
        $recommendedBooks = \App\Models\Book::where('is_archived', false)
            ->whereNotIn('id', $activeBorrows->pluck('book_id'))
            ->inRandomOrder()
            ->take(4)
            ->get();

        return view('student.dashboard.index', compact(
            'subscription',
            'activeBorrows',
            'overdueBorrows',
            'reservations',
            'totalBorrowed',
            'lateFees',
            'recommendedBooks'
        ));
    }
}

