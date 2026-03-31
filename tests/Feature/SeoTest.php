<?php

use App\Models\Category;
use App\Models\Topic;
use App\Models\User;

test('guests can access the sitemap', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Support',
        'slug' => 'support',
    ]);
    $topic = $user->topics()->create([
        'title' => 'Sujet sitemap',
        'content' => 'Contenu sitemap',
        'category_id' => $category->id,
    ]);

    $this
        ->get(route('sitemap'))
        ->assertOk()
        ->assertHeader('Content-Type', 'text/xml; charset=UTF-8')
        ->assertSee(url('/'), false)
        ->assertSee(route('categories.show', $category), false)
        ->assertSee(route('topics.show', $topic), false);
});
