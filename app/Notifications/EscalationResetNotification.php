<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class EscalationResetNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;
    protected $newDueDate;

    public function __construct(BorrowRequest $borrowRequest, $newDueDate)
    {
        $this->borrowRequest = $borrowRequest;
        $this->newDueDate = $newDueDate;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('🔄 Case Reset')
            ->line('Admin has reset your case.')
            ->line("New due date: {$this->newDueDate}")
            ->line("Please return '{$this->borrowRequest->book->title}' by this date to avoid further escalation.");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'escalation_reset',
            'borrow_request_id' => $this->borrowRequest->id,
            'new_due_date' => $this->newDueDate,
            'book_title' => $this->borrowRequest->book->title,
        ];
    }
}
