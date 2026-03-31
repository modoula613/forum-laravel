<?php

use App\Models\AdminLog;
use App\Models\Reply;
use App\Models\Report;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
use App\Models\UserActivity;

test('admin can view the admin replies index with filters', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create([
        'name' => 'Camille',
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet support',
        'content' => 'Contenu',
    ]);
    $reportedReply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse alpha',
    ]);
    Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse beta',
    ]);
    Report::create([
        'user_id' => $admin->id,
        'reply_id' => $reportedReply->id,
        'reason' => 'Signalement',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.replies.index', ['reported' => 1, 'search' => 'alpha']))
        ->assertOk()
        ->assertSee('Gestion des reponses')
        ->assertSee('Reponse alpha')
        ->assertDontSee('Reponse beta');
});

test('admin can delete a reply from the admin replies index and it is logged', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse a supprimer',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.replies.delete', $reply))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Reply::find($reply->id))->toBeNull();
    expect(UserActivity::where('type', 'admin_reply_deleted')->exists())->toBeTrue();
    expect(AdminLog::where('action', 'delete_reply')->exists())->toBeTrue();
});

test('admin can view and delete tags from admin tags index', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.tags.index'))
        ->assertOk()
        ->assertSee('Gestion des tags')
        ->assertSee('Laravel');

    $this
        ->actingAs($admin)
        ->delete(route('admin.tags.delete', $tag))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Tag::find($tag->id))->toBeNull();
});

test('admin can view admin logs', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    AdminLog::create([
        'admin_id' => $admin->id,
        'action' => 'delete_topic',
        'details' => 'A supprime un sujet test',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.logs.index'))
        ->assertOk()
        ->assertSee('Logs admin')
        ->assertSee('A supprime un sujet test');
});

test('admin can filter and clear admin logs', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    AdminLog::create([
        'admin_id' => $admin->id,
        'action' => 'ban_user',
        'details' => 'Utilisateur ID 5 banni',
    ]);
    AdminLog::create([
        'admin_id' => $admin->id,
        'action' => 'delete_reply',
        'details' => 'Reponse ID 9 supprimee',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.logs.index', ['action' => 'ban_user', 'search' => 'banni']))
        ->assertOk()
        ->assertSee('Utilisateur ID 5 banni')
        ->assertDontSee('Reponse ID 9 supprimee');

    $this
        ->actingAs($admin)
        ->delete(route('admin.logs.clear'))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(AdminLog::count())->toBe(0);
});
