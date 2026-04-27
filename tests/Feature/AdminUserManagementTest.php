<?php

use App\Models\User;
use Illuminate\Support\Facades\DB;

test('admin can view the admin users index', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.users.index'))
        ->assertOk();
});

test('admin can toggle a user block status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $user = User::factory()->create([
        'is_blocked' => false,
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.users.toggleBlock', $user))
        ->assertRedirect();

    expect($user->fresh()->is_blocked)->toBeTrue();
});

test('admin can ban and unban a user', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $user = User::factory()->create([
        'is_banned' => false,
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.users.ban', $user))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->is_banned)->toBeTrue();

    $this
        ->actingAs($admin)
        ->patch(route('admin.users.unban', $user))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($user->fresh()->is_banned)->toBeFalse();
});

test('admin cannot ban themselves', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
        'is_banned' => false,
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.users.ban', $admin))
        ->assertRedirect()
        ->assertSessionHas('error', 'Vous ne pouvez pas vous bannir vous-meme.');

    expect($admin->fresh()->is_banned)->toBeFalse();
});

test('admin can delete a user and clean direct account data', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $user = User::factory()->create([
        'email' => 'delete-me@example.com',
    ]);

    DB::table('sessions')->insert([
        'id' => 'session-delete-user',
        'user_id' => $user->id,
        'ip_address' => '127.0.0.1',
        'user_agent' => 'PHPUnit',
        'payload' => 'test',
        'last_activity' => now()->timestamp,
    ]);

    DB::table('notifications')->insert([
        'id' => (string) str()->uuid(),
        'type' => 'App\\Notifications\\TestNotification',
        'notifiable_type' => User::class,
        'notifiable_id' => $user->id,
        'data' => json_encode(['message' => 'test'], JSON_THROW_ON_ERROR),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => 'token-test',
        'created_at' => now(),
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.users.destroy', $user))
        ->assertRedirect()
        ->assertSessionHas('success', 'Utilisateur supprime.');

    expect(User::find($user->id))->toBeNull();
    expect(DB::table('sessions')->where('user_id', $user->id)->exists())->toBeFalse();
    expect(DB::table('notifications')->where('notifiable_type', User::class)->where('notifiable_id', $user->id)->exists())->toBeFalse();
    expect(DB::table('password_reset_tokens')->where('email', $user->email)->exists())->toBeFalse();
});

test('admin cannot delete themselves', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.users.destroy', $admin))
        ->assertRedirect()
        ->assertSessionHas('error', 'Vous ne pouvez pas supprimer votre propre compte.');

    expect($admin->fresh())->not->toBeNull();
});

test('non admin users cannot access admin user management', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.users.index'))
        ->assertForbidden();
});

test('admin can filter banned users and search by name', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    User::factory()->create([
        'name' => 'Alice',
        'is_banned' => true,
    ]);
    User::factory()->create([
        'name' => 'Bob',
        'is_banned' => false,
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.users.index', ['banned' => 1, 'search' => 'Ali']))
        ->assertOk()
        ->assertSee('Alice')
        ->assertDontSee('Bob');
});
