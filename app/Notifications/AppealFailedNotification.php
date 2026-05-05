<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class AppealFailedNotification extends Notification
{
    public function __construct(public $borrowRequest) {}

    public function via($notifiable) { return ['database', 'mail']; }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Account Restricted — Appeal Failed')
            ->line('Your return appeal for "' . $this->borrowRequest->book->title . '" has failed.')
            ->line('You did not show up for your scheduled visits twice.')
            ->line('🚫 Your account has been restricted. You can no longer borrow books.')
            ->line('To resolve this, please visit us in person at:')
            ->line('📍 Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur')
            ->line('Please bring the book when you visit.');
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'appeal_failed',
            'title' => '🚫 Account Restricted',
            'message' => 'Your appeal for "' . $this->borrowRequest->book->title . '" failed. Your account is now restricted. Please visit the branch.',
            'action_url' => route('student.active-borrows.index'),
        ];
    }
}

