<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReturnAppealScheduledNotification extends Notification
{
    public function __construct(public $borrowRequest) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $scheduledAt = \Carbon\Carbon::parse($this->borrowRequest->appeal_scheduled_at)->format('M j, Y g:i A');

        return (new MailMessage)
            ->subject('Appeal Scheduled — Please Visit the Branch')
            ->line('Your return appeal for "' . $this->borrowRequest->book->title . '" has been scheduled.')
            ->line('📅 Please visit us on: ' . $scheduledAt)
            ->line('📍 Branch Address: 123 Library St, Quezon City') // Change this to your actual address
            ->line('Please bring the book and this confirmation when you visit.')
            ->line('Appeal Reason on File: ' . $this->borrowRequest->appeal_reason)
            ->action('View My Borrows', route('student.active-borrows.index'));
    }

    public function toArray($notifiable)
    {
        $scheduledAt = \Carbon\Carbon::parse($this->borrowRequest->appeal_scheduled_at)->format('M j, Y g:i A');

        return [
            'type' => 'return_appeal_scheduled',
            'title' => 'Appeal Scheduled',
            'message' => 'Your appeal for "' . $this->borrowRequest->book->title . '" is scheduled. Please visit the branch on ' . $scheduledAt . ' at 123 Library St, Quezon City.',
            'action_url' => route('student.active-borrows.index'),
            'borrow_request_id' => $this->borrowRequest->id,
            'scheduled_at' => $scheduledAt,
        ];
    }
}

