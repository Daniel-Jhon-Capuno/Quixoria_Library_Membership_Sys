<?php

use App\Http\Controllers\Admin\BookController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MembershipTierController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Staff\BorrowRequestController as StaffBorrowRequestController;
use App\Http\Controllers\Staff\DeadlineDashboardController;
use App\Http\Controllers\Student\BookCatalogController;
use App\Http\Controllers\Student\SubscriptionController as StudentSubscriptionController;
use App\Http\Controllers\Student\BorrowRequestController as StudentBorrowRequestController;
use App\Http\Controllers\Student\ActiveBorrowController;
use App\Http\Controllers\Student\DashboardController as StudentDashboardController;
use App\Http\Controllers\Student\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return match (auth()->user()->role) {
            'admin' => redirect()->route('admin.dashboard.index'),
            'staff' => redirect()->route('staff.dashboard.index'),
            'student' => redirect()->route('student.dashboard.index'),
            default => redirect()->route('login'),
        };
    }

    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::resource('tiers', MembershipTierController::class);
    Route::resource('users', UserController::class);
    Route::post('users/{user}/reset-password', [UserController::class, 'resetPassword'])
        ->name('users.reset-password');
    Route::resource('books', BookController::class);
    Route::patch('books/{book}/archive', [BookController::class, 'archive'])
        ->name('books.archive');
    Route::resource('subscriptions', SubscriptionController::class)->only(['index', 'show']);
    Route::get('subscriptions/debug/{user}', [SubscriptionController::class, 'debug'])->name('subscriptions.debug');
    Route::post('subscriptions/override/{user}', [SubscriptionController::class, 'override'])
        ->name('subscriptions.override');
    Route::post('subscriptions/quick-fix/{user}', [SubscriptionController::class, 'quickFix'])
        ->name('subscriptions.quick-fix');
    Route::post('subscriptions/force/{user}', [SubscriptionController::class, 'forceActivate'])
        ->name('subscriptions.force');
    Route::post('subscriptions/adjust/{subscription}', [SubscriptionController::class, 'adjust'])
        ->name('subscriptions.adjust');
    // Admin confirm/reject pending subscriptions
    Route::post('subscriptions/{subscription}/confirm', [SubscriptionController::class, 'confirm'])
        ->name('subscriptions.confirm');
    Route::post('subscriptions/{subscription}/reject', [SubscriptionController::class, 'reject'])
        ->name('subscriptions.reject');
    Route::get('subscriptions/pending', [SubscriptionController::class, 'pending'])->name('subscriptions.pending');
    Route::post('subscriptions/bulk-confirm', [SubscriptionController::class, 'bulkConfirm'])->name('subscriptions.bulk-confirm');
    Route::post('subscriptions/bulk-reject', [SubscriptionController::class, 'bulkReject'])->name('subscriptions.bulk-reject');
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('reports/most-borrowed-books', [ReportController::class, 'mostBorrowedBooks'])->name('reports.most-borrowed-books');
    Route::get('reports/overdue-statistics', [ReportController::class, 'overdueStatistics'])->name('reports.overdue-statistics');
    Route::get('reports/subscription-revenue', [ReportController::class, 'subscriptionRevenue'])->name('reports.subscription-revenue');
    Route::get('reports/student-activity', [ReportController::class, 'studentActivity'])->name('reports.student-activity');
    Route::get('reports/staff-activity', [ReportController::class, 'staffActivity'])->name('reports.staff-activity');
    Route::get('reports/audit-logs', [ReportController::class, 'auditLogs'])->name('reports.audit-logs');
});

Route::middleware(['auth', 'role:staff'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('borrow-requests', [StaffBorrowRequestController::class, 'index'])->name('borrow-requests.index');
    Route::get('borrow-requests/{id}/receipt', [StaffBorrowRequestController::class, 'downloadReceipt'])->name('borrow-requests.receipt');
    Route::post('borrow-requests/{id}/confirm', [StaffBorrowRequestController::class, 'confirm'])->name('borrow-requests.confirm');
    Route::get('borrow-requests/{id}/confirm', [StaffBorrowRequestController::class, 'showConfirm'])->name('borrow-requests.confirm.form');
    Route::post('borrow-requests/{id}/reject', [StaffBorrowRequestController::class, 'reject'])->name('borrow-requests.reject');
    Route::get('borrow-requests/{id}/reject', [StaffBorrowRequestController::class, 'showReject'])->name('borrow-requests.reject.form');
    Route::post('borrow-requests/{id}/check-in', [StaffBorrowRequestController::class, 'checkIn'])->name('borrow-requests.check-in');
    Route::get('deadline-dashboard', [DeadlineDashboardController::class, 'index'])->name('deadline-dashboard.index');
    Route::post('deadline-dashboard/ping/{borrowRequestId}', [DeadlineDashboardController::class, 'ping'])->name('deadline-dashboard.ping');
    Route::get('students/{student}', [UserController::class, 'show'])->name('students.show');
    Route::get('notifications/{id}/go', [\App\Http\Controllers\Staff\NotificationController::class, 'go'])->name('notifications.go');
});

Route::middleware(['auth', 'role:student'])->prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', [StudentDashboardController::class, 'index'])->name('dashboard.index');
    Route::get('books', [BookCatalogController::class, 'index'])->name('book-catalog.index');
    Route::get('books/{id}', [BookCatalogController::class, 'show'])->name('book-catalog.show');
    Route::get('borrow-usage', [BookCatalogController::class, 'usage'])->name('borrow-usage');
    Route::get('borrow-requests', [StudentBorrowRequestController::class, 'index'])->name('borrow-requests.index');
    Route::post('borrow-requests/{bookId}', [StudentBorrowRequestController::class, 'store'])->name('borrow-requests.store');
    Route::delete('borrow-requests/{id}', [StudentBorrowRequestController::class, 'destroy'])->name('borrow-requests.destroy');
    Route::get('borrow-requests/{id}/receipt', [StudentBorrowRequestController::class, 'receipt'])->name('borrow-requests.receipt');
    Route::get('receipts', [StudentBorrowRequestController::class, 'receipts'])->name('receipts.index');
    Route::get('active-borrows', [ActiveBorrowController::class, 'index'])->name('active-borrows.index');
    Route::post('active-borrows/{id}/renew', [ActiveBorrowController::class, 'renew'])->name('active-borrows.renew');
    Route::get('subscription', [StudentSubscriptionController::class, 'index'])->name('subscription.index');
    Route::post('subscription/purchase', [StudentSubscriptionController::class, 'purchase'])->name('subscription.purchase');
    Route::post('subscription/upgrade', [StudentSubscriptionController::class, 'upgrade'])->name('subscription.upgrade');
    Route::post('subscription/downgrade', [StudentSubscriptionController::class, 'downgrade'])->name('subscription.downgrade');
    Route::post('subscription/cancel', [StudentSubscriptionController::class, 'cancel'])->name('subscription.cancel');
    Route::post('reservations', [ReservationController::class, 'store'])->name('reservations.store');
});

Route::middleware('auth')->group(function () {
    Route::post('/notifications/mark-read', function() {
        auth()->user()->unreadNotifications->markAsRead();
        return back();
    })->name('notifications.mark-read');

    Route::get('/notifications/{id}/go', [\App\Http\Controllers\NotificationController::class, 'go'])->name('notifications.go');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
