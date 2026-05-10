<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseResolvedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;
    protected $amountPaid;

    public function __construct(BorrowRequest $borrowRequest, $amountCents = 0)
    {
        $this->borrowRequest = $borrowRequest;
        $this->amountPaid = $amountCents / 100;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('✅ Account Cleared!')
            ->line('Your case has been resolved.')
            ->line("Payment of ₱{$this->amountPaid} confirmed.")
            ->line('You can now borrow books again!')
            ->action('View Borrows', url('/student/active-borrows'))
            ->line('Thank you for visiting the branch.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'case_resolved',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'amount_paid' => $this->amountPaid,
        ];
    }
}
