<?php

use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\NewTopicForFollowedTagNotification;
use App\Notifications\NewReplyNotification;
use App\Notifications\TopicFollowedNewReplyNotification;
use App\Notifications\UserWarnedNotification;
use Illuminate\Support\Facades\Notification;

test('admin deletion sends a warning notification', function () {
    Notification::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet a notifier',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.topics.destroy', $topic))
        ->assertRedirect(route('topics.index'));

    Notification::assertSentTo($author, UserWarnedNotification::class);
});

test('authenticated users can view their notifications page', function () {
    $user = User::factory()->create();
    $user->notify(new UserWarnedNotification(2));

    $response = $this
        ->actingAs($user)
        ->get(route('notifications.index'));

    $response
        ->assertOk()
        ->assertSee('Activite recente')
        ->assertSee('2 avertissement');
});

test('replying to a topic notifies its author', function () {
    Notification::fake();

    $author = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet suivi',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Nouvelle reponse',
        ])
        ->assertRedirect(route('topics.show', $topic));

    Notification::assertSentTo($author, NewReplyNotification::class);
});

test('reply author does not notify themselves on their own topic', function () {
    Notification::fake();

    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet auto-reponse',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($author)
        ->post(route('replies.store', $topic), [
            'content' => 'Je reponds a mon propre sujet',
        ])
        ->assertRedirect(route('topics.show', $topic));

    Notification::assertNothingSent();
});

test('notifications page shows new reply notifications', function () {
    $author = User::factory()->create();
    $replier = User::factory()->create([
        'name' => 'Camille',
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet notifie',
        'content' => 'Contenu',
    ]);

    $author->notify(new NewReplyNotification($topic, $replier));

    $this
        ->actingAs($author)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Nouvelle reponse sur un sujet suivi')
        ->assertSee('Camille')
        ->assertSee('Sujet notifie')
        ->assertSee(route('users.show', $replier), false);
});

test('notifications page links private message sender to their profile', function () {
    $receiver = User::factory()->create();
    $sender = User::factory()->create([
        'name' => 'Nora',
    ]);

    $receiver->notify(new \App\Notifications\NewPrivateMessageNotification($sender));

    $this
        ->actingAs($receiver)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Nora')
        ->assertSee(route('users.show', $sender), false);
});

test('replying to a followed topic notifies followers', function () {
    Notification::fake();

    $author = User::factory()->create();
    $follower = User::factory()->create();
    $replier = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet suivi par un membre',
        'content' => 'Contenu',
    ]);

    $follower->favorites()->create([
        'topic_id' => $topic->id,
    ]);

    $this
        ->actingAs($replier)
        ->post(route('replies.store', $topic), [
            'content' => 'Nouvelle reponse pour les abonnes',
        ])
        ->assertRedirect(route('topics.show', $topic));

    Notification::assertSentTo($follower, TopicFollowedNewReplyNotification::class);
});

test('creating a topic notifies followers of its tags', function () {
    Notification::fake();

    $author = User::factory()->create();
    $follower = User::factory()->create();
    $tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
    $follower->followedTags()->attach($tag);

    $this
        ->actingAs($author)
        ->post(route('topics.store'), [
            'title' => 'Nouveau sujet Laravel',
            'content' => 'Contenu',
            'tags' => [$tag->id],
        ])
        ->assertRedirect();

    Notification::assertSentTo($follower, NewTopicForFollowedTagNotification::class);
    Notification::assertNothingSentTo($author, NewTopicForFollowedTagNotification::class);
});

test('notifications page shows followed tag topic notifications', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $tag = Tag::create([
        'name' => 'PHP',
        'slug' => 'php',
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet PHP',
        'content' => 'Contenu',
    ]);

    $user->notify(new NewTopicForFollowedTagNotification($topic, $tag));

    $this
        ->actingAs($user)
        ->get(route('notifications.index'))
        ->assertOk()
        ->assertSee('Nouveau sujet dans un tag suivi')
        ->assertSee('Sujet PHP')
        ->assertSee('PHP');
});
