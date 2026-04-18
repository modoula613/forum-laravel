<?php

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;

test('search suggestions return matching topics', function () {
    $user = User::factory()->create(['name' => 'Nadia']);
    $category = Category::create([
        'name' => 'Actualites et debats',
        'slug' => 'actualites-et-debats',
    ]);

    $topic = $user->topics()->create([
        'category_id' => $category->id,
        'title' => 'Le prix des courses devient absurde',
        'content' => 'On en parle tous les jours dans le forum.',
    ]);

    $this->getJson(route('search.suggestions', ['query' => 'courses']))
        ->assertOk()
        ->assertJsonPath('sections.0.label', 'Recherche')
        ->assertJsonFragment([
            'type' => 'topic',
            'title' => $topic->title,
            'url' => route('topics.show', $topic),
        ]);
});

test('search suggestions support user prefix', function () {
    $user = User::factory()->create([
        'name' => 'Moh',
        'email' => 'moh@example.com',
    ]);

    $this->getJson(route('search.suggestions', ['query' => 'user:moh']))
        ->assertOk()
        ->assertJsonPath('sections.0.items.0.url', route('users.index', ['search' => 'moh']))
        ->assertJsonFragment([
            'type' => 'user',
            'title' => 'Moh',
            'url' => route('users.show', $user),
        ]);
});

test('search suggestions keep keyword search without hashtag suggestions', function () {
    $this->getJson(route('search.suggestions', ['query' => '#actu']))
        ->assertOk()
        ->assertJsonPath('sections.0.items.0.title', 'actu')
        ->assertJsonPath('sections.0.items.0.url', route('topics.index', ['search' => 'actu']))
        ->assertJsonMissing([
            'type' => 'tag',
        ]);
});
