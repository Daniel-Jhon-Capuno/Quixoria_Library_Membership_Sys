<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeadlineReminderNotification extends Notification implements ShouldQueue
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
        $daysUntilDue = now()->diffInDays($borrowRequest->due_at, false);

        return (new MailMessage)
            ->subject('Book Return Deadline Reminder')
            ->greeting("Hello {$notifiable->name}!")
            ->line("This is a reminder that your borrowed book \"{$borrowRequest->book->title}\" is due for return.")
            ->line("**Book Details:**")
            ->line("- Title: {$borrowRequest->book->title}")
            ->line("- Author: {$borrowRequest->book->author}")
            ->line("- Due Date: {$borrowRequest->due_at->format('F j, Y')}")
            ->when($daysUntilDue < 0, function ($mail) use ($daysUntilDue) {
                return $mail->line("⚠️ **OVERDUE by " . abs($daysUntilDue) . " days**")
                    ->line("Please return the book as soon as possible to avoid additional late fees.");
            })
            ->when($daysUntilDue === 0, function ($mail) {
                return $mail->line("📅 **Due today!** Please return the book by the end of today.");
            })
            ->when($daysUntilDue > 0, function ($mail) use ($daysUntilDue) {
                return $mail->line("📅 **{$daysUntilDue} days remaining** until the due date.");
            })
            ->action('View My Account', url('/dashboard'))
            ->line('Thank you for using our library services!')
            ->salutation('Best regards, Library Management Team');
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
            'book_title' => $this->borrowRequest->book->title,
            'due_at' => $this->borrowRequest->due_at->toISOString(),
            'message' => 'Book return deadline reminder',
        ];
    }
}