<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppealCompletedNotification extends Notification
{
    public function __construct(public $borrowRequest) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Return Appeal Completed')
            ->line('Your return appeal for "' . $this->borrowRequest->book->title . '" has been completed.')
            ->line('Thank you for visiting the branch. The book has been marked as returned.')
            ->action('View My Borrows', route('student.active-borrows.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appeal_completed',
            'title' => 'Return Appeal Completed',
            'message' => 'Your return appeal for "' . $this->borrowRequest->book->title . '" has been completed. Book marked as returned.',
            'action_url' => route('student.active-borrows.index'),
        ];
    }
}

