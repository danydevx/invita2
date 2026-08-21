<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class EmailVerificationNotification extends Notification
{
    public function __construct(
        protected int $userId,
        protected string $email,
        protected ?int $createdAt = null
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $signedUrl = URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            [
                'id' => $this->userId,
                'hash' => sha1($this->email),
            ]
        );

        $firstName = explode(' ', $notifiable->name ?? 'Usuario')[0];

        return (new MailMessage)
            ->subject('Confirma tu cuenta - ' . app_name())
            ->greeting("¡Hola, {$firstName}!")
            ->line('Gracias por registrarte en ' . app_name() . '. Para activar tu cuenta, por favor confirma tu correo electrónico.')
            ->action('Confirmar mi cuenta', $signedUrl)
            ->line('Si el botón no funciona, copia y pega este enlace en tu navegador:')
            ->line($signedUrl)
            ->line('Este enlace expira en 60 minutos.')
            ->line('Si no creaste una cuenta, puedes ignorar este mensaje.')
            ->salutation('Saludos, ' . app_name());
    }
}
