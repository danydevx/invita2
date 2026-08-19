<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetCodeNotification extends Notification
{
    public function __construct(
        protected string $token,
        protected string $code
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = url('/reset-password/'.$this->token);

        return (new MailMessage)
            ->subject('Restablece tu contrasena')
            ->line('Recibimos una solicitud para restablecer tu contrasena.')
            ->line('Tu codigo de verificacion es: '.$this->code)
            ->action('Restablecer contrasena', $url)
            ->line('Este enlace expirara en 30 minutos.')
            ->line('Si no solicitaste este cambio, ignora este correo.');
    }
}
