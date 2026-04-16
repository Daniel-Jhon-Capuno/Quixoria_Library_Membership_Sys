<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BorrowRequestConfirmedNotification extends Notification implements ShouldQueue
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
            ->subject('Borrow Request Confirmed')
            ->greeting('Good news!')
            ->line('Your borrow request has been confirmed by our staff.')
            ->line('You can now pick up your book from the library.')
            ->action('View My Books', url('/active-borrows'))
            ->line('Thank you for using our library system!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Borrow Request Confirmed',
            'message' => 'Your borrow request has been confirmed. You can now pick up your book from the library.',
            'action_url' => url('/active-borrows'),
            'action_text' => 'View My Books',
            'type' => 'borrow_request_confirmed',
        ];
    }
}
