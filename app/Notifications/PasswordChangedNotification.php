<?php

namespace App\Notifications;

use App\Services\TemplateRenderService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordChangedNotification extends Notification implements ShouldQueue
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

        $subject = $templates->renderSubject('password_changed', $variables, 'Tu contrasena fue actualizada');
        $lines = $templates->renderLines('password_changed', $variables);

        $mail = new MailMessage;
        $mail->subject($subject ?? 'Tu contrasena fue actualizada');

        if ($lines) {
            foreach ($lines as $line) {
                $mail->line($line);
            }
        } else {
            $mail->line('Te confirmamos que tu contrasena fue actualizada correctamente.')
                ->line('Si no realizaste este cambio, contacta soporte de inmediato.');
        }

        return $mail;
    }
}
