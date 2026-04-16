# Authorization Testing Guide

## Route Structure Verification

All routes have been reorganized with proper prefixes and middleware. Each role group is protected by the `role:rolename` middleware which enforces role-based access control.

### Route Organization

#### Admin Routes (Prefix: `/admin/`)
All admin routes are under the middleware group: `middleware(['auth', 'role:admin'])`
- `/admin/dashboard` - Admin dashboard
- `/admin/tiers` - Membership tier management
- `/admin/users` - User management  
- `/admin/books` - Book catalog management
- `/admin/subscriptions` - Subscription management
- `/admin/reports` - System reports

#### Staff Routes (Prefix: `/staff/`)
All staff routes are under the middleware group: `middleware(['auth', 'role:staff'])`
- `/staff/dashboard` - Staff dashboard
- `/staff/borrow-requests` - Borrow request management
- `/staff/deadline-dashboard` - Deadline tracking
- `/staff/students/{student}` - Student profile view

#### Student Routes (Prefix: `/student/`)
All student routes are under the middleware group: `middleware(['auth', 'role:student'])`
- `/student/dashboard` - Student personalized dashboard
- `/student/books` - Book catalog browsing
- `/student/borrow-requests` - Borrow request management
- `/student/active-borrows` - Active borrowing tracking
- `/student/subscription` - Subscription management
- `/student/reservations` - Book reservations

## Manual Testing Steps

### 1. Create Test Users
```bash
php artisan tinker
>>> $admin = User::factory()->create(['role' => 'admin']);
>>> $staff = User::factory()->create(['role' => 'staff']);
>>> $student = User::factory()->create(['role' => 'student']);
```

### 2. Test Admin Access
1. Login as admin user
2. Verify access to `/admin/dashboard` ✓ (should see admin dashboard)
3. Attempt to access `/staff/dashboard` → Should return **403 Forbidden**
4. Attempt to access `/student/dashboard` → Should return **403 Forbidden**

### 3. Test Staff Access
1. Logout and login as staff user
2. Verify access to `/staff/dashboard` ✓ (should see staff dashboard)
3. Attempt to access `/admin/dashboard` → Should return **403 Forbidden**
4. Attempt to access `/student/dashboard` → Should return **403 Forbidden**

### 4. Test Student Access
1. Logout and login as student user
2. Verify access to `/student/dashboard` ✓ (should see student dashboard)
3. Attempt to access `/admin/dashboard` → Should return **403 Forbidden**
4. Attempt to access `/staff/dashboard` → Should return **403 Forbidden**

### 5. Test Unauthenticated Access
1. Logout completely
2. Attempt to access any role-protected route
3. Should be redirected to `/login` page

## Middleware Implementation

The `RoleMiddleware` class (located at `app/Http/Middleware/RoleMiddleware.php`) handles role verification:

```php
public function handle(Request $request, Closure $next, ...$roles): Response
{
    if (!auth()->check() || !in_array(auth()->user()->role, $roles)) {
        abort(403, 'Unauthorized.');
    }
    return $next($request);
}
```

This middleware:
- Checks if user is authenticated
- Verifies user's role matches the required role(s)
- Returns 403 Forbidden if unauthorized
- Allows request to proceed if authorized

## Navigation Role-Based Display

The navigation blade file (`resources/views/layouts/navigation.blade.php`) conditionally displays navigation links based on the user's role:

- **Admin**: Shows "Dashboard" link pointing to `admin.dashboard.index`
- **Staff**: Shows "Dashboard" link pointing to `staff.dashboard.index`
- **Student**: Shows "Dashboard" and student-specific links (Books, My Books, Subscription, My Requests)
- **Unauthenticated**: Shows generic "Dashboard" link

## Expected Response Codes

| Scenario | Response |
|----------|----------|
| Authorized user accessing their role's route | 200 OK |
| Authenticated user accessing different role's route | 403 Forbidden |
| Unauthenticated user accessing protected route | 302 Redirect to login |
| Invalid route | 404 Not Found |

## Verification Checklist

- [x] All admin routes prefixed with `/admin/` and protected by `role:admin` middleware
- [x] All staff routes prefixed with `/staff/` and protected by `role:staff` middleware
- [x] All student routes prefixed with `/student/` and protected by `role:student` middleware
- [x] Public routes (login, register, forgot password) accessible without authentication
- [x] Profile routes accessible to authenticated users of any role
- [x] RoleMiddleware registered in `bootstrap/app.php`
- [x] Navigation displays role-specific links
- [x] 403 Forbidden response for unauthorized access

## Notes

- The `role:admin|staff` syntax can be used to allow multiple roles: `middleware(['auth', 'role:admin|staff'])`
- The generic `/dashboard` route is available for unauthenticated users (landing page)
- Profile and notification routes use only `middleware(['auth'])` allowing any authenticated user
