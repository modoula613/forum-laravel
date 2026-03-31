<?php

use App\Models\Reply;
use App\Models\User;

test('reply author can update their reply', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet de test',
        'content' => 'Contenu initial',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $user->id,
        'content' => 'Ancienne reponse',
    ]);

    $this
        ->actingAs($user)
        ->put(route('replies.update', $reply), [
            'content' => 'Reponse mise a jour',
        ])
        ->assertRedirect();

    expect($reply->fresh()->content)->toBe('Reponse mise a jour');
});

test('users cannot update replies they do not own', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet protege',
        'content' => 'Contenu initial',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $author->id,
        'content' => 'Reponse protegee',
    ]);

    $this
        ->actingAs($user)
        ->put(route('replies.update', $reply), [
            'content' => 'Tentative',
        ])
        ->assertForbidden();

    expect($reply->fresh()->content)->toBe('Reponse protegee');
});

test('reply author can delete their reply', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet avec suppression',
        'content' => 'Contenu initial',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $user->id,
        'content' => 'Reponse a supprimer',
    ]);

    $this
        ->actingAs($user)
        ->delete(route('replies.destroy', $reply))
        ->assertRedirect();

    expect(Reply::find($reply->id))->toBeNull();
});

test('admin can delete any reply', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet admin',
        'content' => 'Contenu initial',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $author->id,
        'content' => 'Reponse cible',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('replies.destroy', $reply))
        ->assertRedirect();

    expect(Reply::find($reply->id))->toBeNull();
});

test('updating a reply stores its previous content in history', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet historique reponse',
        'content' => 'Contenu',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $user->id,
        'content' => 'Version originale',
    ]);

    $this
        ->actingAs($user)
        ->put(route('replies.update', $reply), [
            'content' => 'Version modifiee',
        ])
        ->assertRedirect();

    expect($reply->fresh()->edits()->latest()->first()?->old_content)->toBe('Version originale');
});

test('reply author can view reply history', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet historique reponse',
        'content' => 'Contenu',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $user->id,
        'content' => 'Version actuelle',
    ]);
    $reply->edits()->create([
        'old_content' => 'Version precedente',
    ]);

    $this
        ->actingAs($user)
        ->get(route('replies.history', $reply))
        ->assertOk()
        ->assertSee('Version precedente');
});
