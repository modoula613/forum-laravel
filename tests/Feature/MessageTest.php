<?php

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewPrivateMessageNotification;
use Illuminate\Support\Facades\Notification;

test('authenticated users can view their inbox', function () {
    $receiver = User::factory()->create();
    $sender = User::factory()->create();

    Message::create([
        'sender_id' => $sender->id,
        'receiver_id' => $receiver->id,
        'content' => 'Bonjour depuis la messagerie',
        'is_read' => false,
    ]);

    $this
        ->actingAs($receiver)
        ->get(route('messages.index'))
        ->assertOk()
        ->assertSee('Boite de reception')
        ->assertSee('Bonjour depuis la messagerie')
        ->assertSee('1 non lu');

    expect($receiver->receivedMessages()->first()->is_read)->toBeFalse();
});

test('authenticated users can send a private message', function () {
    Notification::fake();

    $sender = User::factory()->create();
    $receiver = User::factory()->create();

    $this
        ->actingAs($sender)
        ->post(route('messages.send'), [
            'receiver_id' => $receiver->id,
            'content' => 'Message prive',
        ])
        ->assertRedirect();

    expect(Message::where('sender_id', $sender->id)->where('receiver_id', $receiver->id)->exists())->toBeTrue();
    Notification::assertSentTo($receiver, NewPrivateMessageNotification::class);
});

test('guests can view public user profile', function () {
    $user = User::factory()->create([
        'name' => 'Nadia',
    ]);

    $this
        ->get(route('users.show', $user))
        ->assertOk()
        ->assertSee('Profil public')
        ->assertSee('Nadia');
});

test('authenticated users can view a conversation with another user', function () {
    $currentUser = User::factory()->create();
    $otherUser = User::factory()->create([
        'name' => 'Camille',
    ]);

    Message::create([
        'sender_id' => $otherUser->id,
        'receiver_id' => $currentUser->id,
        'content' => 'Premier message',
        'is_read' => false,
    ]);

    Message::create([
        'sender_id' => $currentUser->id,
        'receiver_id' => $otherUser->id,
        'content' => 'Reponse en retour',
        'is_read' => true,
    ]);

    $this
        ->actingAs($currentUser)
        ->get(route('messages.conversation', $otherUser))
        ->assertOk()
        ->assertSee('Discussion avec Camille')
        ->assertSee('Premier message')
        ->assertSee('Reponse en retour');

    expect(
        Message::where('sender_id', $otherUser->id)
            ->where('receiver_id', $currentUser->id)
            ->first()
            ->is_read
    )->toBeTrue();
});

test('users cannot send a private message to themselves', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('messages.send'), [
            'receiver_id' => $user->id,
            'content' => 'Auto message',
        ])
        ->assertForbidden();
});

test('banned users cannot send a private message', function () {
    $sender = User::factory()->create([
        'is_banned' => true,
    ]);
    $receiver = User::factory()->create();

    $this
        ->actingAs($sender)
        ->post(route('messages.send'), [
            'receiver_id' => $receiver->id,
            'content' => 'Message interdit',
        ])
        ->assertForbidden();

    expect(Message::count())->toBe(0);
});

test('authenticated users can search within a conversation', function () {
    $currentUser = User::factory()->create();
    $otherUser = User::factory()->create();

    Message::create([
        'sender_id' => $otherUser->id,
        'receiver_id' => $currentUser->id,
        'content' => 'Bonjour Laravel',
        'is_read' => false,
    ]);

    Message::create([
        'sender_id' => $otherUser->id,
        'receiver_id' => $currentUser->id,
        'content' => 'Bonjour Symfony',
        'is_read' => false,
    ]);

    $this
        ->actingAs($currentUser)
        ->get(route('messages.conversation', ['user' => $otherUser, 'search' => 'Laravel']))
        ->assertOk()
        ->assertSee('Bonjour Laravel')
        ->assertDontSee('Bonjour Symfony');
});

test('authenticated users can delete a message from a conversation', function () {
    $currentUser = User::factory()->create();
    $otherUser = User::factory()->create();

    $message = Message::create([
        'sender_id' => $otherUser->id,
        'receiver_id' => $currentUser->id,
        'content' => 'Message a supprimer',
        'is_read' => false,
    ]);

    $this
        ->actingAs($currentUser)
        ->delete(route('messages.destroy', $message))
        ->assertRedirect();

    expect(Message::find($message->id))->toBeNull();
});
