<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseUnresolvedAdminNotification extends Notification implements ShouldQueue
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
            ->subject('🚨 UNRESOLVED CASE — ACTION NEEDED')
            ->line("Student: {$this->borrowRequest->student->name}")
            ->line("Email: {$this->borrowRequest->student->email}")
            ->line("Book: {$this->borrowRequest->book->title}")
            ->line("Days Overdue: 60")
            ->line("Total Fees: ₱{$lateFeePesos}")
            ->line('Manual intervention required.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'case_unresolved_admin',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'student_email' => $this->borrowRequest->student->email,
            'book_title' => $this->borrowRequest->book->title,
        ];
    }
}
