<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class StudentPermanentlyBannedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $student;
    protected $reason;
    protected $bannedBy;

    public function __construct($student, $reason, $bannedBy = null)
    {
        $this->student = $student;
        $this->reason = $reason;
        $this->bannedBy = $bannedBy ?? auth()->user()->name;
    }

    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Alert: Student Permanently Banned')
            ->line("Student {$this->student->name} ({$this->student->email}) has been permanently banned.")
            ->line("Reason: {$this->reason}")
            ->line("Banned by: {$this->bannedBy}");
    }

    public function toDatabase($notifiable)
    {
        return [
            'type' => 'student_permanently_banned',
            'student_name' => $this->student->name,
            'student_email' => $this->student->email,
            'reason' => $this->reason,
        ];
    }
}
