<?php

use App\Models\Reply;
use App\Models\ReplyLike;
use App\Models\Topic;
use App\Models\User;

test('authenticated users can like a reply', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet aime',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse utile',
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.like', $reply))
        ->assertRedirect();

    expect(ReplyLike::where('reply_id', $reply->id)->where('user_id', $user->id)->exists())->toBeTrue();
});

test('authenticated users can unlike a reply', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet aime',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse utile',
    ]);

    ReplyLike::create([
        'reply_id' => $reply->id,
        'user_id' => $user->id,
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.like', $reply))
        ->assertRedirect();

    expect(ReplyLike::where('reply_id', $reply->id)->where('user_id', $user->id)->exists())->toBeFalse();
});

test('topic show displays likes count for replies', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet detaille',
        'content' => 'Contenu detaille',
    ]);
    $reply = $topic->replies()->create([
        'user_id' => $user->id,
        'content' => 'Reponse populaire',
    ]);

    ReplyLike::create([
        'reply_id' => $reply->id,
        'user_id' => User::factory()->create()->id,
    ]);

    $this
        ->get(route('topics.show', $topic))
        ->assertOk()
        ->assertViewHas('topic', function ($loadedTopic) use ($reply) {
            $loadedReply = $loadedTopic->replies->firstWhere('id', $reply->id);

            return $loadedReply !== null && $loadedReply->likes_count === 1;
        });
});
