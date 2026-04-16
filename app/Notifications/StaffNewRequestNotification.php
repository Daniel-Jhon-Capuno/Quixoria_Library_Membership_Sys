<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StaffNewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public BorrowRequest $borrowRequest;

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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $borrowRequest = $this->borrowRequest;

        return (new MailMessage)
            ->subject('New Book Borrow Request')
            ->greeting("Hello {$notifiable->name}!")
            ->line("A new borrow request has been submitted by {$borrowRequest->student->name}.")
            ->line("**Request Details:**")
            ->line("- Student: {$borrowRequest->student->name} ({$borrowRequest->student->email})")
            ->line("- Book: {$borrowRequest->book->title}")
            ->line("- Author: {$borrowRequest->book->author}")
            ->line("- Requested: {$borrowRequest->created_at->format('M j, Y g:i A')}")
            ->action('Review Request', url('/staff/borrow-requests'))
            ->line('Please review and approve or reject this request.')
            ->salutation('Library Management System');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'book_title' => $this->borrowRequest->book->title,
            'message' => 'New borrow request submitted',
            'action_url' => '/staff/borrow-requests',
        ];
    }
}