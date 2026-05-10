<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookOverdueDay7Notification extends Notification implements ShouldQueue
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
            ->subject('⚠️ URGENT: Book Overdue')
            ->line("The book '{$this->borrowRequest->book->title}' is now 7 days overdue.")
            ->line("Late fee: ₱{$lateFeePesos} and growing.")
            ->line('⚠️ Your account will be restricted in 23 days if the book is not returned.')
            ->action('View Borrow', url('/student/active-borrows'))
            ->line('Please return the book immediately.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_overdue_day7',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 7,
            'late_fee' => $this->borrowRequest->late_fee_charged,
        ];
    }
}
