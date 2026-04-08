<?php

use App\Models\User;
use App\Notifications\NewFollowRequestNotification;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;

test('authenticated users can send a follow request', function () {
    Notification::fake();

    $sender = User::factory()->create();
    $target = User::factory()->create();

    $this
        ->actingAs($sender)
        ->post(route('users.follow', $target))
        ->assertRedirect();

    expect(DB::table('follow_requests')->where([
        'requester_id' => $sender->id,
        'requested_id' => $target->id,
    ])->exists())->toBeTrue();

    expect($sender->isFollowing($target))->toBeFalse();

    Notification::assertSentTo($target, NewFollowRequestNotification::class);
});

test('users can accept an incoming follow request', function () {
    $sender = User::factory()->create();
    $target = User::factory()->create();

    DB::table('follow_requests')->insert([
        'requester_id' => $sender->id,
        'requested_id' => $target->id,
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    $this
        ->actingAs($target)
        ->post(route('users.follow', $sender), [
            'action' => 'accept_request',
        ])
        ->assertRedirect();

    expect(DB::table('follow_requests')->where([
        'requester_id' => $sender->id,
        'requested_id' => $target->id,
    ])->exists())->toBeFalse();

    expect($sender->refresh()->isFollowing($target))->toBeTrue();
});

test('profile shows friend status when both users follow each other', function () {
    $firstUser = User::factory()->create();
    $secondUser = User::factory()->create([
        'name' => 'Camille',
    ]);

    $firstUser->followingUsers()->attach($secondUser->id);
    $secondUser->followingUsers()->attach($firstUser->id);

    $this
        ->actingAs($firstUser)
        ->get(route('users.show', $secondUser))
        ->assertOk()
        ->assertSee('Vous etes amis');
});
