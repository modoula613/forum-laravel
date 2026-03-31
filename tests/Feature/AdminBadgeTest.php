<?php

use App\Models\Badge;
use App\Models\Reply;
use App\Models\User;

test('admin can view the admin badges index', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.badges.index'))
        ->assertOk();
});

test('admin can assign a badge manually to a user', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $user = User::factory()->create();
    $badge = Badge::create([
        'name' => 'Mentor',
        'description' => 'Badge manuel',
    ]);

    $this
        ->actingAs($admin)
        ->post(route('admin.badges.assign', ['user' => $user, 'badge' => $badge]))
        ->assertRedirect();

    expect($user->fresh()->badges()->pluck('name')->all())
        ->toContain('Mentor');
});

test('non admin users cannot access badge administration', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.badges.index'))
        ->assertForbidden();
});

test('reply author receives the utilisateur apprecie badge after twenty five likes received', function () {
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet aime',
        'content' => 'Contenu aime',
    ]);

    foreach (range(1, 25) as $index) {
        $reply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => $author->id,
            'content' => "Reponse {$index}",
        ]);

        $liker = User::factory()->create();

        $this
            ->actingAs($liker)
            ->post(route('replies.like', $reply))
            ->assertRedirect();
    }

    expect($author->fresh()->badges()->pluck('name')->all())
        ->toContain('Utilisateur apprecie');
});
