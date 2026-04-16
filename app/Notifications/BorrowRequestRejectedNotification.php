<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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
            ->subject('Borrow Request Rejected')
            ->greeting('We\'re sorry to inform you')
            ->line('Your borrow request has been rejected by our staff.')
            ->line('This could be due to book unavailability or other library policies.')
            ->action('Browse Books', url('/books'))
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
            'message' => 'Your borrow request has been rejected. Please try requesting another book.',
            'action_url' => url('/books'),
            'action_text' => 'Browse Books',
            'type' => 'borrow_request_rejected',
        ];
    }
}
