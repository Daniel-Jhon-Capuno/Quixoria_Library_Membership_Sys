<?php

namespace App\Notifications;

use App\Models\BorrowRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class BorrowRequestConfirmedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $borrowRequest;

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
        $mail = (new MailMessage)
            ->subject('Borrow Request Confirmed - ' . $this->borrowRequest->book->title)
            ->greeting('Good news, ' . $notifiable->name . '!')
            ->line('Your borrow request for "' . $this->borrowRequest->book->title . '" has been confirmed by our staff.')
            ->line('You can now pick up your book from the library.')
            ->line('Due Date: ' . $this->borrowRequest->due_at->format('M j, Y'))
            ->action('View Receipt', route('student.borrow-requests.receipt', $this->borrowRequest->id))
            ->action('View My Books', url('/student/active-borrows'))
            ->line('Thank you for using our library system!');

        // Attach PDF receipt if dompdf package is installed
        try {
            if (class_exists(\Barryvdh\DomPDF\Facade\Pdf::class)) {
                $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('student.borrow-requests.receipt', ['borrowRequest' => $this->borrowRequest]);
                $mail->attachData($pdf->output(), 'receipt-' . $this->borrowRequest->id . '.pdf', [
                    'mime' => 'application/pdf',
                ]);
            }
        } catch (\Throwable $e) {
            Log::warning('Failed to generate or attach receipt PDF', ['exception' => $e->getMessage(), 'borrow_request_id' => $this->borrowRequest->id]);
        }

        return $mail;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Borrow Request Confirmed',
            'message' => 'Your borrow request for "' . $this->borrowRequest->book->title . '" has been confirmed.',
            'action_url' => route('student.borrow-requests.receipt', $this->borrowRequest->id),
            'action_text' => 'View Receipt',
            'type' => 'borrow_request_confirmed',
            'borrow_request_id' => $this->borrowRequest->id,
            'receipt_url' => route('student.borrow-requests.receipt', $this->borrowRequest->id),
        ];
    }
}
