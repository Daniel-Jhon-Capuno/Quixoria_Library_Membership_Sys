# Student Book Return Feature Plan

**Status**: Planning (awaiting approval)

**Information Gathered**:
- `student.active-borrows.index` shows "My Borrows" cards (Renew/View only, no Return)
- Staff `BorrowRequestController` has `checkIn()` (physical return)
- BorrowRequest model: `returned_at`, `is_damaged`
- Sidebar has "My Borrows" (`student.active-borrows.index`)

**Plan**:
1. **Student side** (`resources/views/student/active-borrows/index.blade.php`):
   - Add "Request Return" button alongside Renew
2. **Student controller** (`app/Http/Controllers/Student/ActiveBorrowController.php`):
   - Add `requestReturn()`: Set `status` = 'return_requested'
3. **Staff side** (`app/Http/Controllers/Staff/BorrowRequestController.php`):
   - Add return confirmation form with damage checkbox + reason/amount fields
   - `confirmReturn()`: Update status='returned', add late fee if needed
4. **Migration** (if needed): Add `return_rejection_reason`, `return_damage_fee`
5. **Notifications**: Return requested, confirmed/rejected

**Dependent Files**:
- views/student/active-borrows/index.blade.php
- controllers/Student/ActiveBorrowController.php  
- controllers/Staff/BorrowRequestController.php
- routes/web.php

**Followup**:
- `php artisan migrate`
- `php artisan serve` + test flow
- Update TODO.md with progress

Approve plan to proceed?
