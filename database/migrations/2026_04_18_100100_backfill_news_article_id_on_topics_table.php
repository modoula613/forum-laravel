<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('topics')
            ->select('id', 'content')
            ->whereNull('news_article_id')
            ->orderBy('id')
            ->chunkById(100, function ($topics): void {
                foreach ($topics as $topic) {
                    if (! preg_match('/^Source\s*:\s*(https?:\/\/\S+)/mi', (string) $topic->content, $matches)) {
                        continue;
                    }

                    $sourceUrl = trim($matches[1]);

                    $newsArticleId = DB::table('news_articles')
                        ->where('source_url', $sourceUrl)
                        ->value('id');

                    if (! $newsArticleId) {
                        continue;
                    }

                    DB::table('topics')
                        ->where('id', $topic->id)
                        ->update(['news_article_id' => $newsArticleId]);
                }
            });
    }

    public function down(): void
    {
        // Intentionally left empty: this data backfill is safe to keep.
    }
};
