<?php

use App\Models\Category;
use App\Models\NewsArticle;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Http;

test('guests can view the news index', function () {
    $category = Category::create([
        'name' => 'Sport',
        'description' => 'Toute l actualite sportive',
        'slug' => 'sport',
    ]);

    NewsArticle::create([
        'category_id' => $category->id,
        'title' => 'Finale europeenne ce soir',
        'excerpt' => 'Le match attire deja beaucoup d attention.',
        'content' => 'Contenu complet de l article.',
        'source_name' => 'Source test',
        'source_url' => 'https://example.test/news/finale-europeenne',
        'published_at' => now(),
    ]);

    $this
        ->get(route('news.index'))
        ->assertOk()
        ->assertSee('Le fil des actualites')
        ->assertSee('Finale europeenne ce soir')
        ->assertSee('Sport');
});

test('news can be filtered by category slug', function () {
    $sport = Category::create([
        'name' => 'Sport',
        'description' => 'Toute l actualite sportive',
        'slug' => 'sport',
    ]);

    $culture = Category::create([
        'name' => 'Culture et loisirs',
        'description' => 'Sorties et divertissement',
        'slug' => 'culture-et-loisirs',
    ]);

    NewsArticle::create([
        'category_id' => $sport->id,
        'title' => 'Victoire en championnat',
        'source_url' => 'https://example.test/news/victoire-championnat',
        'published_at' => now()->subHour(),
    ]);

    NewsArticle::create([
        'category_id' => $culture->id,
        'title' => 'Nouveau festival en ville',
        'source_url' => 'https://example.test/news/nouveau-festival',
        'published_at' => now(),
    ]);

    $this
        ->get(route('news.index', ['category' => 'sport']))
        ->assertOk()
        ->assertSee('Victoire en championnat')
        ->assertDontSee('Nouveau festival en ville');
});

test('news sync imports articles from gnews and classifies them automatically', function () {
    config()->set('services.gnews.key', 'test-key');
    config()->set('services.gnews.endpoint', 'https://gnews.test/api/v4');
    config()->set('services.gnews.lang', 'fr');
    config()->set('services.gnews.country', 'fr');
    config()->set('services.gnews.max', 10);

    $fallback = Category::create([
        'name' => 'Actualites et debats',
        'description' => 'Actualites generales',
        'slug' => 'actualites-et-debats',
    ]);

    $sport = Category::create([
        'name' => 'Sport',
        'description' => 'Toute l actualite sportive',
        'slug' => 'sport',
    ]);

    Http::fake([
        'https://gnews.test/*' => Http::response([
            'articles' => [
                [
                    'title' => 'Le match de football du week-end attire les foules',
                    'description' => 'Une grande finale se joue ce soir.',
                    'content' => 'Les supporters attendent ce match depuis des semaines.',
                    'url' => 'https://example.test/news/match-football',
                    'image' => 'https://example.test/images/match.jpg',
                    'publishedAt' => now()->toIso8601String(),
                    'source' => [
                        'name' => 'GNews test',
                        'url' => 'https://example.test',
                    ],
                ],
            ],
        ], 200),
    ]);

    Artisan::call('news:sync', ['--limit' => 1]);

    $article = NewsArticle::query()->first();

    expect($article)->not->toBeNull();
    expect($article->title)->toBe('Le match de football du week-end attire les foules');
    expect($article->source_url)->toBe('https://example.test/news/match-football');
    expect($article->category_id)->toBe($sport->id);
    expect($article->category_id)->not->toBe($fallback->id);
});

test('topics index surfaces related news articles', function () {
    $category = Category::create([
        'name' => 'Sport',
        'description' => 'Toute l actualite sportive',
        'slug' => 'sport',
    ]);

    NewsArticle::create([
        'category_id' => $category->id,
        'title' => 'Retour du grand championnat',
        'source_url' => 'https://example.test/news/retour-championnat',
        'published_at' => now(),
    ]);

    $this
        ->get(route('topics.index'))
        ->assertOk()
        ->assertSee('Actualites reliees')
        ->assertSee('Retour du grand championnat');
});

test('category page displays news tied to that category', function () {
    $sport = Category::create([
        'name' => 'Sport',
        'description' => 'Toute l actualite sportive',
        'slug' => 'sport',
    ]);

    $culture = Category::create([
        'name' => 'Culture et loisirs',
        'description' => 'Sorties et divertissement',
        'slug' => 'culture-et-loisirs',
    ]);

    NewsArticle::create([
        'category_id' => $sport->id,
        'title' => 'Coupe nationale ce week-end',
        'source_url' => 'https://example.test/news/coupe-nationale',
        'published_at' => now(),
    ]);

    NewsArticle::create([
        'category_id' => $culture->id,
        'title' => 'Nouveau concert annonce',
        'source_url' => 'https://example.test/news/nouveau-concert',
        'published_at' => now()->subHour(),
    ]);

    $this
        ->get(route('categories.show', $sport))
        ->assertOk()
        ->assertSee('Actualites liees')
        ->assertSee('Coupe nationale ce week-end')
        ->assertDontSee('Nouveau concert annonce');
});
