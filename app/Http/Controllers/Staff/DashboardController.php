<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\BorrowRequest;
use App\Models\Book;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $pendingCount = BorrowRequest::where("status", "pending")->count();
        $activeCount = BorrowRequest::where("status", "active")->count();
        $overdueCount = BorrowRequest::where("status", "active")->where("due_at", "<", now())->count();
        $dueTodayCount = BorrowRequest::where("status", "active")->whereDate("due_at", today())->count();

        $overdueBorrows = BorrowRequest::where("status", "active")
            ->where("due_at", "<", now())
            ->with(["student", "book"])
            ->latest("due_at")
            ->get();


        $pendingRequests = BorrowRequest::where("status", "pending")
            ->with(["student", "book", "student.subscription.membershipTier"])
            ->latest("created_at")
            ->get();


        $dueTodayBorrows = BorrowRequest::where("status", "active")
            ->whereDate("due_at", today())
            ->with(["student", "book"])
            ->get();


        $lowStockBooks = Book::whereRaw("available_copies < 3")->with("category")->get();
        $totalAvailableBooks = Book::sum("available_copies");
        $returnRequestsCount = BorrowRequest::where('status', 'return_requested')->count();
        $returnRequests = BorrowRequest::where('status', 'return_requested')
            ->with(['student', 'book'])
            ->latest('created_at')
            ->take(10)
            ->get();

        return view("staff.dashboard.index", compact(
            "pendingCount",
            "activeCount",
            "overdueCount",
            "dueTodayCount",
            "overdueBorrows",
            "pendingRequests",
            "dueTodayBorrows",
            "lowStockBooks",
            "totalAvailableBooks",
            "returnRequestsCount",
            "returnRequests"
        ));
    }
}
