<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\View\View;

class TagController extends Controller
{
    public function index(): View
    {
        $search = request('search');

        $tags = Tag::when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->withCount('topics')
            ->orderByDesc('topics_count')
            ->paginate(20)
            ->withQueryString();

        return view('tags.index', compact('tags'));
    }

    public function show(Tag $tag): View
    {
        $tag->loadCount('followers');
        $topics = $tag->topics()
            ->with(['user', 'category', 'tags'])
            ->withCount(['replies', 'favorites'])
            ->latest()
            ->paginate(10);

        return view('tags.show', compact('tag', 'topics'));
    }
}
