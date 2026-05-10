<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LostBookReportedAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;

    public function __construct(BorrowRequest $borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $lateFeesPesos = ($this->borrowRequest->late_fee_charged ?? 0) / 100;
        $replacementFeePesos = ($this->borrowRequest->replacement_fee_cents ?? 0) / 100;
        $totalPesos = $lateFeesPesos + $replacementFeePesos;
        $borrowedDays = $this->borrowRequest->borrowed_at->diffInDays($this->borrowRequest->due_at);

        return (new MailMessage)
            ->subject('📚 Lost Book Reported — Admin Alert')
            ->line("Student: {$this->borrowRequest->student->name} ({$this->borrowRequest->student->email})")
            ->line("Book: {$this->borrowRequest->book->title}")
            ->line("Days Borrowed: {$borrowedDays} days")
            ->line("Late Fees: ₱{$lateFeesPesos}")
            ->line("Replacement: ₱{$replacementFeePesos}")
            ->line("Total Due: ₱{$totalPesos}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'lost_book_reported_admin',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'student_email' => $this->borrowRequest->student->email,
            'book_title' => $this->borrowRequest->book->title,
            'days_borrowed' => $this->borrowRequest->borrowed_at->diffInDays($this->borrowRequest->due_at),
            'late_fee' => $this->borrowRequest->late_fee_charged,
            'replacement_fee' => $this->borrowRequest->replacement_fee_cents,
        ];
    }
}
