<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Support\Facades\Lang;

class StaffNewReturnRequestNotification extends Notification
{
    use Queueable;

    public $borrowRequest;

    /**
     * Create a new notification instance.
     */
    public function __construct(BorrowRequest $borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
                    ->subject('New Return Request - ' . $this->borrowRequest->book->title)
                    ->line('**' . $this->borrowRequest->student->name . '** has requested to return "' . $this->borrowRequest->book->title . '"')
                    ->line('Due Date: ' . $this->borrowRequest->due_at->format('M j, Y'))
                    ->action('Review Request', route('staff.borrow-requests.index'))
                    ->line('Review in Staff Dashboard → Requests');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'return_requested',
            'title' => 'Return Request: ' . $this->borrowRequest->book->title,
            'message' => $this->borrowRequest->student->name . ' requested to return the book',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'book_title' => $this->borrowRequest->book->title,
            'due_date' => $this->borrowRequest->due_at->toDateString(),
        ];
    }

    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'type' => 'return_requested',
            'title' => 'Return Request: ' . $this->borrowRequest->book->title,
            'message' => $this->borrowRequest->student->name . ' requested to return the book',
            'borrow_request_id' => $this->borrowRequest->id,
        ]);
    }
}

