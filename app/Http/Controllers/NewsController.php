<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NewsArticle;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class NewsController extends Controller
{
    public function index(): View
    {
        $category = request('category');

        $articles = Schema::hasTable('news_articles')
            ? NewsArticle::with('category')
                ->when($category, fn ($query) => $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $category)))
                ->latest('published_at')
                ->paginate(12)
                ->withQueryString()
            : new LengthAwarePaginator(
                items: collect(),
                total: 0,
                perPage: 12,
                currentPage: 1,
                options: [
                    'path' => request()->url(),
                    'query' => request()->query(),
                ],
            );

        $categories = Category::orderBy('name')->get();

        return view('news.index', compact('articles', 'categories'));
    }
}
