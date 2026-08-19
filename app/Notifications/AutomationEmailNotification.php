<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AutomationEmailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        protected string $subject,
        protected string $message
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = new MailMessage;
        $mail->subject($this->subject);

        // Separa el contenido en lineas para respetar saltos de linea en la plantilla.
        $lines = preg_split("/\r\n|\r|\n/", $this->message);
        foreach ($lines as $line) {
            $mail->line($line);
        }

        return $mail;
    }
}
