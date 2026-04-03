<?php

namespace App\Console\Commands;

use App\Models\NewsArticle;
use App\Services\GNewsService;
use App\Services\NewsCategorizer;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class SyncNewsCommand extends Command
{
    protected $signature = 'news:sync {--limit=25}';

    protected $description = 'Synchronise les actualites depuis GNews et les classe automatiquement.';

    public function handle(GNewsService $gnewsService, NewsCategorizer $categorizer): int
    {
        $articles = $gnewsService->fetchTopHeadlines((int) $this->option('limit'));

        if ($articles === []) {
            $this->warn('Aucune actualite synchronisee. Verifie GNEWS_API_KEY ou la disponibilite de l’API.');

            return self::SUCCESS;
        }

        $count = 0;

        foreach ($articles as $article) {
            $sourceUrl = $article['url'] ?? null;

            if (! $sourceUrl) {
                continue;
            }

            NewsArticle::updateOrCreate(
                ['source_url' => $sourceUrl],
                [
                    'category_id' => $categorizer->resolveCategoryId($article),
                    'title' => Str::limit($article['title'] ?? 'Actualite', 255, ''),
                    'excerpt' => $article['description'] ?? null,
                    'content' => $article['content'] ?? null,
                    'source_name' => $article['source']['name'] ?? 'GNews',
                    'image_url' => $article['image'] ?? null,
                    'published_at' => isset($article['publishedAt']) ? Carbon::parse($article['publishedAt']) : now(),
                    'metadata' => [
                        'source' => $article['source'] ?? [],
                    ],
                ]
            );

            $count++;
        }

        $this->info($count.' actualite(s) synchronisee(s).');

        return self::SUCCESS;
    }
}
