<?php

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users can register', function () {
    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password1!',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration shows a clear error when password confirmation does not match', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password1!',
        'password_confirmation' => 'Password2!',
    ]);

    $response
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            'password' => 'La confirmation du mot de passe ne correspond pas.',
        ]);
});

test('registration rejects unsafe password patterns', function () {
    $response = $this->from('/register')->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => '<?php echo 1; ?>Aa1!',
        'password_confirmation' => '<?php echo 1; ?>Aa1!',
    ]);

    $response
        ->assertRedirect('/register')
        ->assertSessionHasErrors([
            'password' => 'Le mot de passe contient des sequences interdites.',
        ]);
});
