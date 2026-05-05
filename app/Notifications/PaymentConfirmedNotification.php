<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentConfirmedNotification extends Notification
{
    public function __construct(public $payment) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Payment Confirmed — Subscription Active!')
            ->line('Your payment for ' . $this->payment->membershipTier->name . ' has been confirmed!')
            ->line('Amount Paid: ₱' . number_format($this->payment->amount, 2))
            ->line('Your subscription is now active. Happy reading!')
            ->action('View My Subscription', route('student.subscription.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_confirmed',
            'title' => '✅ Subscription Activated!',
            'message' => 'Your payment for ' . $this->payment->membershipTier->name . ' has been confirmed. Your subscription is now active!',
            'action_url' => route('student.subscription.index'),
            'payment_id' => $this->payment->id,
        ];
    }
}
