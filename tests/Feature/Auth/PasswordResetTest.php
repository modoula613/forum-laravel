<?php

use App\Notifications\ResetPasswordNotification;
use App\Models\User;
use Illuminate\Support\Facades\Notification;

test('reset password link screen can be rendered', function () {
    $response = $this->get('/forgot-password');

    $response->assertStatus(200);
});

test('reset password link can be requested', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class);
});

test('reset password screen can be rendered', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) {
        $response = $this->get('/reset-password/'.$notification->token);

        $response->assertStatus(200);

        return true;
    });
});

test('reset password screen reuses the email from the reset link', function () {
    $user = User::factory()->create();

    $response = $this->get('/reset-password/demo-token?email='.urlencode($user->email));

    $response
        ->assertOk()
        ->assertSee('Adresse associee')
        ->assertSee($user->email)
        ->assertDontSee('Adresse e-mail');
});

test('password can be reset with valid token', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
        $response = $this->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => 'Password1!',
            'password_confirmation' => 'Password1!',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        return true;
    });
});

test('password reset rejects unsafe password patterns', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
        $response = $this->from('/reset-password/'.$notification->token)->post('/reset-password', [
            'token' => $notification->token,
            'email' => $user->email,
            'password' => '<?php echo 1; ?>Aa1!',
            'password_confirmation' => '<?php echo 1; ?>Aa1!',
        ]);

        $response
            ->assertRedirect('/reset-password/'.$notification->token)
            ->assertSessionHasErrors([
                'password' => 'Le mot de passe contient des sequences interdites.',
            ]);

        return true;
    });
});

test('password reset email is written in french', function () {
    Notification::fake();

    $user = User::factory()->create();

    $this->post('/forgot-password', ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
        $mail = $notification->toMail($user);

        expect($mail->subject)->toBe('Reinitialisation de votre mot de passe');
        expect($mail->introLines)->toContain('Vous recevez cet email parce qu\'une demande de reinitialisation du mot de passe a ete effectuee pour votre compte Sphere.');
        expect($mail->actionText)->toBe('Reinitialiser le mot de passe');

        return true;
    });
});
