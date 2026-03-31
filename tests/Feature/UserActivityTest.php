<?php

use App\Models\User;

test('creating a topic stores a user activity entry', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Sujet avec activite',
            'content' => 'Contenu avec activite',
        ])
        ->assertRedirect();

    expect($user->fresh()->activities()->where('type', 'topic_created')->exists())->toBeTrue();
});

test('replying stores a user activity entry', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet activite',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Reponse avec activite',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($replier->fresh()->activities()->where('type', 'reply_created')->exists())->toBeTrue();
});

test('guests can view a users activity page', function () {
    $user = User::factory()->create([
        'name' => 'Ines',
    ]);
    $user->activities()->create([
        'type' => 'topic_created',
        'description' => 'A cree un sujet : Bienvenue',
    ]);

    $this
        ->get(route('users.activity', $user))
        ->assertOk()
        ->assertSee('Ines')
        ->assertSee('topic_created')
        ->assertSee('A cree un sujet : Bienvenue');
});
