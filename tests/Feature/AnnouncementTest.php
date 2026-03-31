<?php

use App\Models\Announcement;
use App\Models\User;

test('admin can view the announcements admin page', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.announcements.index'))
        ->assertOk();
});

test('admin can create an announcement', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->post(route('admin.announcements.store'), [
            'title' => 'Nouvelle annonce',
            'content' => 'Contenu de la nouvelle annonce',
        ])
        ->assertRedirect();

    expect(Announcement::where('title', 'Nouvelle annonce')->exists())->toBeTrue();
});

test('admin can toggle announcement active status', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $announcement = Announcement::create([
        'title' => 'Maintenance',
        'content' => 'Une maintenance est prevue.',
        'is_active' => true,
    ]);

    $this
        ->actingAs($admin)
        ->patch(route('admin.announcements.toggle', $announcement))
        ->assertRedirect();

    expect($announcement->fresh()->is_active)->toBeFalse();
});

test('admin can update an announcement', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $announcement = Announcement::create([
        'title' => 'Titre initial',
        'content' => 'Contenu initial',
        'is_active' => true,
    ]);

    $this
        ->actingAs($admin)
        ->put(route('admin.announcements.update', $announcement), [
            'title' => 'Titre modifie',
            'content' => 'Contenu modifie',
            'is_active' => '0',
        ])
        ->assertRedirect();

    expect($announcement->fresh()->title)->toBe('Titre modifie')
        ->and($announcement->fresh()->content)->toBe('Contenu modifie')
        ->and($announcement->fresh()->is_active)->toBeFalse();
});

test('admin can delete an announcement', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $announcement = Announcement::create([
        'title' => 'Annonce a supprimer',
        'content' => 'Contenu a supprimer',
        'is_active' => true,
    ]);

    $this
        ->actingAs($admin)
        ->delete(route('admin.announcements.destroy', $announcement))
        ->assertRedirect();

    expect(Announcement::find($announcement->id))->toBeNull();
});

test('welcome page displays active announcements only', function () {
    Announcement::create([
        'title' => 'Annonce visible',
        'content' => 'Cette annonce doit apparaitre.',
        'is_active' => true,
    ]);
    Announcement::create([
        'title' => 'Annonce cachee',
        'content' => 'Cette annonce ne doit pas apparaitre.',
        'is_active' => false,
    ]);

    $this
        ->get('/')
        ->assertOk()
        ->assertSee('Annonce visible')
        ->assertDontSee('Annonce cachee');
});

test('public announcements page displays active announcements', function () {
    Announcement::create([
        'title' => 'Annonce publique',
        'content' => 'Contenu public',
        'is_active' => true,
    ]);

    $this
        ->get(route('announcements.index'))
        ->assertOk()
        ->assertSee('Annonce publique')
        ->assertSee('Contenu public');
});

test('non admin users cannot access announcement administration', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('admin.announcements.index'))
        ->assertForbidden();
});
