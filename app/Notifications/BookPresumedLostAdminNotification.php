<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookPresumedLostAdminNotification extends Notification implements ShouldQueue
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
        $lateFeesPesos = $this->borrowRequest->late_fee_charged / 100;
        $replacementFeePesos = ($this->borrowRequest->replacement_fee_cents ?? 0) / 100;
        $totalPesos = $lateFeesPesos + $replacementFeePesos;
        $borrowedDays = $this->borrowRequest->borrowed_at->diffInDays($this->borrowRequest->due_at);

        return (new MailMessage)
            ->subject('📚 Book Presumed Lost — Admin Alert')
            ->line("Student: {$this->borrowRequest->student->name} ({$this->borrowRequest->student->email})")
            ->line("Book: {$this->borrowRequest->book->title}")
            ->line("Days Overdue: 365")
            ->line('')
            ->line('Payment Breakdown:')
            ->line("Late Fees: ₱{$lateFeesPesos}")
            ->line("Replacement: ₱{$replacementFeePesos}")
            ->line("TOTAL: ₱{$totalPesos}")
            ->line('')
            ->line('Case requires immediate resolution.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_presumed_lost_admin',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'student_email' => $this->borrowRequest->student->email,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 365,
            'late_fee' => $this->borrowRequest->late_fee_charged,
            'replacement_fee' => $this->borrowRequest->replacement_fee_cents,
        ];
    }
}
