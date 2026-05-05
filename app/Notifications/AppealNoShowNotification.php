<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppealNoShowNotification extends Notification
{
    public function __construct(public $borrowRequest) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('You Missed Your Appeal Schedule')
            ->line('You did not show up for your return appeal for "' . $this->borrowRequest->book->title . '".')
            ->line('⚠️ You have ONE more chance to reschedule.')
            ->line('If you miss again, your account will be permanently restricted.')
            ->line('Please log in and reschedule immediately.')
            ->action('Reschedule Now', route('student.active-borrows.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appeal_no_show',
            'title' => 'Missed Appeal Schedule',
            'message' => 'You missed your appeal schedule for "' . $this->borrowRequest->book->title . '". You have ONE chance to reschedule.',
            'action_url' => route('student.active-borrows.index'),
        ];
    }
}

