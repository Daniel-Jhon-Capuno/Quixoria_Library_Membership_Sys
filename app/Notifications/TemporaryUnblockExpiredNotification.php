<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemporaryUnblockExpiredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $bookTitle;

    public function __construct($bookTitle)
    {
        $this->bookTitle = $bookTitle;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $libraryAddress = "Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur";

        return (new MailMessage)
            ->subject('⏰ Temporary Access Expired')
            ->line('Your temporary access has ended.')
            ->line('Your account is restricted again.')
            ->line("Please visit the branch immediately regarding '{$this->bookTitle}':")
            ->line("📍 {$libraryAddress}")
            ->line('Bring a valid ID.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'temporary_unblock_expired',
            'book_title' => $this->bookTitle,
        ];
    }
}
