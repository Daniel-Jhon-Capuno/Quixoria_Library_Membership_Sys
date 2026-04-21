<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Total students (active, suspended)
        $totalStudents = User::where('role', 'student')->count();
        $activeStudents = User::where('role', 'student')->where('is_active', true)->count();
        $suspendedStudents = User::where('role', 'student')->where('is_active', false)->count();

        // Total active subscriptions (breakdown by tier)
        $activeSubscriptions = Subscription::where('status', 'active')
            ->with('membershipTier')
            ->get()
            ->groupBy('membership_tier_id')
            ->map(function ($subscriptions, $tierId) {
                $tier = $subscriptions->first()->membershipTier;
                return [
                    'tier_name' => $tier->name,
                    'count' => $subscriptions->count(),
                    'monthly_revenue' => $subscriptions->count() * $tier->monthly_fee,
                ];
            });

        $totalActiveSubscriptions = $activeSubscriptions->sum('count');

        // Total books in catalog (available copies vs total copies)
        $totalBooks = Book::sum('total_copies');
        $availableBooks = Book::sum('available_copies');

        // Pending borrow requests count
        $pendingRequests = BorrowRequest::where('status', 'pending')->count();

        // Overdue borrows count
        $overdueBorrows = BorrowRequest::where('status', 'active')
            ->where('due_at', '<', now())
            ->count();

        // Revenue this month
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $monthlyRevenue = Transaction::whereYear('created_at', $currentYear)
            ->whereMonth('created_at', $currentMonth)
            ->sum('amount');

        // Latest 5 borrow requests
        $latestBorrowRequests = BorrowRequest::with(['user', 'book'])
            ->latest()
            ->take(5)
            ->get();

        // Latest 5 transactions
        $latestTransactions = Transaction::with(['user', 'subscription.membershipTier'])
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard.index', compact(
            'totalStudents',
            'activeStudents',
            'suspendedStudents',
            'activeSubscriptions',
            'totalActiveSubscriptions',
            'totalBooks',
            'availableBooks',
            'pendingRequests',
            'overdueBorrows',
            'monthlyRevenue',
            'latestBorrowRequests',
            'latestTransactions'
        ));
    }
}
