<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPrivateMessageNotification extends Notification
{
    use Queueable;

    public function __construct(
        public User $sender,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_private_message',
            'title' => 'Nouveau message prive',
            'message' => "{$this->sender->name} vous a envoye un message prive.",
            'sender_name' => $this->sender->name,
            'sender_id' => $this->sender->id,
            'sender_url' => route('users.show', $this->sender),
            'url' => route('messages.conversation', $this->sender),
        ];
    }
}
