<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Subscription;

class StudentSubscriptionConfirmedNotification extends Notification implements ShouldQueue
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
        $tier = $this->subscription->membershipTier;

        return (new MailMessage)
                    ->subject('Your subscription is active')
                    ->line("Your subscription to the {$tier->name} tier has been approved and is now active.")
                    ->line('You can now borrow according to your membership limits.')
                    ->action('View subscription', url('/student/subscription'));
    }

    public function toArray($notifiable)
    {
        return [
            'subscription_id' => $this->subscription->id,
            'membership_tier_id' => $this->subscription->membership_tier_id,
            'status' => $this->subscription->status,
        ];
    }
}
