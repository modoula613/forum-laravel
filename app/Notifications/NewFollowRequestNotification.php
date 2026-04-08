<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewFollowRequestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $requester,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'follow_request',
            'title' => 'Nouvelle demande de suivi',
            'message' => "{$this->requester->name} souhaite vous suivre.",
            'requester_name' => $this->requester->name,
            'requester_id' => $this->requester->id,
            'requester_url' => route('users.show', $this->requester),
            'url' => route('users.show', $this->requester),
        ];
    }
}
