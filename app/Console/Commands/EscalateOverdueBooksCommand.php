<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Models\EscalationLog;
use App\Notifications\BookOverdueDay3Notification;
use App\Notifications\BookOverdueDay7Notification;
use App\Notifications\BookSeverelyOverdueNotification;
use App\Notifications\AccountRestrictedOverdueNotification;
use App\Notifications\CaseUnresolvedNotification;
use App\Notifications\BookPresumedLostNotification;
use App\Notifications\BookPresumedLostAdminNotification;
use App\Notifications\TemporaryUnblockExpiredNotification;
use Illuminate\Console\Command;

class EscalateOverdueBooksCommand extends Command
{
    protected $signature = 'escalate:overdue-books';
    protected $description = 'Check and escalate overdue books based on days overdue';

    public function handle()
    {
        $activeBorrows = BorrowRequest::with(['student', 'book'])
            ->whereIn('status', ['active', 'overdue'])
            ->get();

        $now = now();

        foreach ($activeBorrows as $borrow) {
            $daysOverdue = $borrow->due_at->diffInDays($now);

            // Day 3 overdue
            if ($daysOverdue === 3 && $borrow->escalation_level !== 'day3_reminded') {
                $borrow->update(['escalation_level' => 'day3_reminded']);
                EscalationLog::create([
                    'borrow_request_id' => $borrow->id,
                    'level' => 'day3_reminded',
                    'note' => 'Book is 3 days overdue',
                ]);
                $borrow->student->notify(new BookOverdueDay3Notification($borrow));
                $this->info("Day 3 reminder sent for {$borrow->book->title} to {$borrow->student->name}");
            }

            // Day 7 overdue
            if ($daysOverdue === 7 && $borrow->escalation_level !== 'day7_warned') {
                $borrow->update(['escalation_level' => 'day7_warned']);
                EscalationLog::create([
                    'borrow_request_id' => $borrow->id,
                    'level' => 'day7_warned',
                    'note' => 'Book is 7 days overdue',
                ]);
                $borrow->student->notify(new BookOverdueDay7Notification($borrow));
                
                // Notify staff
                $staffUsers = \App\Models\User::where('role', 'staff')->get();
                foreach ($staffUsers as $staff) {
                    $staff->notify(new \App\Notifications\BookOverdueDay7StaffNotification($borrow));
                }
                $this->info("Day 7 warning sent for {$borrow->book->title} to {$borrow->student->name}");
            }

            // Day 14 overdue - severely overdue
            if ($daysOverdue === 14 && $borrow->escalation_level !== 'severely_overdue') {
                $borrow->update(['escalation_level' => 'severely_overdue', 'status' => 'severely_overdue']);
                EscalationLog::create([
                    'borrow_request_id' => $borrow->id,
                    'level' => 'severely_overdue',
                    'note' => 'Book is 14 days overdue - marked as severely overdue',
                ]);
                $borrow->student->notify(new BookSeverelyOverdueNotification($borrow));
                
                // Notify admin
                $adminUsers = \App\Models\User::where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new \App\Notifications\BookSeverelyOverdueAdminNotification($borrow));
                }
                $this->info("Severely overdue warning sent for {$borrow->book->title} to {$borrow->student->name}");
            }

            // Day 30 overdue - auto-restrict account
            if ($daysOverdue === 30 && $borrow->escalation_level !== 'account_restricted') {
                $borrow->update(['escalation_level' => 'account_restricted']);
                EscalationLog::create([
                    'borrow_request_id' => $borrow->id,
                    'level' => 'account_restricted',
                    'note' => 'Book is 30 days overdue - account auto-restricted',
                ]);
                
                // Restrict account
                $borrow->student->update([
                    'is_restricted' => true,
                    'has_unpaid_fees' => true,
                    'restriction_reason' => 'Auto-restricted due to overdue book',
                    'restricted_at' => now(),
                ]);
                
                $borrow->student->notify(new AccountRestrictedOverdueNotification($borrow));
                
                // Notify admin
                $adminUsers = \App\Models\User::where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new \App\Notifications\AccountRestrictedOverdueAdminNotification($borrow));
                }
                $this->info("Account restricted for {$borrow->student->name} due to {$borrow->book->title}");
            }

            // Day 60 overdue - unresolved case escalation
            if ($daysOverdue === 60 && $borrow->escalation_level !== 'unresolved') {
                $borrow->update(['escalation_level' => 'unresolved', 'status' => 'unresolved']);
                EscalationLog::create([
                    'borrow_request_id' => $borrow->id,
                    'level' => 'unresolved',
                    'note' => 'Book is 60 days overdue - escalated to unresolved',
                ]);
                $borrow->student->notify(new CaseUnresolvedNotification($borrow));
                
                // Notify admin
                $adminUsers = \App\Models\User::where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new \App\Notifications\CaseUnresolvedAdminNotification($borrow));
                }
                $this->info("Case marked as unresolved for {$borrow->student->name} - {$borrow->book->title}");
            }

            // Day 365 overdue - presumed lost
            if ($daysOverdue === 365 && $borrow->escalation_level !== 'presumed_lost') {
                $borrow->update([
                    'escalation_level' => 'presumed_lost',
                    'status' => 'presumed_lost',
                    'replacement_fee_cents' => $borrow->book->price_cents ?? 0,
                ]);
                EscalationLog::create([
                    'borrow_request_id' => $borrow->id,
                    'level' => 'presumed_lost',
                    'note' => 'Book is 365 days overdue - marked as presumed lost',
                ]);
                
                // Reduce book inventory
                $borrow->book->update([
                    'total_copies' => max(0, $borrow->book->total_copies - 1),
                ]);
                
                $borrow->student->notify(new BookPresumedLostNotification($borrow));
                
                // Notify admin
                $adminUsers = \App\Models\User::where('role', 'admin')->get();
                foreach ($adminUsers as $admin) {
                    $admin->notify(new BookPresumedLostAdminNotification($borrow));
                }
                $this->info("Book presumed lost: {$borrow->book->title} from {$borrow->student->name}");
            }
        }

        // Check for temporary unblock expiry
        $temporarilyUnblocked = \App\Models\User::where('temporary_unblock_until', '<=', $now)
            ->whereNotNull('temporary_unblock_until')
            ->get();

        foreach ($temporarilyUnblocked as $user) {
            // Re-restrict the account
            $borrow = $user->borrowRequests()
                ->whereIn('status', ['lost', 'severely_overdue', 'unresolved', 'presumed_lost'])
                ->latest()
                ->first();

            if ($borrow) {
                $user->update([
                    'is_restricted' => true,
                    'temporary_unblock_until' => null,
                ]);
                $user->notify(new TemporaryUnblockExpiredNotification($borrow->book->title));
                $this->info("Temporary unblock expired for {$user->name} - account re-restricted");
            }
        }

        $this->info('Overdue book escalation check completed.');
    }
}
