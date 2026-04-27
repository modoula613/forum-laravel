<?php

namespace App\Http\Controllers;

use App\Models\Category;
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
            $query = trim(substr($query, 1));
            $searchTerm = $query;
        } elseif (str_starts_with($query, 'category:')) {
            $searchMode = 'categories';
            $searchTerm = trim(substr($query, 9));
        }

        $searchTerm = trim($searchTerm);

        $matchedCategory = null;

        if ($searchMode === 'categories' && $searchTerm !== '') {
            $matchedCategory = Category::query()
                ->where('name', 'like', "%{$searchTerm}%")
                ->orderBy('name')
                ->first();
        }

        $searchUrl = match ($searchMode) {
            'users' => route('users.index', array_filter(['search' => $searchTerm])),
            'categories' => $matchedCategory
                ? route('categories.show', $matchedCategory)
                : route('topics.index', ['search' => 'category:'.$searchTerm]),
            default => route('topics.index', ['search' => $query]),
        };

        $searchTitle = match ($searchMode) {
            'users' => 'Chercher un membre : '.$searchTerm,
            'categories' => 'Chercher une categorie : '.$searchTerm,
            default => 'Rechercher : '.$query,
        };

        $searchSubtitle = match ($searchMode) {
            'users' => 'Voir la liste des membres correspondants',
            'categories' => 'Ouvrir la categorie ou les sujets les plus proches',
            default => 'Voir les sujets et reactions correspondants',
        };

        $sections[] = [
            'label' => 'Recherche',
            'items' => [[
                'type' => 'search',
                'title' => $searchTitle,
                'subtitle' => $searchSubtitle,
                'url' => $searchUrl,
                'historyValue' => $query,
            ]],
        ];

        if ($searchMode === 'global') {
            $sections[] = [
                'label' => 'Raccourcis utiles',
                'items' => [
                    [
                        'type' => 'shortcut',
                        'title' => 'Chercher ce mot parmi les membres',
                        'subtitle' => 'Ouvrir directement la recherche des profils',
                        'url' => route('users.index', ['search' => $query]),
                        'historyValue' => 'user:'.$query,
                    ],
                    [
                        'type' => 'query',
                        'title' => 'Chercher une categorie avec ce nom',
                        'subtitle' => 'Utile si tu cherches un theme precis',
                        'query' => 'category:'.$query,
                        'historyValue' => 'category:'.$query,
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
                ->where(function ($builder) use ($searchTerm) {
                    $builder
                        ->where('name', 'like', "%{$searchTerm}%")
                        ->orWhere('email', 'like', "%{$searchTerm}%");
                })
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
