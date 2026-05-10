<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookOverdueDay3Notification extends Notification implements ShouldQueue
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
        $lateFeePesos = $this->borrowRequest->late_fee_charged / 100;

        return (new MailMessage)
            ->subject('⏰ Book Overdue Reminder')
            ->line("The book '{$this->borrowRequest->book->title}' is now 3 days overdue.")
            ->line("Late fee so far: ₱{$lateFeePesos}")
            ->line('Please return it immediately to avoid further penalties.')
            ->action('View Borrow', url('/student/active-borrows'))
            ->line('Thank you for using our library.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_overdue_day3',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 3,
            'late_fee' => $this->borrowRequest->late_fee_charged,
        ];
    }
}
