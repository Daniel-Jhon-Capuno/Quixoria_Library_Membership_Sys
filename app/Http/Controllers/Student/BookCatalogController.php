<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Book;
use App\Models\BorrowRequest;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BookCatalogController extends Controller
{
    public function index(Request $request)
    {
        $query = Book::where('is_archived', false);

        // Search filter
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('author', 'LIKE', "%{$search}%")
                  ->orWhere('isbn', 'LIKE', "%{$search}%");
            });
        }

        // Genre filter
        if ($request->filled('genre')) {
            $query->where('genre', $request->genre);
        }

        // Category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Availability filter
        if ($request->filled('availability')) {
            if ($request->availability === 'available') {
                $query->whereRaw('total_copies > (
                    SELECT COUNT(*)
                    FROM borrow_requests br
                    WHERE br.book_id = books.id
                    AND br.status IN ("active", "overdue")
                )');
            } elseif ($request->availability === 'unavailable') {
                $query->whereRaw('total_copies <= (
                    SELECT COUNT(*)
                    FROM borrow_requests br
                    WHERE br.book_id = books.id
                    AND br.status IN ("active", "overdue")
                )');
            }
        }

        $books = $query->paginate(12)->withQueryString();

        // Get unique genres and categories for filter dropdowns
        $genres = Book::where('is_archived', false)
            ->whereNotNull('genre')
            ->distinct()
            ->pluck('genre')
            ->sort();

        $categories = Book::where('is_archived', false)
            ->whereNotNull('category')
            ->distinct()
            ->pluck('category')
            ->sort();

        return view('student.book-catalog.index', compact('books', 'genres', 'categories'));
    }

    public function show($id)
    {
        $book = Book::where('is_archived', false)->findOrFail($id);

        $user = Auth::user();

        // Calculate available copies
        $activeBorrows = BorrowRequest::where('book_id', $book->id)
            ->whereIn('status', ['active', 'overdue'])
            ->count();

        $availableCopies = $book->total_copies - $activeBorrows;

        // Check if user can borrow
        $canBorrow = true;
        $borrowDisabledReason = null;

        if (!$user->subscription || $user->subscription->expires_at <= now()) {
            $canBorrow = false;
            $borrowDisabledReason = 'No active subscription';
        } else {
            $tier = $user->subscription->membershipTier;

            // Check weekly borrow limit
            $weekStart = now()->startOfWeek();
            $weekEnd = now()->endOfWeek();
            $weeklyBorrows = BorrowRequest::where('user_id', $user->id)
                ->whereIn('status', ['active', 'returned', 'overdue'])
                ->whereBetween('borrowed_at', [$weekStart, $weekEnd])
                ->count();

            if ($weeklyBorrows >= $tier->borrow_limit_per_week) {
                $canBorrow = false;
                $borrowDisabledReason = 'Weekly borrow limit reached';
            }

            // Check for overdue books
            $overdueCount = BorrowRequest::where('user_id', $user->id)
                ->whereIn('status', ['active', 'overdue'])
                ->where('due_at', '<', now())
                ->count();

            if ($overdueCount > 0) {
                $canBorrow = false;
                $borrowDisabledReason = 'Has overdue books';
            }
        }

        // Check if user can reserve
        $canReserve = $user->subscription && $user->subscription->membershipTier->can_reserve;

        // Check if user already has a reservation for this book
        $hasReservation = Reservation::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['waiting', 'ready'])
            ->exists();

        // Check if user already has an active borrow request for this book
        $hasActiveRequest = BorrowRequest::where('user_id', $user->id)
            ->where('book_id', $book->id)
            ->whereIn('status', ['pending', 'confirmed', 'active', 'overdue'])
            ->exists();

        return view('student.book-catalog.show', compact(
            'book',
            'availableCopies',
            'canBorrow',
            'borrowDisabledReason',
            'canReserve',
            'hasReservation',
            'hasActiveRequest'
        ));
    }
}