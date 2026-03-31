<?php

use App\Models\Badge;
use App\Models\Reply;
use App\Models\Tag;
use App\Models\User;

test('guests can view forum statistics', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet stats',
        'content' => 'Contenu stats',
    ]);

    Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $user->id,
        'content' => 'Reponse stats',
    ]);

    $this
        ->get(route('stats.index'))
        ->assertOk()
        ->assertSeeText('Statistiques')
        ->assertSeeText('Sujets')
        ->assertSee('1');
});

test('stats page shows top users and top topics', function () {
    $activeUser = User::factory()->create([
        'name' => 'Alice',
    ]);
    $quietUser = User::factory()->create([
        'name' => 'Bob',
    ]);

    $activeTopic = $activeUser->topics()->create([
        'title' => 'Sujet populaire',
        'content' => 'Contenu populaire',
    ]);
    $quietTopic = $quietUser->topics()->create([
        'title' => 'Sujet calme',
        'content' => 'Contenu calme',
    ]);

    foreach (range(1, 3) as $index) {
        Reply::create([
            'topic_id' => $activeTopic->id,
            'user_id' => $activeUser->id,
            'content' => "Reponse active {$index}",
        ]);
    }

    Reply::create([
        'topic_id' => $quietTopic->id,
        'user_id' => $quietUser->id,
        'content' => 'Reponse calme',
    ]);

    $response = $this->get(route('stats.index'));

    $response
        ->assertOk()
        ->assertSee('Alice')
        ->assertSee('Sujet populaire');
});

test('stats page shows popular tags', function () {
    $tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet tague',
        'content' => 'Contenu',
    ]);
    $topic->tags()->attach($tag);

    $this
        ->get(route('stats.index'))
        ->assertOk()
        ->assertSee('Tags populaires')
        ->assertSee('Laravel')
        ->assertSee('1 sujets');
});

test('stats page shows banned users count and leaderboard is accessible', function () {
    User::factory()->create([
        'name' => 'Banni',
        'is_banned' => true,
        'reputation' => 12,
    ]);

    $this
        ->get(route('stats.index'))
        ->assertOk()
        ->assertSee('Moderation')
        ->assertSee('Utilisateurs actuellement bannis.');

    $this
        ->get(route('leaderboard'))
        ->assertOk()
        ->assertSee('Leaderboard Sphere')
        ->assertSee('Banni');
});
