<?php

use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\UserWarnedNotification;

test('authenticated users can view the activity page', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet recent',
        'content' => 'Contenu recent',
    ]);
    Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $user->id,
        'content' => 'Reponse recente',
    ]);
    $user->notify(new UserWarnedNotification(1));

    $this
        ->actingAs($user)
        ->get(route('activity.index'))
        ->assertOk()
        ->assertSee('Activite recente')
        ->assertSee('Sujet recent')
        ->assertSee('Reponse recente');
});
