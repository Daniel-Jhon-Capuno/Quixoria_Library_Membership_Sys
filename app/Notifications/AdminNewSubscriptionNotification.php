<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Subscription;

class AdminNewSubscriptionNotification extends Notification implements ShouldQueue
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
        $user = $this->subscription->user;
        $tier = $this->subscription->membershipTier;

        return (new MailMessage)
                    ->subject('New subscription pending approval')
                    ->line("User {$user->name} ({$user->email}) requested the {$tier->name} tier.")
                    ->action('Review subscription', url('/admin/subscriptions/' . $this->subscription->id))
                    ->line('Please review and confirm or reject this subscription.');
    }

    public function toArray($notifiable)
    {
        return [
            'subscription_id' => $this->subscription->id,
            'user_id' => $this->subscription->user_id,
            'membership_tier_id' => $this->subscription->membership_tier_id,
        ];
    }
}
