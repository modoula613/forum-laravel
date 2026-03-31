<?php

use App\Models\Reply;
use App\Models\Report;
use App\Models\User;
use App\Notifications\ReplyReportedNotification;
use Illuminate\Support\Facades\Notification;

test('authenticated users can report a topic', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet signale',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($user)
        ->post(route('topics.report', $topic), [
            'reason' => 'Contenu inapproprie',
        ])
        ->assertRedirect();

    expect(Report::where('user_id', $user->id)->where('topic_id', $topic->id)->exists())->toBeTrue();
});

test('authenticated users can report a reply', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet source',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse a signaler',
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.report', $reply), [
            'reason' => 'Message offensant',
        ])
        ->assertRedirect();

    expect(Report::where('user_id', $user->id)->where('reply_id', $reply->id)->exists())->toBeTrue();
});

test('users cannot report the same reply twice', function () {
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet source',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse a signaler',
    ]);
    Report::create([
        'user_id' => $user->id,
        'reply_id' => $reply->id,
        'reason' => 'Premier signalement',
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.report', $reply), [
            'reason' => 'Deuxieme signalement',
        ])
        ->assertRedirect()
        ->assertSessionHas('error');

    expect(Report::where('user_id', $user->id)->where('reply_id', $reply->id)->count())->toBe(1);
});

test('reporting a reply notifies admins', function () {
    Notification::fake();

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $user = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet source',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse a signaler',
    ]);

    $this
        ->actingAs($user)
        ->post(route('replies.report', $reply), [
            'reason' => 'Contenu abusif',
        ])
        ->assertRedirect();

    Notification::assertSentTo($admin, ReplyReportedNotification::class);
});

test('admin can view reports index', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $reporter = User::factory()->create([
        'name' => 'Camille',
    ]);
    $author = User::factory()->create([
        'name' => 'Louis',
    ]);
    $topic = $author->topics()->create([
        'title' => 'Sujet modere',
        'content' => 'Contenu',
    ]);
    $reply = Reply::create([
        'topic_id' => $topic->id,
        'user_id' => $author->id,
        'content' => 'Reponse a moderer',
    ]);
    Report::create([
        'user_id' => $reporter->id,
        'reply_id' => $reply->id,
        'reason' => 'Message offensant',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Signalements')
        ->assertSee('Reponse a moderer')
        ->assertSee('Camille')
        ->assertSee('Louis')
        ->assertSee('Message offensant');
});

test('non admin users cannot access reports index', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

test('admin can mark a report as resolved', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet modere',
        'content' => 'Contenu',
    ]);
    $report = Report::create([
        'user_id' => $reporter->id,
        'topic_id' => $topic->id,
        'reason' => 'Spam',
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.reports.resolve', $report))
        ->assertRedirect();

    expect($report->fresh()->status)->toBe('resolved');
});

test('admin can ignore a report', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet ignore',
        'content' => 'Contenu',
    ]);
    $report = Report::create([
        'user_id' => $reporter->id,
        'topic_id' => $topic->id,
        'reason' => 'Sans objet',
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.reports.ignore', $report))
        ->assertRedirect();

    expect($report->fresh()->status)->toBe('ignored');
});

test('admin can delete a processed report', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet traite',
        'content' => 'Contenu',
    ]);
    $report = Report::create([
        'user_id' => $reporter->id,
        'topic_id' => $topic->id,
        'reason' => 'Motif',
        'status' => 'resolved',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.reports.destroy', $report))
        ->assertRedirect();

    expect(Report::find($report->id))->toBeNull();
});

test('admin can delete reported topic and keep resolved report history', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $reporter = User::factory()->create();
    $author = User::factory()->create();
    $topic = $author->topics()->create([
        'title' => 'Sujet a supprimer',
        'content' => 'Contenu',
    ]);
    $report = Report::create([
        'user_id' => $reporter->id,
        'topic_id' => $topic->id,
        'reason' => 'Abus',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.reports.destroyTopic', $report))
        ->assertRedirect();

    $report->refresh();

    expect($report->status)->toBe('resolved');
    expect($report->topic_id)->toBeNull();
});
