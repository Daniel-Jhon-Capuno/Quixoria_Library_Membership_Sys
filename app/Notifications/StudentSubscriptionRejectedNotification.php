<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Subscription;

class StudentSubscriptionRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Subscription $subscription)
    {
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Subscription request rejected')
                    ->line('Your subscription request was rejected by an administrator.')
                    ->line($this->subscription->rejection_reason ?? '')
                    ->action('View subscription', url('/student/subscription'));
    }

    public function toArray($notifiable)
    {
        return [
            'subscription_id' => $this->subscription->id,
            'status' => $this->subscription->status,
            'reason' => $this->subscription->rejection_reason ?? null,
        ];
    }
}
