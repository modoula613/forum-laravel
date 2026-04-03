<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GNewsService
{
    public function fetchTopHeadlines(int $limit = 25): array
    {
        $apiKey = config('services.gnews.key');

        if (! $apiKey) {
            return [];
        }

        $response = Http::timeout(15)->get(config('services.gnews.endpoint').'/top-headlines', [
            'apikey' => $apiKey,
            'lang' => config('services.gnews.lang', 'fr'),
            'country' => config('services.gnews.country', 'fr'),
            'max' => min($limit, (int) config('services.gnews.max', 25)),
        ]);

        if (! $response->successful()) {
            return [];
        }

        return $response->json('articles', []);
    }
}
