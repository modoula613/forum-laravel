<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NewsArticle;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('topics')
            ->with(['latestTopic.user'])
            ->orderByDesc('topics_count')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        $category->loadCount('topics');

        $topics = $category->topics()
            ->with(['user', 'category', 'tags'])
            ->withCount('replies')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        $relatedCategories = Category::withCount('topics')
            ->whereKeyNot($category->getKey())
            ->orderByDesc('topics_count')
            ->take(4)
            ->get();

        $categoryNews = Schema::hasTable('news_articles')
            ? NewsArticle::query()
                ->where('category_id', $category->id)
                ->latest('published_at')
                ->take(3)
                ->get()
            : collect();

        return view('categories.show', compact('category', 'topics', 'relatedCategories', 'categoryNews'));
    }
}
