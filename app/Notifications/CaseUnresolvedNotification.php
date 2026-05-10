<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CaseUnresolvedNotification extends Notification implements ShouldQueue
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
            ->subject('🚨 CASE ESCALATED TO ADMIN')
            ->line("Your case has been escalated. The book '{$this->borrowRequest->book->title}' is 60 days overdue.")
            ->line("Total fees: ₱{$lateFeePesos}")
            ->line('You MUST visit the branch IMMEDIATELY:')
            ->line("📍 {$libraryAddress}")
            ->line('Bring a valid ID.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'case_unresolved',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 60,
            'late_fee' => $this->borrowRequest->late_fee_charged,
        ];
    }
}
