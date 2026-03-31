<?php

use App\Models\Badge;
use App\Models\User;

test('replying for the first time awards the premier message badge', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet badge',
        'content' => 'Contenu badge',
    ]);

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Ma premiere reponse',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($replier->fresh()->badges()->pluck('name')->all())
        ->toContain('Premier message');
});

test('creating a first topic awards the createur badge', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Premier sujet badge',
            'content' => 'Contenu du premier sujet',
        ])
        ->assertRedirect();

    expect($user->fresh()->badges()->pluck('name')->all())
        ->toContain('Createur');
});

test('tenth reply awards the participant actif badge', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet actif',
        'content' => 'Contenu actif',
    ]);

    foreach (range(1, 9) as $index) {
        $replier->replies()->create([
            'topic_id' => $topic->id,
            'content' => "Reponse {$index}",
        ]);
    }

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Dixieme reponse',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($replier->fresh()->badges()->pluck('name')->all())
        ->toContain('Participant actif');
});

test('fiftieth reply awards the top contributeur badge', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet top contributeur',
        'content' => 'Contenu top contributeur',
    ]);

    foreach (range(1, 49) as $index) {
        $replier->replies()->create([
            'topic_id' => $topic->id,
            'content' => "Reponse {$index}",
        ]);
    }

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Cinquantieme reponse',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($replier->fresh()->badges()->pluck('name')->all())
        ->toContain('Top contributeur');
});

test('guests can view the badges index', function () {
    Badge::create([
        'name' => 'Explorateur',
        'description' => 'Badge public',
    ]);

    $this
        ->get(route('badges.index'))
        ->assertOk()
        ->assertSee('Recompenses de la communaute')
        ->assertSee('Explorateur');
});

test('guests can view a users badges page', function () {
    $user = User::factory()->create([
        'name' => 'Camille',
    ]);
    $badge = Badge::create([
        'name' => 'Veteran',
        'description' => 'Membre recompense',
    ]);

    $user->badges()->attach($badge);

    $this
        ->get(route('users.badges', $user))
        ->assertOk()
        ->assertSee('Camille')
        ->assertSee('Veteran');
});

test('authenticated legacy users automatically receive the utilisateur ancien badge', function () {
    $user = User::factory()->create([
        'created_at' => now()->subDays(366),
    ]);

    $this
        ->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();

    expect($user->fresh()->badges()->pluck('name')->all())
        ->toContain('Utilisateur ancien');
});

test('topic author receives the sujet populaire badge when a topic reaches twenty replies', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet populaire',
        'content' => 'Contenu populaire',
    ]);

    foreach (range(1, 19) as $index) {
        $replier->replies()->create([
            'topic_id' => $topic->id,
            'content' => "Reponse {$index}",
        ]);
    }

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Vingtieme reponse',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($author->fresh()->badges()->pluck('name')->all())
        ->toContain('Sujet populaire');
});
