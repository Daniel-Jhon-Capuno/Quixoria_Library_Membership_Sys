# Quick Reference - Route Authorization

## URL Structure

### Admin Routes
```
/admin/dashboard          → Admin Dashboard
/admin/tiers             → Manage Membership Tiers
/admin/users             → Manage Users
/admin/books             → Manage Books
/admin/subscriptions     → View Subscriptions
/admin/reports           → View Reports
```

### Staff Routes
```
/staff/dashboard         → Staff Dashboard
/staff/borrow-requests   → Manage Borrow Requests
/staff/deadline-dashboard → Track Deadlines
/staff/students/{id}     → View Student Profile
```

### Student Routes
```
/student/dashboard       → Student Dashboard
/student/books           → Browse Books
/student/borrow-requests → My Requests
/student/active-borrows  → My Books
/student/subscription    → My Subscription
```

## Route Names (for navigation/redirects)

### Admin
- `route('admin.dashboard.index')`
- `route('admin.tiers.index')`
- `route('admin.users.index')`
- `route('admin.books.index')`
- `route('admin.subscriptions.index')`
- `route('admin.reports.index')`

### Staff
- `route('staff.dashboard.index')`
- `route('staff.borrow-requests.index')`
- `route('staff.deadline-dashboard.index')`
- `route('staff.students.show')`

### Student
- `route('student.dashboard.index')`
- `route('student.book-catalog.index')`
- `route('student.borrow-requests.index')`
- `route('student.active-borrows.index')`
- `route('student.subscription.index')`

## Middleware Protection

All role routes are protected by:
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(...)
Route::middleware(['auth', 'role:staff'])->prefix('staff')->group(...)
Route::middleware(['auth', 'role:student'])->prefix('student')->group(...)
```

## Authorization Response Codes

| Scenario | Response Code |
|----------|--------------|
| User has correct role | **200 OK** |
| User has wrong role | **403 Forbidden** |
| User not authenticated | **302 Redirect to /login** |
| Invalid route | **404 Not Found** |

## Navigation Display

The `resources/views/layouts/navigation.blade.php` displays:

```blade
@if(auth()->user()->role === 'admin')
  Dashboard → /admin/dashboard
@elseif(auth()->user()->role === 'staff')
  Dashboard → /staff/dashboard
@elseif(auth()->user()->role === 'student')
  Dashboard → /student/dashboard
  Books → /student/books
  My Books → /student/active-borrows
  Subscription → /student/subscription
  My Requests → /student/borrow-requests
@endif
```

## Adding New Routes

### To add a new admin route:
```php
Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('new-resource', [Controller::class, 'index'])->name('new-resource.index');
    // Route will be: /admin/new-resource
    // Route name: admin.new-resource.index
    // Automatically protected by role:admin middleware
});
```

### To add a shared authenticated route:
```php
Route::middleware('auth')->group(function () {
    Route::get('shared-page', [Controller::class, 'index'])->name('shared-page.index');
    // Accessible by any authenticated user
    // Not restricted by role
});
```

## Testing Authorization

### Using Artisan Tinker:
```bash
php artisan tinker

// Create test users
$admin = User::factory()->create(['role' => 'admin']);
$staff = User::factory()->create(['role' => 'staff']);
$student = User::factory()->create(['role' => 'student']);

// Now login and test: /admin/dashboard, /staff/dashboard, /student/dashboard
```

### Expected Results by Role:
- **Admin**: `/admin/dashboard` ✓ | `/staff/dashboard` ✗ | `/student/dashboard` ✗
- **Staff**: `/staff/dashboard` ✓ | `/admin/dashboard` ✗ | `/student/dashboard` ✗
- **Student**: `/student/dashboard` ✓ | `/admin/dashboard` ✗ | `/staff/dashboard` ✗

## Middleware Code

Located at: `app/Http/Middleware/RoleMiddleware.php`

```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized.');
    }
    return $next($request);
}
```

## Configuration

Registered in: `bootstrap/app.php`

```php
->withMiddleware(function (Middleware $middleware): void {
    $middleware->alias([
        'role' => \App\Http\Middleware\RoleMiddleware::class,
    ]);
})
```

## Common Tasks

### Redirect user to their dashboard after login:
```php
// In LoginController or similar
if (auth()->user()->role === 'admin') {
    return redirect('/admin/dashboard');
} elseif (auth()->user()->role === 'staff') {
    return redirect('/staff/dashboard');
} elseif (auth()->user()->role === 'student') {
    return redirect('/student/dashboard');
}
```

### Check user role in blade:
```blade
@if(auth()->user()->role === 'admin')
    <!-- Admin content -->
@elseif(auth()->user()->role === 'staff')
    <!-- Staff content -->
@elseif(auth()->user()->role === 'student')
    <!-- Student content -->
@endif
```

### Check user role in controller:
```php
if (auth()->user()->role === 'admin') {
    // Admin logic
}
```

---

**Last Updated:** April 15, 2026  
**Status:** ✅ All routes properly secured with role-based authorization
