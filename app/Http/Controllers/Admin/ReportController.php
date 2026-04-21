<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\MembershipTier;
use App\Models\Subscription;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        return view('admin.reports.index');
    }

    public function mostBorrowedBooks(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $books = Book::select('books.id', 'books.title', DB::raw('COUNT(borrow_requests.id) as borrow_count'))
            ->join('borrow_requests', 'books.id', '=', 'borrow_requests.book_id')
            ->whereIn('borrow_requests.status', ['active', 'overdue', 'returned'])
            ->whereBetween('borrow_requests.created_at', [$startDate, $endDate])
            ->groupBy('books.id', 'books.title')
            ->orderBy('borrow_count', 'desc')
            ->paginate(15);

        return view('admin.reports.most-borrowed-books', compact('books', 'startDate', 'endDate'));
    }

    public function overdueStatistics(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $overdueStats = BorrowRequest::select(
                'membership_tiers.name as tier_name',
                DB::raw('COUNT(borrow_requests.id) as overdue_count'),
                DB::raw('AVG(DATEDIFF(CURDATE(), borrow_requests.due_at)) as avg_days_overdue')
            )
            ->join('users', 'borrow_requests.user_id', '=', 'users.id')
            ->join('subscriptions', function($join) {
                $join->on('users.id', '=', 'subscriptions.user_id')
                     ->where('subscriptions.status', 'active');
            })
            ->join('membership_tiers', 'subscriptions.membership_tier_id', '=', 'membership_tiers.id')
            ->where('borrow_requests.status', 'overdue')
            ->whereBetween('borrow_requests.created_at', [$startDate, $endDate])
            ->groupBy('membership_tiers.id', 'membership_tiers.name')
            ->orderBy('overdue_count', 'desc')
            ->get();

        return view('admin.reports.overdue-statistics', compact('overdueStats', 'startDate', 'endDate'));
    }

    public function subscriptionRevenue(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfYear()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfYear()->format('Y-m-d'));

        $revenue = Transaction::select(
                DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
                DB::raw('SUM(CASE WHEN type = "payment" THEN amount ELSE 0 END) as payments'),
                DB::raw('SUM(CASE WHEN type = "refund" THEN amount ELSE 0 END) as refunds'),
                DB::raw('SUM(CASE WHEN type = "adjustment" THEN amount ELSE 0 END) as adjustments'),
                DB::raw('SUM(CASE WHEN type IN ("payment", "adjustment") THEN amount ELSE -amount END) as net_revenue')
            )
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('admin.reports.subscription-revenue', compact('revenue', 'startDate', 'endDate'));
    }

    public function studentActivity(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $students = User::select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(borrow_requests.id) as total_borrows'),
                DB::raw('COUNT(CASE WHEN borrow_requests.status = "overdue" THEN 1 END) as overdue_count'),
                DB::raw('ROUND((COUNT(CASE WHEN borrow_requests.status = "overdue" THEN 1 END) / COUNT(borrow_requests.id)) * 100, 2) as overdue_rate')
            )
            ->leftJoin('borrow_requests', 'users.id', '=', 'borrow_requests.user_id')
            ->where('users.role', 'student')
            ->where(function($query) use ($startDate, $endDate) {
                $query->whereBetween('borrow_requests.created_at', [$startDate, $endDate])
                      ->orWhereNull('borrow_requests.created_at');
            })
            ->groupBy('users.id', 'users.name', 'users.email')
            ->having('total_borrows', '>', 0)
            ->orderBy('total_borrows', 'desc')
            ->paginate(15);

        return view('admin.reports.student-activity', compact('students', 'startDate', 'endDate'));
    }

    public function staffActivity(Request $request)
    {
        $startDate = $request->get('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->get('end_date', now()->endOfMonth()->format('Y-m-d'));

        $staff = User::select(
                'users.id',
                'users.name',
                'users.email',
                DB::raw('COUNT(borrow_requests.id) as requests_handled'),
                DB::raw('COUNT(CASE WHEN borrow_requests.status = "active" THEN 1 END) as confirmed_requests'),
                DB::raw('COUNT(CASE WHEN borrow_requests.status = "rejected" THEN 1 END) as rejected_requests')
            )
            ->leftJoin('borrow_requests', 'users.id', '=', 'borrow_requests.handled_by')
            ->where('users.role', 'staff')
            ->whereBetween('borrow_requests.created_at', [$startDate, $endDate])
            ->groupBy('users.id', 'users.name', 'users.email')
            ->having('requests_handled', '>', 0)
            ->orderBy('requests_handled', 'desc')
            ->paginate(15);

        return view('admin.reports.staff-activity', compact('staff', 'startDate', 'endDate'));
    }

    public function auditLogs(Request $request)
    {
        $auditLogs = AuditLog::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(25);

        return view('admin.reports.audit-logs', compact('auditLogs'));
    }
}
