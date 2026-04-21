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
            ->with(["user", "book"])
            ->latest("due_at")
            ->get();

        $pendingRequests = BorrowRequest::where("status", "pending")
            ->with(["user", "book", "user.subscription.membershipTier"])
            ->latest("created_at")
            ->get();

        $dueTodayBorrows = BorrowRequest::where("status", "active")
            ->whereDate("due_at", today())
            ->with(["user", "book"])
            ->get();

        $lowStockBooks = Book::whereRaw("available_copies < 3")->with("category")->get();
        $totalAvailableBooks = Book::sum("available_copies");

        return view("staff.dashboard.index", compact(
            "pendingCount",
            "activeCount",
            "overdueCount",
            "dueTodayCount",
            "overdueBorrows",
            "pendingRequests",
            "dueTodayBorrows",
            "lowStockBooks",
            "totalAvailableBooks"
        ));
    }
}
