<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmailNotification extends VerifyEmail
{
    use Queueable;

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verifie ton adresse e-mail')
            ->greeting('Bienvenue sur Sphere,')
            ->line('Merci pour ton inscription. Confirme ton adresse e-mail pour securiser ton compte et recevoir les messages importants.')
            ->action('Verifier mon adresse e-mail', $this->verificationUrl($notifiable))
            ->line('Ce lien de verification reste valable pendant 60 minutes.')
            ->line('Si tu n as pas cree de compte sur Sphere, tu peux ignorer cet e-mail.');
    }

    protected function verificationUrl($notifiable): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(60),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
