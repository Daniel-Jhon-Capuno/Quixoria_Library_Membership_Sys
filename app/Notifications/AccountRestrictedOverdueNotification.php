<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountRestrictedOverdueNotification extends Notification implements ShouldQueue
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
        $lateFeePesos = $this->borrowRequest->late_fee_charged / 100;
        $libraryAddress = "Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur";

        return (new MailMessage)
            ->subject('🚫 Account Restricted')
            ->line("Your account has been restricted due to severely overdue book: '{$this->borrowRequest->book->title}' (30 days overdue).")
            ->line("Total fees: ₱{$lateFeePesos}")
            ->line('Visit us immediately to resolve this issue:')
            ->line("📍 {$libraryAddress}")
            ->line('Bring a valid ID.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'account_restricted_overdue',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 30,
            'late_fee' => $this->borrowRequest->late_fee_charged,
        ];
    }
}
