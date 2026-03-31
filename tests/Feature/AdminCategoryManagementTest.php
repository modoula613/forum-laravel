<?php

use App\Models\AdminLog;
use App\Models\Category;
use App\Models\User;

test('admin can view the admin categories index', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    Category::create([
        'name' => 'Support',
        'slug' => 'support',
    ]);

    $this
        ->actingAs($admin)
        ->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Gestion des categories')
        ->assertSee('Support');
});

test('admin can create update and delete a category', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $this
        ->actingAs($admin)
        ->post(route('admin.categories.store'), [
            'name' => 'Nouveaute',
            'description' => 'Description test',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    $category = Category::where('name', 'Nouveaute')->firstOrFail();

    expect($category->description)->toBe('Description test');

    $this
        ->actingAs($admin)
        ->put(route('admin.categories.update', $category), [
            'name' => 'Actualites',
            'description' => 'Description mise a jour',
        ])
        ->assertRedirect()
        ->assertSessionHas('success');

    expect($category->fresh()->name)->toBe('Actualites');

    $category = $category->fresh();

    $this
        ->actingAs($admin)
        ->delete(route('admin.categories.delete', $category))
        ->assertRedirect()
        ->assertSessionHas('success');

    expect(Category::find($category->id))->toBeNull();
    expect(AdminLog::where('action', 'delete_category')->exists())->toBeTrue();
});
