<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeedConversationTopicsCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_conversation_seed_command_creates_topics_reacting_to_news(): void
    {
        $user = User::factory()->create([
            'email' => 'modoula.elbou@hotmail.com',
        ]);

        $category = Category::create([
            'name' => 'Actualites et debats',
            'slug' => 'actualites-et-debats',
        ]);

        $article = NewsArticle::create([
            'category_id' => $category->id,
            'title' => 'Une actualite a commenter',
            'excerpt' => 'Un sujet qui peut lancer un debat.',
            'source_name' => 'Source test',
            'source_url' => 'https://example.test/news/a-commenter',
            'published_at' => now(),
        ]);

        $this->artisan('forum:seed-conversations', [
            'email' => $user->email,
            '--count' => 1,
        ])->assertSuccessful();

        $topic = Topic::query()->first();

        $this->assertNotNull($topic);
        $this->assertSame($user->id, $topic->user_id);
        $this->assertSame($article->id, $topic->news_article_id);
        $this->assertStringContainsString($article->source_url, $topic->content);
    }
}
