<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserWarnedNotification extends Notification
{
    use Queueable;

    public function __construct(
        public int $warningCount,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'Sujet supprime par un administrateur',
            'message' => 'Un de vos sujets a ete supprime par un administrateur.',
            'warning_count' => $this->warningCount,
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Avertissement sur votre compte')
            ->line('Un de vos sujets a ete supprime par un administrateur.')
            ->line("Nombre d'avertissements actuels : {$this->warningCount}");
    }
}
