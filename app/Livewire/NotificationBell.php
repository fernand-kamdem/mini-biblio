<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

/**
 * Cloche de notifications (header).
 *
 * Rôle :
 * - Afficher le nombre de notifications non lues (badge)
 * - Lister les 10 dernières non lues dans un dropdown
 * - Marquer comme lue (une / toutes) et rediriger vers l’URL du payload
 *
 * Source des données : table `notifications` via le trait Notifiable de User
 * (`unreadNotifications`, `markAsRead`).
 *
 * i18n : le texte affiché vient de `data.message_key` traduit avec __()
 * au moment de l’affichage (locale courante), pas d’un texte figé en base.
 *
 * Vue : resources/views/livewire/notification-bell.blade.php
 * Inclusion : layouts/partials/header.blade.php → <livewire:notification-bell />
 */
class NotificationBell extends Component
{
    /** Nombre de notifications non lues (badge rouge). */
    public int $unreadCount = 0;

    /**
     * Entrées du dropdown (déjà formatées pour la vue).
     *
     * @var array<int, array{id: string, message: string, url: ?string, book_title: ?string, member_name: ?string, created_at: ?string}>
     */
    public array $items = [];

    /**
     * Au montage du composant : charge count + liste.
     */
    public function mount(): void
    {
        $this->refreshNotifications();
    }

    /**
     * Recharge le compteur et les 10 dernières notifications non lues.
     * Appelé au mount, à l’ouverture de la cloche, et après markAsRead / markAllAsRead.
     */
    public function refreshNotifications(): void
    {
        $user = Auth::user();

        if (! $user) {
            $this->unreadCount = 0;
            $this->items = [];

            return;
        }

        // Badge = total non lues (pas seulement les 10 affichées)
        $this->unreadCount = $user->unreadNotifications()->count();

        // Liste dropdown : max 10, plus récentes d’abord
        $this->items = $user->unreadNotifications()
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($notification) {
                $data = $notification->data ?? [];

                // Préférer message_key (i18n) ; fallback sur ancien champ message texte
                $message = isset($data['message_key'])
                    ? __($data['message_key'], $data['message_replace'] ?? [])
                    : ($data['message'] ?? __('Notification'));

                return [
                    'id' => $notification->id,
                    'message' => $message,
                    'url' => $data['url'] ?? null,
                    'book_title' => $data['book_title'] ?? null,
                    'member_name' => $data['member_name'] ?? null,
                    'created_at' => $notification->created_at?->diffForHumans(),
                ];
            })
            ->values()
            ->all();
    }

    /**
     * Marque une notification comme lue, rafraîchit la cloche,
     * puis redirige vers data.url si présente (ex. liste des emprunts).
     */
    public function markAsRead(string $notificationId): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        // Sécurité : uniquement les notifs du user connecté
        $notification = $user->notifications()->whereKey($notificationId)->first();

        if (! $notification) {
            return;
        }

        $notification->markAsRead(); // renseigne read_at

        $url = $notification->data['url'] ?? null;

        $this->refreshNotifications();

        if ($url) {
            $this->redirect($url);
        }
    }

    /**
     * Marque toutes les notifications non lues du user comme lues.
     */
    public function markAllAsRead(): void
    {
        $user = Auth::user();

        if (! $user) {
            return;
        }

        $user->unreadNotifications->markAsRead();
        $this->refreshNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bell');
    }
}
