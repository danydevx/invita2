<?php

namespace App\Notifications;

use App\Services\TemplateRenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $templates = app(TemplateRenderService::class);
        $variables = [
            'user_name' => (string) ($notifiable->name ?? ''),
            'user_email' => (string) ($notifiable->email ?? ''),
            'app_name' => (string) config('app.name'),
            'support_email' => (string) (config('mail.from.address') ?? ''),
            'date' => now()->toDateString(),
        ];

        $subject = $templates->renderSubject('user_welcome', $variables, 'Bienvenido a '.config('app.name'));
        $lines = $templates->renderLines('user_welcome', $variables);

        $mail = new MailMessage;
        $mail->subject($subject ?? 'Bienvenido a '.config('app.name'));

        if ($lines) {
            foreach ($lines as $line) {
                $mail->line($line);
            }
        } else {
            $mail->line('Tu cuenta ya esta activa y lista para usarse.');
            $mail->line('Si tienes alguna pregunta, responde a este correo.');
        }

        $mail->action('Ir al panel', url('/member'));

        return $mail;
    }
}
