<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchSuggestionController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = trim((string) $request->string('query'));

        if ($query === '') {
            return response()->json([
                'sections' => [],
            ]);
        }

        $sections = [];

        $searchMode = 'global';
        $searchTerm = $query;

        if (str_starts_with($query, 'user:')) {
            $searchMode = 'users';
            $searchTerm = trim(substr($query, 5));
        } elseif (str_starts_with($query, '#')) {
            $searchMode = 'tags';
            $searchTerm = trim(substr($query, 1));
        } elseif (str_starts_with($query, 'category:')) {
            $searchMode = 'categories';
            $searchTerm = trim(substr($query, 9));
        }

        $searchTerm = trim($searchTerm);

        $sections[] = [
            'label' => 'Recherche',
            'items' => [[
                'type' => 'search',
                'title' => $query,
                'subtitle' => 'Lancer la recherche dans le forum',
                'url' => route('topics.index', ['search' => $query]),
            ]],
        ];

        if ($searchMode === 'global') {
            $sections[] = [
                'label' => 'Suggestions',
                'items' => [
                    [
                        'type' => 'query',
                        'title' => 'user:'.$query,
                        'subtitle' => 'Rechercher un membre',
                        'query' => 'user:'.$query,
                    ],
                    [
                        'type' => 'query',
                        'title' => '#'.$query,
                        'subtitle' => 'Explorer un hashtag',
                        'query' => '#'.$query,
                    ],
                ],
            ];
        }

        if ($searchTerm === '') {
            return response()->json([
                'sections' => array_values(array_filter($sections, fn ($section) => ! empty($section['items']))),
            ]);
        }

        if (in_array($searchMode, ['global', 'topics'], true)) {
            $topics = Topic::query()
                ->select(['id', 'title', 'slug', 'category_id', 'user_id'])
                ->with(['category:id,name', 'user:id,name'])
                ->where('is_draft', false)
                ->where(function ($builder) use ($searchTerm) {
                    $builder
                        ->where('title', 'like', "%{$searchTerm}%")
                        ->orWhere('content', 'like', "%{$searchTerm}%");
                })
                ->latest()
                ->take(4)
                ->get()
                ->map(fn (Topic $topic) => [
                    'type' => 'topic',
                    'title' => $topic->title,
                    'subtitle' => collect([
                        $topic->user?->name,
                        $topic->category?->name,
                    ])->filter()->join(' · '),
                    'url' => route('topics.show', $topic),
                ])
                ->all();

            if ($topics !== []) {
                $sections[] = [
                    'label' => 'Sujets',
                    'items' => $topics,
                ];
            }
        }

        if (in_array($searchMode, ['global', 'users'], true)) {
            $users = User::query()
                ->select(['id', 'name', 'email'])
                ->where('name', 'like', "%{$searchTerm}%")
                ->orderBy('name')
                ->take(4)
                ->get()
                ->map(fn (User $user) => [
                    'type' => 'user',
                    'title' => $user->name,
                    'subtitle' => $user->email,
                    'url' => route('users.show', $user),
                ])
                ->all();

            if ($users !== []) {
                $sections[] = [
                    'label' => 'Membres',
                    'items' => $users,
                ];
            }
        }

        if (in_array($searchMode, ['global', 'tags'], true)) {
            $tags = Tag::query()
                ->select(['id', 'name', 'slug'])
                ->where(function ($builder) use ($searchTerm) {
                    $builder
                        ->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('slug', 'like', "%{$searchTerm}%");
                })
                ->orderBy('name')
                ->take(4)
                ->get()
                ->map(fn (Tag $tag) => [
                    'type' => 'tag',
                    'title' => '#'.$tag->name,
                    'subtitle' => 'Voir les sujets lies a ce tag',
                    'url' => route('tags.show', $tag),
                ])
                ->all();

            if ($tags !== []) {
                $sections[] = [
                    'label' => 'Tags',
                    'items' => $tags,
                ];
            }
        }

        if (in_array($searchMode, ['global', 'categories'], true)) {
            $categories = Category::query()
                ->select(['id', 'name', 'slug'])
                ->where('name', 'like', "%{$searchTerm}%")
                ->orderBy('name')
                ->take(4)
                ->get()
                ->map(fn (Category $category) => [
                    'type' => 'category',
                    'title' => $category->name,
                    'subtitle' => 'Ouvrir la categorie',
                    'url' => route('categories.show', $category),
                ])
                ->all();

            if ($categories !== []) {
                $sections[] = [
                    'label' => 'Categories',
                    'items' => $categories,
                ];
            }
        }

        return response()->json([
            'sections' => array_values(array_filter($sections, fn ($section) => ! empty($section['items']))),
        ]);
    }
}
