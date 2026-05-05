<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppealFailedAdminNotification extends Notification
{
    public function __construct(public $borrowRequest) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Student Account Restricted - Appeal Failed')
            ->line('Student ' . $this->borrowRequest->student->name . ' (' . $this->borrowRequest->student->email . ') has been restricted.')
            ->line('Book: ' . $this->borrowRequest->book->title)
            ->line('Reason: Failed to show up for return appeal twice')
            ->action('View Appeals Dashboard', route('staff.appeals.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appeal_admin_restriction',
            'title' => 'Student Account Restricted',
            'message' => $this->borrowRequest->student->name . ' restricted for failing appeal: ' . $this->borrowRequest->book->title,
            'action_url' => route('staff.appeals.index'),
        ];
    }
}

