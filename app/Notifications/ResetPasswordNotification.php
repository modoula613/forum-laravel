<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $resetUrl = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire');

        return (new MailMessage)
            ->subject('Reinitialisation de votre mot de passe')
            ->greeting('Bonjour,')
            ->line('Vous recevez cet email parce qu\'une demande de reinitialisation du mot de passe a ete effectuee pour votre compte Sphere.')
            ->action('Reinitialiser le mot de passe', $resetUrl)
            ->line("Ce lien expirera dans {$expireMinutes} minutes.")
            ->line('Si vous n\'etes pas a l\'origine de cette demande, aucune autre action n\'est necessaire.')
            ->salutation('A bientot,');
    }
}
