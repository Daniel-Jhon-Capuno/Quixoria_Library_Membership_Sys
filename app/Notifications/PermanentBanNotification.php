<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PermanentBanNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $banReason;

    public function __construct($banReason)
    {
        $this->banReason = $banReason;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $libraryAddress = "Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur";

        return (new MailMessage)
            ->subject('🚫 Account Permanently Banned')
            ->line('Your account has been permanently banned due to:')
            ->line($this->banReason)
            ->line('')
            ->line('To appeal, visit us at:')
            ->line("📍 {$libraryAddress}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'permanent_ban',
            'reason' => $this->banReason,
        ];
    }
}
