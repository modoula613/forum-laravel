<?php

use App\Models\Topic;
use App\Models\User;

test('admin can view the admin topics index', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create([
        'name' => 'Camille',
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet admin',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.topics.index'))
        ->assertOk()
        ->assertSee('Gestion des sujets')
        ->assertSee('Sujet admin')
        ->assertSee('Camille');
});

test('admin can filter admin topics by locked pinned and search', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create();

    $lockedPinnedTopic = $author->topics()->create([
        'title' => 'Sujet alpha',
        'content' => 'Contenu',
        'is_locked' => true,
        'is_pinned' => true,
    ]);
    $author->topics()->create([
        'title' => 'Sujet beta',
        'content' => 'Contenu',
        'is_locked' => false,
        'is_pinned' => false,
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.topics.index', [
            'locked' => 1,
            'pinned' => 1,
            'search' => 'alpha',
        ]))
        ->assertOk()
        ->assertSee($lockedPinnedTopic->title)
        ->assertDontSee('Sujet beta');
});

test('non admin users cannot view the admin topics index', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.topics.index'))
        ->assertForbidden();
});
