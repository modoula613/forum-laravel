<?php

use App\Models\Reply;
use App\Models\ReplyLike;
use App\Models\User;

test('liking a reply increases the authors reputation', function () {
    $liker = User::factory()->create();
    $author = User::factory()->create([
        'reputation' => 0,
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet reputation',
        'content' => 'Contenu reputation',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse reputee',
    ]);

    $this
        ->actingAs($liker)
        ->post(route('replies.like', $reply))
        ->assertRedirect();

    expect($author->fresh()->reputation)->toBe(2);
});

test('removing a like decreases the authors reputation', function () {
    $liker = User::factory()->create();
    $author = User::factory()->create([
        'reputation' => 2,
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet reputation',
        'content' => 'Contenu reputation',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse reputee',
    ]);

    ReplyLike::create([
        'reply_id' => $reply->id,
        'user_id' => $liker->id,
    ]);

    $this
        ->actingAs($liker)
        ->post(route('replies.like', $reply))
        ->assertRedirect();

    expect($author->fresh()->reputation)->toBe(0);
});

test('topic author gains reputation when a topic reaches five replies', function () {
    $author = User::factory()->create([
        'reputation' => 0,
    ]);
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet anime',
        'content' => 'Contenu anime',
    ]);

    foreach (range(1, 4) as $index) {
        $replier->replies()->create([
            'topic_id' => $topic->id,
            'content' => "Reponse {$index}",
        ]);
    }

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Cinquieme reponse',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($author->fresh()->reputation)->toBe(5);
});

test('statistics page shows top reputation users', function () {
    User::factory()->create([
        'name' => 'Rania',
        'reputation' => 42,
    ]);
    User::factory()->create([
        'name' => 'Yanis',
        'reputation' => 15,
    ]);

    $this
        ->get(route('stats.index'))
        ->assertOk()
        ->assertSee('Classement par reputation')
        ->assertSee('Rania')
        ->assertSee('Rep 42');
});
