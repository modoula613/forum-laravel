<?php

use App\Models\User;

test('creating a topic grants experience to the author', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Sujet experience',
            'content' => 'Contenu experience',
        ])
        ->assertRedirect();

    expect($user->fresh()->experience)->toBe(10);
});

test('replying grants experience to the author', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet reponse xp',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Reponse avec xp',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($replier->fresh()->experience)->toBe(5);
});

test('experience can level up a user', function () {
    $user = User::factory()->create([
        'level' => 1,
        'experience' => 95,
    ]);

    $user->addExperience(10);

    expect($user->fresh()->level)->toBe(2)
        ->and($user->fresh()->experience)->toBe(5);
});

test('public profile displays user level and experience progression', function () {
    $user = User::factory()->create([
        'name' => 'Jade',
        'level' => 3,
        'experience' => 40,
    ]);

    $this
        ->get(route('users.show', $user))
        ->assertOk()
        ->assertSee('Niveau 3')
        ->assertSee('40 / 300 XP');
});

test('statistics page shows top user levels', function () {
    User::factory()->create([
        'name' => 'Noa',
        'level' => 5,
        'experience' => 20,
    ]);
    User::factory()->create([
        'name' => 'Lina',
        'level' => 3,
        'experience' => 90,
    ]);

    $this
        ->get(route('stats.index'))
        ->assertOk()
        ->assertSee('Classement des niveaux')
        ->assertSee('Noa')
        ->assertSee('Lvl 5');
});
