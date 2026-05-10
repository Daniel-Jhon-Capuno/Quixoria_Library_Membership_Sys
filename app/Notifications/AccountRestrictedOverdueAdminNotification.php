<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRestrictedOverdueAdminNotification extends Notification implements ShouldQueue
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
            ->subject('🚫 Student Auto-Restricted')
            ->line("{$this->borrowRequest->student->name} auto-restricted due to 30 days overdue: '{$this->borrowRequest->book->title}'")
            ->line("Total fees: ₱{$lateFeePesos}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'account_restricted_overdue_admin',
            'borrow_request_id' => $this->borrowRequest->id,
            'student_name' => $this->borrowRequest->student->name,
            'book_title' => $this->borrowRequest->book->title,
        ];
    }
}
