<?php

use App\Models\Badge;
use App\Models\User;

test('guests can view the users index', function () {
    $user = User::factory()->create([
        'name' => 'Alice Martin',
    ]);

    $this
        ->get(route('users.index'))
        ->assertOk()
        ->assertSee('Alice Martin');
});

test('users index can search by name', function () {
    User::factory()->create([
        'name' => 'Camille Bernard',
    ]);
    User::factory()->create([
        'name' => 'Thomas Dupont',
    ]);

    $this
        ->get(route('users.index', ['search' => 'Camille']))
        ->assertOk()
        ->assertSee('Camille Bernard')
        ->assertDontSee('Thomas Dupont');
});

test('public user profile displays earned badges', function () {
    $user = User::factory()->create([
        'name' => 'Nora Petit',
    ]);
    $badge = Badge::create([
        'name' => 'Contributeur',
        'description' => 'Attribue aux membres actifs',
    ]);

    $user->badges()->attach($badge);

    $this
        ->get(route('users.show', $user))
        ->assertOk()
        ->assertSee('Nora Petit')
        ->assertSee('Contributeur');
});
