<?php

use App\Models\Favorite;
use App\Models\User;

test('authenticated users can favorite a topic', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet a suivre',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($user)
        ->post(route('topics.favorite', $topic))
        ->assertRedirect();

    expect(Favorite::where('user_id', $user->id)->where('topic_id', $topic->id)->exists())->toBeTrue();
});

test('authenticated users can remove a topic from favorites', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet a ne plus suivre',
        'content' => 'Contenu',
    ]);

    Favorite::create([
        'user_id' => $user->id,
        'topic_id' => $topic->id,
    ]);

    $this
        ->actingAs($user)
        ->post(route('topics.favorite', $topic))
        ->assertRedirect();

    expect(Favorite::where('user_id', $user->id)->where('topic_id', $topic->id)->exists())->toBeFalse();
});

test('authenticated users can view their followed topics page', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet favori',
        'content' => 'Contenu',
    ]);

    Favorite::create([
        'user_id' => $user->id,
        'topic_id' => $topic->id,
    ]);

    $this
        ->actingAs($user)
        ->get(route('favorites.index'))
        ->assertOk()
        ->assertSee('Mes sujets suivis')
        ->assertSee('Sujet favori');
});
