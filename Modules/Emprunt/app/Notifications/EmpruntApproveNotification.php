<?php

namespace Modules\Emprunt\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Modules\Emprunt\Models\Emprunt;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Notification database : demande d'emprunt approuvée.
 *
 * Destinataire (via le listener) : le membre propriétaire (member.user).
 * Contenu stocké dans notifications.data (toArray).
 */
class EmpruntApproveNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Emprunt $emprunt) {}

    /**
     * Canal(ux) de livraison.
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Payload JSON enregistré en base pour le destinataire.
     */
    public function toArray(object $notifiable): array
    {
        $emprunt = $this->emprunt->loadMissing(['book', 'member.user']);

        return [
            'type' => 'emprunt_approuve',
            'emprunt_id' => $emprunt->id,
            'book_title' => $emprunt->book?->title,
            'member_name' => $emprunt->member?->user?->name,
            'message_key' => 'notifications.emprunt_approve',
            'url' => route('emprunt-mesemprunts.list'),
        ];
    }
}
