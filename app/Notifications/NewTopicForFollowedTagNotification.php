<?php

namespace App\Notifications;

use App\Models\Tag;
use App\Models\Topic;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewTopicForFollowedTagNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Topic $topic,
        public Tag $tag,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'new_topic_followed_tag',
            'title' => 'Nouveau sujet dans un tag suivi',
            'message' => "Un nouveau sujet a ete publie dans le tag {$this->tag->name}.",
            'topic_id' => $this->topic->id,
            'topic_title' => $this->topic->title,
            'tag_name' => $this->tag->name,
            'url' => route('topics.show', $this->topic),
        ];
    }
}
