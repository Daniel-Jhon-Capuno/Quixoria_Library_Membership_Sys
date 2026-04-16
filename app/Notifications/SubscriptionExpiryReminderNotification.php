<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriptionExpiryReminderNotification extends Notification implements ShouldQueue
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
            ->subject('Subscription Expiry Reminder')
            ->greeting('Subscription Renewal Reminder')
            ->line('Your library membership subscription is expiring soon.')
            ->line('To continue enjoying our library services, please renew your subscription.')
            ->action('Manage Subscription', url('/subscription'))
            ->line('Don\'t lose access to your borrowed books and special privileges!')
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
            'title' => 'Subscription Expiry Reminder',
            'message' => 'Your library membership subscription is expiring soon. Please renew to continue enjoying our services.',
            'action_url' => url('/subscription'),
            'action_text' => 'Manage Subscription',
            'type' => 'subscription_expiry_reminder',
        ];
    }
}
