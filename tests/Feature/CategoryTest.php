<?php

use App\Models\Category;
use App\Models\User;

test('guests can view the categories index', function () {
    Category::create([
        'name' => 'Support',
        'slug' => 'support',
    ]);

    $this
        ->get(route('categories.index'))
        ->assertOk()
        ->assertSee('Support');
});

test('guests can view topics for a category', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Developpement',
        'slug' => 'developpement-web',
    ]);

    $user->topics()->create([
        'title' => 'Sujet technique',
        'content' => 'Contenu technique',
        'category_id' => $category->id,
    ]);

    $this
        ->get(route('categories.show', $category))
        ->assertOk()
        ->assertSee('Developpement')
        ->assertSee('Sujet technique');
});

test('categories use slug based routing', function () {
    $category = Category::create([
        'name' => 'Support client',
        'slug' => 'support-client',
    ]);

    expect(route('categories.show', $category))->toContain('/categories/support-client');
});
