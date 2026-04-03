<?php

use App\Models\User;

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
