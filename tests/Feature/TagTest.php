<?php

use App\Models\Tag;
use App\Models\User;

test('guests can view the tags index', function () {
    $tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet Laravel',
        'content' => 'Contenu Laravel',
    ]);
    $topic->tags()->attach($tag);

    $this
        ->get(route('tags.index'))
        ->assertOk()
        ->assertSee('Laravel')
        ->assertSee('1 sujet');
});

test('tags index can search by name', function () {
    Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
    Tag::create([
        'name' => 'Vue',
        'slug' => 'vue',
    ]);

    $this
        ->get(route('tags.index', ['search' => 'Lara']))
        ->assertOk()
        ->assertSee('Laravel')
        ->assertDontSee('Vue');
});

test('guests can view topics for a specific tag', function () {
    $tag = Tag::create([
        'name' => 'Vue',
        'slug' => 'vue',
    ]);
    $otherTag = Tag::create([
        'name' => 'React',
        'slug' => 'react',
    ]);
    $user = User::factory()->create();

    $taggedTopic = $user->topics()->create([
        'title' => 'Sujet Vue',
        'content' => 'Contenu Vue',
    ]);
    $taggedTopic->tags()->attach($tag);

    $otherTopic = $user->topics()->create([
        'title' => 'Sujet React',
        'content' => 'Contenu React',
    ]);
    $otherTopic->tags()->attach($otherTag);

    $this
        ->get(route('tags.show', $tag))
        ->assertOk()
        ->assertSee('Sujet Vue')
        ->assertDontSee('Sujet React');
});

test('authenticated users can follow and unfollow a tag', function () {
    $user = User::factory()->create();
    $tag = Tag::create([
        'name' => 'PHP',
        'slug' => 'php',
    ]);

    $this
        ->actingAs($user)
        ->post(route('tags.follow', $tag))
        ->assertRedirect();

    expect($user->fresh()->followedTags->pluck('id'))->toContain($tag->id);

    $this
        ->actingAs($user)
        ->post(route('tags.follow', $tag))
        ->assertRedirect();

    expect($user->fresh()->followedTags->pluck('id'))->not->toContain($tag->id);
});

test('authenticated users can view their followed tags page', function () {
    $user = User::factory()->create();
    $tag = Tag::create([
        'name' => 'PHP',
        'slug' => 'php',
    ]);
    $user->followedTags()->attach($tag);

    $this
        ->actingAs($user)
        ->get(route('tags.followed'))
        ->assertOk()
        ->assertSee('Mes tags suivis')
        ->assertSee('PHP');
});
