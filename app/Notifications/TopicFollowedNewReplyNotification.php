<?php

namespace App\Notifications;

use App\Models\Topic;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TopicFollowedNewReplyNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Topic $topic,
        public User $replyUser,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'followed_topic_new_reply',
            'title' => 'Nouvelle reponse sur un sujet suivi',
            'message' => "{$this->replyUser->name} a repondu a un sujet que vous suivez.",
            'topic_id' => $this->topic->id,
            'topic_title' => $this->topic->title,
            'reply_user' => $this->replyUser->name,
            'url' => route('topics.show', $this->topic),
        ];
    }
}
