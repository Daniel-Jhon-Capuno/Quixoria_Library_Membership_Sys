<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ReturnAppealSubmittedNotification extends Notification
{
    public function __construct(public $borrowRequest) {}

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Return Appeal Submitted')
            ->line('Student ' . $this->borrowRequest->student->name . ' has submitted a return appeal.')
            ->line('Book: ' . $this->borrowRequest->book->title)
            ->line('Appeal Reason: ' . $this->borrowRequest->appeal_reason)
            ->line('Scheduled Visit: ' . \Carbon\Carbon::parse($this->borrowRequest->appeal_scheduled_at)->format('M j, Y g:i A'))
            ->action('View Borrow Requests', route('staff.borrow-requests.index'));
    }

    public function toArray($notifiable)
    {
        return [
            'type' => 'return_appeal_submitted',
            'title' => 'Return Appeal Submitted',
            'message' => $this->borrowRequest->student->name . ' appealed a rejected return for "' . $this->borrowRequest->book->title . '" — scheduled ' . \Carbon\Carbon::parse($this->borrowRequest->appeal_scheduled_at)->format('M j, Y g:i A'),
            'action_url' => route('staff.borrow-requests.index'),
            'borrow_request_id' => $this->borrowRequest->id,
        ];
    }
}

