<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentRejectedNotification extends Notification
{
    public function __construct(public $payment) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Rejected')
            ->line('Unfortunately your payment for ' . $this->payment->membershipTier->name . ' has been rejected.')
            ->line('Reason: ' . $this->payment->rejection_reason)
            ->line('Please try again or contact us for assistance.')
            ->action('View Subscription', route('student.subscription.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_rejected',
            'title' => '❌ Payment Rejected',
            'message' => 'Your payment for ' . $this->payment->membershipTier->name . ' was rejected. Reason: ' . $this->payment->rejection_reason,
            'action_url' => route('student.subscription.index'),
            'payment_id' => $this->payment->id,
        ];
    }
}
