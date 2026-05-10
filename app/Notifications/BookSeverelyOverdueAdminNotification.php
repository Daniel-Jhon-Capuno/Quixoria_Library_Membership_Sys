<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookSeverelyOverdueAdminNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;

    public function __construct(BorrowRequest $borrowRequest)
    {
        $this->borrowRequest = $borrowRequest;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        $lateFeePesos = ($this->borrowRequest->late_fee_charged ?? 0) / 100;

        return (new MailMessage)
            ->subject('⚠️ Severely Overdue Case')
            ->line("Student: {$this->borrowRequest->student->name}")
            ->line("Book: {$this->borrowRequest->book->title}")
            ->line("Days: 14")
            ->line("Fees: ₱{$lateFeePesos}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_severely_overdue_admin',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'book_title' => $this->borrowRequest->book->title,
        ];
    }
}
