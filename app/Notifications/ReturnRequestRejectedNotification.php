<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReturnRequestRejectedNotification extends Notification
{
    use Queueable;

    public $borrowRequest;

    public function __construct($borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->line('Your return request for "' . $this->borrowRequest->book->title . '" has been rejected.')
                    ->line('Reason: ' . $this->borrowRequest->rejection_reason)
                    ->line('Please contact staff to resolve the issue.')
                    ->action('View My Borrows', route('student.active-borrows.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'borrow_return_rejected',
            'title' => 'Return Request Rejected',
            'message' => 'Your return request for "' . $this->borrowRequest->book->title . '" was rejected. Reason: ' . $this->borrowRequest->rejection_reason,
            'action_url' => route('student.active-borrows.index'),
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'reason' => $this->borrowRequest->rejection_reason,
            'timestamp' => now(),
        ];
    }
}

