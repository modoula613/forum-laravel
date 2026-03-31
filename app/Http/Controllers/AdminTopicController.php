<?php

namespace App\Http\Controllers;

use App\Models\Topic;
use Illuminate\View\View;

class AdminTopicController extends Controller
{
    public function index(): View
    {
        $locked = request('locked');
        $pinned = request('pinned');
        $search = request('search');

        $topics = Topic::with('user')
            ->withCount('replies')
            ->when($locked === '1', fn ($query) => $query->where('is_locked', true))
            ->when($pinned === '1', fn ($query) => $query->where('is_pinned', true))
            ->when($search, fn ($query) => $query->where('title', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.topics.index', compact('topics'));
    }
}
