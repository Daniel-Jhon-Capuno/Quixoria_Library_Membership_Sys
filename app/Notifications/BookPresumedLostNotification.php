<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookPresumedLostNotification extends Notification implements ShouldQueue
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
        $lateFeesPesos = $this->borrowRequest->late_fee_charged / 100;
        $replacementFeePesos = ($this->borrowRequest->replacement_fee_cents ?? 0) / 100;
        $totalPesos = $lateFeesPesos + $replacementFeePesos;
        $libraryAddress = "Ground Floor, City Library and Information Center building, 203 C. Bangoy St, Poblacion District, Davao City, 8000 Davao del Sur";

        return (new MailMessage)
            ->subject('🚨 FINAL NOTICE — BOOK PRESUMED LOST')
            ->line("The book '{$this->borrowRequest->book->title}' has been marked as PRESUMED LOST after 365 days.")
            ->line('')
            ->line('Payment Breakdown:')
            ->line("Late Fees: ₱{$lateFeesPesos}")
            ->line("Replacement Fee: ₱{$replacementFeePesos}")
            ->line("─────────────────────────────")
            ->line("TOTAL OWED: ₱{$totalPesos}")
            ->line('─────────────────────────────')
            ->line('You MUST visit our branch:')
            ->line("📍 {$libraryAddress}")
            ->line('Bring a valid ID.')
            ->line('Your account is permanently restricted until resolved.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'book_presumed_lost',
            'borrow_request_id' => $this->borrowRequest->id,
            'book_title' => $this->borrowRequest->book->title,
            'days_overdue' => 365,
            'late_fee' => $this->borrowRequest->late_fee_charged,
            'replacement_fee' => $this->borrowRequest->replacement_fee_cents,
        ];
    }
}
