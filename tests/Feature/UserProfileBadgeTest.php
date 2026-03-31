<?php

use App\Models\Badge;
use App\Models\User;

test('public user profile shows badges', function () {
    $user = User::factory()->create([
        'name' => 'Lea',
    ]);
    $badge = Badge::create([
        'name' => 'Contributeur',
        'description' => 'A participe activement',
    ]);

    $user->badges()->attach($badge);

    $this
        ->get(route('users.show', $user))
        ->assertOk()
        ->assertSee('Lea')
        ->assertSee('Contributeur');
});
