<?php

namespace App\Console\Commands;

use App\Models\BorrowRequest;
use App\Notifications\OverdueNotification;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('books:mark-overdue')]
#[Description('Mark overdue books and send notifications')]
class MarkOverdueBooks extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Marking overdue books...');

        // Get active borrows that are past due date
        $overdueBorrows = BorrowRequest::where('status', 'approved')
            ->where('due_at', '<', now())
            ->with(['student', 'book'])
            ->get();

        $this->info("Found {$overdueBorrows->count()} overdue books");

        foreach ($overdueBorrows as $borrow) {
            /** @var \App\Models\BorrowRequest $borrow */
            // Update status to overdue
            $borrow->update(['status' => 'overdue']);

            // Send overdue notification
            $borrow->student->notify(new OverdueNotification());

            $this->line("Marked '{$borrow->book->title}' as overdue for {$borrow->student->email}");
        }

        $this->info("Successfully processed {$overdueBorrows->count()} overdue books");
    }
}
