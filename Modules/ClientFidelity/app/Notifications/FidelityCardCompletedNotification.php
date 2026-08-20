<?php

namespace Modules\ClientFidelity\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FidelityCardCompletedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public $card,
        public $business
    ) {}

    public function via(object $notifiable): array
    {
        return ['database', 'mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Tarjeta de fidelidad completada!')
            ->greeting('¡Felicidades!')
            ->line("El cliente {$this->card->client_name} ha completado su tarjeta de fidelidad en {$this->business->name}.")
            ->line("Numero de visitas: {$this->card->max_visits}")
            ->line("Premio/discount: {$this->card->description}")
            ->action('Ver tarjeta', url("/member/listings/{$this->business->id}/fidelity-cards/{$this->card->id}"))
            ->line('No olvides entregar el premio o descuento al cliente.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'type' => 'fidelity_card_completed',
            'card_id' => $this->card->id,
            'listing_id' => $this->business->id,
            'client_name' => $this->card->client_name,
            'message' => "El cliente {$this->card->client_name} ha completado su tarjeta de fidelidad.",
        ];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'card_id' => $this->card->id,
            'listing_id' => $this->business->id,
            'client_name' => $this->card->client_name,
            'max_visits' => $this->card->max_visits,
            'description' => $this->card->description,
        ];
    }
}
