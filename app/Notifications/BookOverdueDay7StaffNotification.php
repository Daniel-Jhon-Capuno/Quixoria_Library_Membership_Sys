<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookOverdueDay7StaffNotification extends Notification implements ShouldQueue
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
            ->subject('⚠️ Book Overdue — 7 Days')
            ->line("Student {$this->borrowRequest->student->name} has not returned '{$this->borrowRequest->book->title}' for 7 days.")
            ->line("Late fee: ₱{$lateFeePesos}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_overdue_day7_staff',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 7,
        ];
    }
}
