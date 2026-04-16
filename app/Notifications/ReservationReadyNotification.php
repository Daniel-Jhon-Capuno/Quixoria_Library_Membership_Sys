<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReservationReadyNotification extends Notification implements ShouldQueue
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
            ->subject('Reserved Book Available')
            ->greeting('Great News!')
            ->line('A book you reserved is now available for pickup.')
            ->line('Please visit the library within 3 days to pick up your reserved book.')
            ->line('After 3 days, the reservation may be cancelled and the book made available to others.')
            ->action('View Reservations', url('/reservations'))
            ->line('Thank you for using our reservation service!')
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
            'title' => 'Reserved Book Available',
            'message' => 'A book you reserved is now available for pickup. Please visit the library within 3 days.',
            'action_url' => url('/reservations'),
            'action_text' => 'View Reservations',
            'type' => 'reservation_ready',
        ];
    }
}
