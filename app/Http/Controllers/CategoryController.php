<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        $categories = Category::withCount('topics')
            ->orderByDesc('topics_count')
            ->get();

        return view('categories.index', compact('categories'));
    }

    public function show(Category $category): View
    {
        $topics = $category->topics()
            ->with(['user', 'category'])
            ->withCount('replies')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('categories.show', compact('category', 'topics'));
    }
}
