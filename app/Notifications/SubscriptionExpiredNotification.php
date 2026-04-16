<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiredNotification extends Notification implements ShouldQueue
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
            ->subject('Subscription Expired')
            ->greeting('Subscription Expired Notice')
            ->line('Your library membership subscription has expired.')
            ->line('You have lost access to premium features and may have limited borrowing privileges.')
            ->line('Please renew your subscription to regain full access.')
            ->action('Renew Subscription', url('/subscription'))
            ->line('We hope to continue serving you!')
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
            'title' => 'Subscription Expired',
            'message' => 'Your library membership subscription has expired. Please renew to regain full access.',
            'action_url' => url('/subscription'),
            'action_text' => 'Renew Subscription',
            'type' => 'subscription_expired',
            'urgent' => true,
        ];
    }
}
