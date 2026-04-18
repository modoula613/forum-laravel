<?php

use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\Reply;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;

test('guests can view the topics index', function () {
    $user = User::factory()->create();
    $user->topics()->create([
        'title' => 'Premier sujet',
        'content' => 'Contenu du sujet',
    ]);

    $response = $this->get(route('topics.index'));

    $response
        ->assertOk()
        ->assertSee('Premier sujet');
});

test('guests do not see the subscriptions feed tab', function () {
    $user = User::factory()->create();
    $user->topics()->create([
        'title' => 'Sujet public',
        'content' => 'Visible sans connexion',
    ]);

    $this
        ->get(route('topics.index'))
        ->assertOk()
        ->assertSee('Pour toi')
        ->assertDontSee('Abonnements');
});

test('topics index does not show the publishing examples section anymore', function () {
    $user = User::factory()->create();
    $user->topics()->create([
        'title' => 'Sujet public',
        'content' => 'Visible sans connexion',
    ]);

    $this
        ->get(route('topics.index'))
        ->assertOk()
        ->assertDontSee("Besoin d'une idee pour publier ?", false)
        ->assertDontSee('Que se passe-t-il ?')
        ->assertDontSee('Recherche rapide')
        ->assertDontSee('Utiliser cet exemple')
        ->assertDontSee('Utilise la plume en bas pour poster')
        ->assertDontSee('Quel langage ou framework vous a le plus aide a progresser cette annee ?');
});

test('authenticated users can create a topic', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'General',
        'slug' => 'general',
    ]);
    $tag = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Sujet de test',
            'content' => 'Message principal',
            'category_id' => $category->id,
            'tags' => [$tag->id],
        ]);

    $topic = Topic::first();

    $response->assertRedirect(route('topics.show', $topic));

    expect($topic)
        ->not->toBeNull()
        ->title->toBe('Sujet de test')
        ->user_id->toBe($user->id)
        ->category_id->toBe($category->id);
    expect($topic->slug)->not->toBeNull();
    expect($topic->tags()->pluck('tags.id')->all())->toBe([$tag->id]);
});

test('guests can view a topic by its slug', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet avec slug',
        'content' => 'Contenu slug',
    ]);

    $this
        ->get(route('topics.show', $topic))
        ->assertOk()
        ->assertSee('Sujet avec slug');
});

test('authenticated users can view the create topic page', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get(route('topics.create'));

    $response->assertOk();
});

test('authenticated users can start a reaction topic from a news article', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Actualites et debats',
        'slug' => 'actualites-et-debats',
    ]);

    $article = NewsArticle::create([
        'category_id' => $category->id,
        'title' => 'Nouvelle annonce produit',
        'excerpt' => 'Une nouveaute vient d etre devoilee.',
        'content' => 'Contenu complet',
        'source_name' => 'Tech Source',
        'source_url' => 'https://example.test/news/nouvelle-annonce-produit',
        'published_at' => now(),
    ]);

    $this
        ->actingAs($user)
        ->get(route('topics.create', ['news' => $article->id]))
        ->assertOk()
        ->assertSee('Reaction a une actualite')
        ->assertSee($article->title)
        ->assertSee('Reaction : '.$article->title)
        ->assertSee('Ce que j&#039;en pense :', false)
        ->assertSee('name="news_article_id"', false)
        ->assertSee('value="'.$article->id.'"', false)
        ->assertSee((string) $category->id, false)
        ->assertSee($article->source_url, false);
});

test('authenticated users can publish a topic linked to a news article', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Actualites et debats',
        'slug' => 'actualites-et-debats',
    ]);

    $article = NewsArticle::create([
        'category_id' => $category->id,
        'title' => 'Un article a commenter',
        'source_name' => 'Source test',
        'source_url' => 'https://example.test/news/article-a-commenter',
        'published_at' => now(),
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Reaction : Un article a commenter',
            'content' => "Je trouve ce sujet interessant.\n\nVoici mon avis.",
            'category_id' => $category->id,
            'news_article_id' => $article->id,
        ]);

    $topic = Topic::query()->latest('id')->first();

    $response->assertRedirect(route('topics.show', $topic));

    expect($topic)
        ->not->toBeNull()
        ->news_article_id->toBe($article->id);
});

test('topics index paginates results', function () {
    $user = User::factory()->create();

    foreach (range(1, 12) as $index) {
        $user->topics()->create([
            'title' => "Sujet {$index}",
            'content' => "Contenu {$index}",
        ]);
    }

    $response = $this->get(route('topics.index'));

    $response
        ->assertOk()
        ->assertViewHas('topics', fn ($topics) => $topics->count() === 10 && $topics->total() === 12);
});

test('topics index can search by title', function () {
    $user = User::factory()->create();
    $user->topics()->create([
        'title' => 'Laravel Horizon',
        'content' => 'Sujet recherche',
    ]);
    $user->topics()->create([
        'title' => 'Vue Composer',
        'content' => 'Autre sujet',
    ]);

    $response = $this->get(route('topics.index', ['search' => 'Horizon']));

    $response
        ->assertOk()
        ->assertSee('Laravel Horizon')
        ->assertDontSee('Vue Composer');
});

test('topics index marks topics that are reactions to news', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Actualites et debats',
        'slug' => 'actualites-et-debats',
    ]);
    $article = NewsArticle::create([
        'category_id' => $category->id,
        'title' => 'Article important du jour',
        'source_url' => 'https://example.test/news/article-important',
        'published_at' => now(),
    ]);

    $user->topics()->create([
        'title' => 'Mon avis sur l article',
        'content' => 'Je pense que ce sujet merite un vrai debat.',
        'category_id' => $category->id,
        'news_article_id' => $article->id,
    ]);

    $this
        ->get(route('topics.index'))
        ->assertOk()
        ->assertSee('Reaction a une actualite')
        ->assertSee('A partir de : '.$article->title);
});

test('topics index redirects user search prefix to members search', function () {
    $this
        ->get(route('topics.index', ['search' => 'user:camille']))
        ->assertRedirect(route('users.index', ['search' => 'camille']));
});

test('topics index links author names to their public profiles', function () {
    $user = User::factory()->create([
        'name' => 'Camille',
    ]);

    $user->topics()->create([
        'title' => 'Sujet avec auteur',
        'content' => 'Contenu',
    ]);

    $this
        ->get(route('topics.index'))
        ->assertOk()
        ->assertSee(route('users.show', $user), false)
        ->assertSee('Camille');
});

test('topics index can sort by popularity', function () {
    $user = User::factory()->create();

    $popular = $user->topics()->create([
        'title' => 'Sujet populaire',
        'content' => 'Beaucoup de reponses',
    ]);

    $latest = $user->topics()->create([
        'title' => 'Sujet recent',
        'content' => 'Peu de reponses',
    ]);

    foreach (range(1, 3) as $index) {
        Reply::create([
            'topic_id' => $popular->id,
            'user_id' => $user->id,
            'content' => "Reponse {$index}",
        ]);
    }

    Reply::create([
        'topic_id' => $latest->id,
        'user_id' => $user->id,
        'content' => 'Une seule reponse',
    ]);

    $response = $this->get(route('topics.index', ['order' => 'popular']));

    $response->assertOk();

    $content = $response->getContent();

    expect(strpos($content, 'Sujet populaire'))->toBeLessThan(strpos($content, 'Sujet recent'));
});

test('topics index shows pinned topics in a dedicated section before regular topics', function () {
    $user = User::factory()->create();

    $pinnedTopic = $user->topics()->create([
        'title' => 'Sujet epingle',
        'content' => 'Contenu epingle',
        'is_pinned' => true,
    ]);

    $regularTopic = $user->topics()->create([
        'title' => 'Sujet classique',
        'content' => 'Contenu classique',
    ]);

    $response = $this->get(route('topics.index'));

    $response
        ->assertOk()
        ->assertSee('Sujets a la une')
        ->assertSee('Sujet epingle')
        ->assertSee('Sujet classique')
        ->assertViewHas('pinnedTopics', fn ($topics) => $topics->pluck('id')->contains($pinnedTopic->id))
        ->assertViewHas('topics', fn ($topics) => $topics->pluck('id')->contains($regularTopic->id) && ! $topics->pluck('id')->contains($pinnedTopic->id));
});

test('topics index can filter by category', function () {
    $user = User::factory()->create();
    $dev = Category::create([
        'name' => 'Developpement',
        'slug' => 'developpement',
    ]);
    $design = Category::create([
        'name' => 'Design',
        'slug' => 'design',
    ]);

    $user->topics()->create([
        'title' => 'Sujet dev',
        'content' => 'Contenu dev',
        'category_id' => $dev->id,
    ]);

    $user->topics()->create([
        'title' => 'Sujet design',
        'content' => 'Contenu design',
        'category_id' => $design->id,
    ]);

    $this
        ->get(route('topics.index', ['category' => $dev->id]))
        ->assertOk()
        ->assertSee('Sujet dev')
        ->assertDontSee('Sujet design');
});

test('topics index can filter by tag', function () {
    $user = User::factory()->create();
    $laravel = Tag::create([
        'name' => 'Laravel',
        'slug' => 'laravel',
    ]);
    $vue = Tag::create([
        'name' => 'Vue',
        'slug' => 'vue',
    ]);

    $topicWithLaravel = $user->topics()->create([
        'title' => 'Sujet Laravel',
        'content' => 'Contenu Laravel',
    ]);
    $topicWithLaravel->tags()->sync([$laravel->id]);

    $topicWithVue = $user->topics()->create([
        'title' => 'Sujet Vue',
        'content' => 'Contenu Vue',
    ]);
    $topicWithVue->tags()->sync([$vue->id]);

    $this
        ->get(route('topics.index', ['tag' => 'laravel']))
        ->assertOk()
        ->assertSee('Sujet Laravel')
        ->assertDontSee('Sujet Vue');
});

test('users can save a topic as draft', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->post(route('topics.store'), [
            'title' => 'Brouillon test',
            'content' => 'Contenu brouillon',
            'save_draft' => '1',
        ])
        ->assertRedirect(route('topics.drafts'));

    expect(Topic::where('title', 'Brouillon test')->first()?->is_draft)->toBeTrue();
});

test('public topics index hides draft topics', function () {
    $user = User::factory()->create();
    $user->topics()->create([
        'title' => 'Sujet public',
        'content' => 'Visible',
        'is_draft' => false,
    ]);
    $user->topics()->create([
        'title' => 'Sujet brouillon',
        'content' => 'Invisible',
        'is_draft' => true,
    ]);

    $this
        ->get(route('topics.index'))
        ->assertOk()
        ->assertSee('Sujet public')
        ->assertDontSee('Sujet brouillon');
});

test('drafts page redirects back to the forum', function () {
    $user = User::factory()->create();
    $user->topics()->create([
        'title' => 'Mon brouillon',
        'content' => 'Contenu brouillon',
        'is_draft' => true,
    ]);

    $this
        ->actingAs($user)
        ->get(route('topics.drafts'))
        ->assertRedirect(route('topics.index'));
});

test('users can publish their own draft', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Brouillon a publier',
        'content' => 'Contenu',
        'is_draft' => true,
    ]);

    $this
        ->actingAs($user)
        ->patch(route('topics.publish', $topic))
        ->assertRedirect(route('topics.show', $topic));

    expect($topic->fresh()->is_draft)->toBeFalse();
});

test('updating a topic stores its previous content in history', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet historique',
        'content' => 'Contenu original',
    ]);

    $this
        ->actingAs($user)
        ->put(route('topics.update', $topic), [
            'title' => 'Sujet historique',
            'content' => 'Contenu mis a jour',
        ])
        ->assertRedirect(route('topics.show', $topic));

    expect($topic->fresh()->edits()->latest()->first()?->old_content)->toBe('Contenu original');
});

test('topic owner can view topic history', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet historique',
        'content' => 'Contenu actuel',
    ]);
    $topic->edits()->create([
        'old_content' => 'Ancienne version',
    ]);

    $this
        ->actingAs($user)
        ->get(route('topics.history', $topic))
        ->assertOk()
        ->assertSee('Ancienne version');
});

test('topic show marks edited topics as modified', function () {
    $user = User::factory()->create();
    $topic = $user->topics()->create([
        'title' => 'Sujet modifie',
        'content' => 'Contenu actuel',
    ]);
    $topic->edits()->create([
        'old_content' => 'Ancien contenu',
    ]);

    $this
        ->actingAs($user)
        ->get(route('topics.show', $topic))
        ->assertOk()
        ->assertSee('Modifie');
});

test('topic show highlights when a post is a reaction to news', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Actualites et debats',
        'slug' => 'actualites-et-debats',
    ]);
    $article = NewsArticle::create([
        'category_id' => $category->id,
        'title' => 'Actualite source a afficher',
        'source_name' => 'Source test',
        'source_url' => 'https://example.test/news/source-a-afficher',
        'published_at' => now(),
    ]);
    $topic = $user->topics()->create([
        'title' => 'Mon point de vue',
        'content' => 'Je partage ma reaction a cette actualite.',
        'category_id' => $category->id,
        'news_article_id' => $article->id,
    ]);

    $this
        ->get(route('topics.show', $topic))
        ->assertOk()
        ->assertSee('Reaction a une actualite')
        ->assertSee('Actualite source')
        ->assertSee($article->title)
        ->assertSee("Lire l'article d'origine", false)
        ->assertSee($article->source_url, false);
});

test('mon flux route redirects back to the main forum feed', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('topics.feed'))
        ->assertRedirect(route('topics.index'));
});

test('topics index shows follow badge for followed members topics', function () {
    $user = User::factory()->create();
    $followedAuthor = User::factory()->create();

    $user->followingUsers()->attach($followedAuthor->id);

    $followedAuthor->topics()->create([
        'title' => 'Sujet suivi',
        'content' => 'Contenu suivi',
    ]);

    $this
        ->actingAs($user)
        ->get(route('topics.index'))
        ->assertOk()
        ->assertSee('Suivi');
});

test('topics index can filter followed members topics for authenticated users', function () {
    $user = User::factory()->create();
    $followedAuthor = User::factory()->create();
    $otherAuthor = User::factory()->create();

    $user->followingUsers()->attach($followedAuthor->id);

    $followedAuthor->topics()->create([
        'title' => 'Sujet de mon suivi',
        'content' => 'Contenu',
    ]);

    $otherAuthor->topics()->create([
        'title' => 'Sujet hors suivi',
        'content' => 'Contenu',
    ]);

    $this
        ->actingAs($user)
        ->get(route('topics.index', ['following' => 1]))
        ->assertOk()
        ->assertSee('Sujet de mon suivi')
        ->assertDontSee('Sujet hors suivi');
});

test('topics index shows a clear message when user follows nobody', function () {
    $user = User::factory()->create();

    $this
        ->actingAs($user)
        ->get(route('topics.index', ['following' => 1]))
        ->assertOk()
        ->assertSee('Tu ne suis encore personne');
});
