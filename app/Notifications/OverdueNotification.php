<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OverdueNotification extends Notification implements ShouldQueue
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
            ->subject('Overdue Book Notice')
            ->greeting('Urgent: Overdue Books')
            ->line('You have books that are currently overdue for return.')
            ->line('Late fees are accumulating daily. Please return your books as soon as possible.')
            ->action('View My Books', url('/active-borrows'))
            ->line('Contact the library if you need an extension.')
            ->salutation('Library Management Team');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Overdue Books Notice',
            'message' => 'You have books that are currently overdue. Late fees are accumulating.',
            'action_url' => url('/active-borrows'),
            'action_text' => 'View My Books',
            'type' => 'overdue_notice',
            'urgent' => true,
        ];
    }
}
