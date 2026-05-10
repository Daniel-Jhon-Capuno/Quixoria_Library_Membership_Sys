<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookSeverelyOverdueNotification extends Notification implements ShouldQueue
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
            ->subject('🚨 SEVERELY OVERDUE')
            ->line("The book '{$this->borrowRequest->book->title}' is now 14 days overdue.")
            ->line("Late fee: ₱{$lateFeePesos}")
            ->line('🚨 Your account WILL be restricted in 16 days.')
            ->action('View Borrow', url('/student/active-borrows'))
            ->line('Visit the library immediately to resolve this.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_severely_overdue',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 14,
            'late_fee' => $this->borrowRequest->late_fee_charged,
        ];
    }
}
