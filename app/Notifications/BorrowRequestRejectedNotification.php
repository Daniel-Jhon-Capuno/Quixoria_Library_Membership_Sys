<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(BorrowRequest $borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Borrow Request Rejected - ' . $this->borrowRequest->book->title)
            ->greeting('We\'re sorry, ' . $notifiable->name)
            ->line('Your borrow request for "' . $this->borrowRequest->book->title . '" has been rejected.')
            ->line('Reason: ' . ($this->borrowRequest->rejection_reason ?? 'No specific reason provided'))
            ->action('Browse Other Books', url('/student/books'))
            ->line('Please try requesting another book or contact staff for more information.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Borrow Request Rejected',
            'message' => 'Your request for "' . $this->borrowRequest->book->title . '" was rejected. Reason: ' . ($this->borrowRequest->rejection_reason ?? 'Not specified'),
            'action_url' => url('/student/books'),
            'action_text' => 'Browse Books',
            'type' => 'borrow_request_rejected',
        ];
    }
}
