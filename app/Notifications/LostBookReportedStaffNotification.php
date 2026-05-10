<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LostBookReportedStaffNotification extends Notification implements ShouldQueue
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
        $totalPesos = (($this->borrowRequest->late_fee_charged ?? 0) + ($this->borrowRequest->replacement_fee_cents ?? 0)) / 100;

        return (new MailMessage)
            ->subject('📚 Lost Book Reported')
            ->line("Student: {$this->borrowRequest->student->name}")
            ->line("Book: {$this->borrowRequest->book->title}")
            ->line("Total Due: ₱{$totalPesos}")
            ->line('Please process when student visits the branch.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'lost_book_reported_staff',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'book_title' => $this->borrowRequest->book->title,
            'total_due' => ($this->borrowRequest->late_fee_charged ?? 0) + ($this->borrowRequest->replacement_fee_cents ?? 0),
        ];
    }
}
