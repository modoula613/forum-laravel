<?php

use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;

test('authenticated users can bookmark and unbookmark a reply', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet bookmark',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse a sauvegarder',
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.bookmark', $reply))
        ->assertRedirect();

    expect($user->fresh()->bookmarkedReplies->pluck('id'))->toContain($reply->id);

    $this
        ->actingAs($user)
        ->post(route('replies.bookmark', $reply))
        ->assertRedirect();

    expect($user->fresh()->bookmarkedReplies->pluck('id'))->not->toContain($reply->id);
});

test('authenticated users can view their bookmarked replies page', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet signet',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse memorisee',
    ]);

    $user->bookmarkedReplies()->attach($reply->id);

    $this
        ->actingAs($user)
        ->get(route('replies.bookmarks'))
        ->assertOk()
        ->assertSee('Mes reponses sauvegardees')
        ->assertSee('Reponse memorisee');
});

test('bookmarked replies page can filter by content topic and author', function () {
    $user = User::factory()->create();
    $firstAuthor = User::factory()->create([
        'name' => 'Camille',
    ]);
    $secondAuthor = User::factory()->create([
        'name' => 'Louis',
    ]);

    $matchingTopic = $firstAuthor->topics()->create([
        'title' => 'Sujet Laravel',
        'content' => 'Contenu',
    ]);
    $otherTopic = $secondAuthor->topics()->create([
        'title' => 'Sujet Vue',
        'content' => 'Contenu',
    ]);

    $matchingReply = Reply::create([
        'topic_id' => $matchingTopic->id,
        'user_id' => $firstAuthor->id,
        'content' => 'Reponse tres utile sur Horizon',
    ]);
    $otherReply = Reply::create([
        'topic_id' => $otherTopic->id,
        'user_id' => $secondAuthor->id,
        'content' => 'Reponse annexe',
    ]);

    $user->bookmarkedReplies()->attach([
        $matchingReply->id => ['created_at' => now(), 'updated_at' => now()],
        $otherReply->id => ['created_at' => now(), 'updated_at' => now()],
    ]);

    $this
        ->actingAs($user)
        ->get(route('replies.bookmarks', [
            'search' => 'Horizon',
            'topic' => 'Laravel',
            'author' => 'Camille',
        ]))
        ->assertOk()
        ->assertSee('Reponse tres utile sur Horizon')
        ->assertDontSee('Reponse annexe');
});
