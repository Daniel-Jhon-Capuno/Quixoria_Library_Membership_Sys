<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Notifications\DeadlineReminderNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Carbon\Carbon;

#[Signature('reminders:deadlines')]
#[Description('Send deadline reminders for books due in 3 days and 1 day')]
class SendDeadlineReminders extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Sending deadline reminders...');

        // Get borrows due in 3 days (date only comparison)
        $threeDaysFromNow = Carbon::now()->addDays(3)->toDateString();
        $threeDayReminders = BorrowRequest::where('status', 'approved')
            ->whereDate('due_at', $threeDaysFromNow)
            ->with(['student', 'book'])
            ->get();

        $this->info("Found {$threeDayReminders->count()} books due in 3 days");

        foreach ($threeDayReminders as $borrow) {
            /** @var \App\Models\BorrowRequest $borrow */
            $borrow->student->notify(new DeadlineReminderNotification($borrow));
            $this->line("Sent 3-day reminder to {$borrow->student->email} for '{$borrow->book->title}'");
        }

        // Get borrows due in 1 day (date only comparison)
        $oneDayFromNow = Carbon::now()->addDay()->toDateString();
        $oneDayReminders = BorrowRequest::where('status', 'approved')
            ->whereDate('due_at', $oneDayFromNow)
            ->with(['student', 'book'])
            ->get();

        $this->info("Found {$oneDayReminders->count()} books due in 1 day");

        foreach ($oneDayReminders as $borrow) {
            /** @var \App\Models\BorrowRequest $borrow */
            $borrow->student->notify(new DeadlineReminderNotification($borrow));
            $this->line("Sent 1-day reminder to {$borrow->student->email} for '{$borrow->book->title}'");
        }

        $totalReminders = $threeDayReminders->count() + $oneDayReminders->count();
        $this->info("Successfully sent {$totalReminders} deadline reminders");
    }
}
