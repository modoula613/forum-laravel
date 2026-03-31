<?php

use App\Models\Reply;
use App\Models\Report;
use App\Models\Topic;
use App\Models\User;

test('admin can view the admin dashboard', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $user = User::factory()->create([
        'name' => 'Nadia',
    ]);
    $topic = $user->topics()->create([
        'title' => 'Sujet recent',
        'content' => 'Contenu',
    ]);
    Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $user->id,
        'content' => 'Reponse recente',
    ]);
    Report::create([
        'user_id' => $user->id,
        'topic_id' => $topic->id,
        'reason' => 'Signalement test',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.index'))
        ->assertOk()
        ->assertSee('Tableau de bord admin')
        ->assertSee('Signalement test')
        ->assertSee('Nadia')
        ->assertSee('Sujet recent');
});

test('non admin users cannot view the admin dashboard', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.index'))
        ->assertForbidden();
});
