<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class UpgradeRefundNotification extends Notification
{
    public function __construct(public $payment, public $oldTierName) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Subscription Upgraded — Refund Information')
            ->line('Your subscription has been upgraded from ' . $this->oldTierName . ' to ' . $this->payment->membershipTier->name . '.')
            ->line('To claim your refund for your previous subscription, please visit us at:')
            ->line('📍 Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur')
            ->line('Please bring a valid ID and your receipt when you visit.')
            ->action('View My Subscription', route('student.subscription.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'upgrade_refund',
            'title' => '📋 Refund Available',
            'message' => 'Your subscription was upgraded. Visit our branch to claim your refund for your previous ' . $this->oldTierName . ' subscription.',
            'action_url' => route('student.subscription.index'),
            'payment_id' => $this->payment->id,
        ];
    }
}
