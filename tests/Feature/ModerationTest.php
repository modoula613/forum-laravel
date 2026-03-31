<?php

use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;

test('admin can delete any topic and warn its author', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet modere',
        'content' => 'Contenu a supprimer',
    ]);

    $response = $this
        ->actingAs($admin)
        ->delete(route('admin.topics.destroy', $topic));

    $response
        ->assertRedirect(route('topics.index'))
        ->assertSessionHas('success');

    expect($author->fresh()->warning_count)->toBe(1);
    expect(Topic::find($topic->id))->toBeNull();
});

test('user is blocked after three admin warnings', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $author = User::factory()->create([
        'warning_count' => 2,
    ]);

    $topic = $author->topics()->create([
        'title' => 'Dernier avertissement',
        'content' => 'Sujet signale',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.topics.destroy', $topic))
        ->assertRedirect(route('topics.index'));

    $author->refresh();

    expect($author->warning_count)->toBe(3);
    expect($author->is_blocked)->toBeTrue();
});

test('blocked users cannot create topics', function () {
    $user = User::factory()->create([
        'is_blocked' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Sujet interdit',
            'content' => 'Ce sujet ne doit pas etre cree',
        ]);

    $response
        ->assertRedirect(route('topics.index'))
        ->assertSessionHas('error');

    expect(Topic::count())->toBe(0);
});

test('blocked users cannot post replies', function () {
    $user = User::factory()->create([
        'is_blocked' => true,
    ]);

    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet ouvert',
        'content' => 'Message initial',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('replies.store', $topic), [
            'content' => 'Reponse bloquee',
        ]);

    $response
        ->assertRedirect(route('topics.show', $topic))
        ->assertSessionHas('error');

    expect(Reply::count())->toBe(0);
});

test('banned users cannot create topics', function () {
    $user = User::factory()->create([
        'is_banned' => true,
    ]);

    $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Sujet interdit',
            'content' => 'Ce sujet ne doit pas etre cree',
        ])
        ->assertForbidden();

    expect(Topic::count())->toBe(0);
});

test('banned users cannot post replies', function () {
    $user = User::factory()->create([
        'is_banned' => true,
    ]);

    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet ouvert',
        'content' => 'Message initial',
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.store', $topic), [
            'content' => 'Reponse bannie',
        ])
        ->assertForbidden();

    expect(Reply::count())->toBe(0);
});

test('non admin users cannot access admin topic deletion', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet protege',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('admin.topics.destroy', $topic))
        ->assertForbidden();
});

test('admin can lock and unlock a topic', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet a verrouiller',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.topics.lock', $topic))
        ->assertRedirect();

    expect($topic->fresh()->is_locked)->toBeTrue();

    $this
        ->actingAs($admin)
        ->patch(route('admin.topics.lock', $topic))
        ->assertRedirect();

    expect($topic->fresh()->is_locked)->toBeFalse();
});

test('admin can pin and unpin a topic', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet a epingler',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.topics.pin', $topic))
        ->assertRedirect();

    expect($topic->fresh()->is_pinned)->toBeTrue();

    $this
        ->actingAs($admin)
        ->patch(route('admin.topics.pin', $topic))
        ->assertRedirect();

    expect($topic->fresh()->is_pinned)->toBeFalse();
});

test('users cannot reply to a locked topic', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet verrouille',
        'content' => 'Contenu',
        'is_locked' => true,
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.store', $topic), [
            'content' => 'Tentative de reponse',
        ])
        ->assertForbidden();

    expect(Reply::count())->toBe(0);
});
