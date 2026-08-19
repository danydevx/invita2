<?php

namespace App\Notifications;

use App\Models\Invitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private Invitation $invitation, private string $plainToken) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/invite/'.$this->plainToken);
        $expires = $this->invitation->expires_at?->toDateTimeString();
        $message = $this->invitation->metadata['message'] ?? null;

        $mail = (new MailMessage)
            ->subject('Invitacion a '.config('app.name'))
            ->line('Recibiste una invitacion para unirte a '.config('app.name').'.')
            ->action('Aceptar invitacion', $url);

        if ($message) {
            $mail->line($message);
        }

        if ($expires) {
            $mail->line('Esta invitacion expira en: '.$expires);
        }

        $mail->line('Si no esperabas esta invitacion, puedes ignorar este correo.');

        return $mail;
    }
}
