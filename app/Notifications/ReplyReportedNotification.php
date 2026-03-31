<?php

namespace App\Notifications;

use App\Models\Reply;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class ReplyReportedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public Reply $reply,
        public string $reason,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'reply_reported',
            'title' => 'Reponse signalee',
            'message' => 'Une reponse a ete signalee et demande une moderation.',
            'reply_id' => $this->reply->id,
            'reply_content' => $this->reply->content,
            'topic_id' => $this->reply->topic_id,
            'topic_title' => $this->reply->topic?->title,
            'reason' => $this->reason,
            'url' => $this->reply->topic ? route('topics.show', $this->reply->topic) : null,
        ];
    }
}
