<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TemporaryUnblockNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $daysUntilDeadline;
    protected $bookTitle;

    public function __construct($daysUntilDeadline, $bookTitle)
    {
        $this->daysUntilDeadline = $daysUntilDeadline;
        $this->bookTitle = $bookTitle;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('⚠️ Temporary Access Granted')
            ->line("Admin has given you {$this->daysUntilDeadline} days to resolve your case for '{$this->bookTitle}'.")
            ->line("Deadline: " . now()->addDays($this->daysUntilDeadline)->format('Y-m-d'))
            ->line('Your account will be automatically restricted again after this date.')
            ->line('Please visit the branch to fully resolve your case.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'temporary_unblock',
            'days' => $this->daysUntilDeadline,
            'book_title' => $this->bookTitle,
        ];
    }
}
