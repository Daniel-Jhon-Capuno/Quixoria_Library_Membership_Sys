<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LostBookReportedNotification extends Notification implements ShouldQueue
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
        $lateFeesPesos = ($this->borrowRequest->late_fee_charged ?? 0) / 100;
        $replacementFeePesos = ($this->borrowRequest->replacement_fee_cents ?? 0) / 100;
        $totalPesos = $lateFeesPesos + $replacementFeePesos;
        $libraryAddress = "Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur";

        return (new MailMessage)
            ->subject('📚 Lost Book Report Submitted')
            ->line("You reported '{$this->borrowRequest->book->title}' as lost.")
            ->line('')
            ->line('Payment Breakdown:')
            ->line("Late Fees: ₱{$lateFeesPesos}")
            ->line("Replacement Fee: ₱{$replacementFeePesos}")
            ->line("─────────────────────────────")
            ->line("Total Due: ₱{$totalPesos}")
            ->line('─────────────────────────────')
            ->line('Your account is now restricted.')
            ->line('Please visit us to settle:')
            ->line("📍 {$libraryAddress}")
            ->line('Bring a valid ID and this notification.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'lost_book_reported',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'late_fee' => $this->borrowRequest->late_fee_charged,
            'replacement_fee' => $this->borrowRequest->replacement_fee_cents,
        ];
    }
}
