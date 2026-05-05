<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentSubmittedNotification extends Notification
{
    public function __construct(public $payment) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('New Payment Submitted')
            ->line('A new payment has been submitted by ' . $this->payment->user->name . '.')
            ->line('Tier: ' . $this->payment->membershipTier->name)
            ->line('Amount: ₱' . number_format($this->payment->amount, 2))
            ->line('Payment Method: ' . ucfirst(str_replace('_', ' ', $this->payment->payment_method)))
            ->line('Reference Number: ' . $this->payment->reference_number)
            ->action('Review Payment', route('admin.payments.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'payment_submitted',
            'title' => 'New Payment Submitted',
            'message' => $this->payment->user->name . ' submitted a payment for ' . $this->payment->membershipTier->name . ' (₱' . number_format($this->payment->amount, 2) . ')',
            'action_url' => route('admin.payments.index'),
            'payment_id' => $this->payment->id,
        ];
    }
}
