<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
use App\Models\NewsArticle;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StatsController extends Controller
{
    public function index(): View
    {
        $topicsCount = Topic::count();
        $repliesCount = Reply::count();
        $usersCount = User::count();
        $bannedUsers = User::where('is_banned', true)->count();
        $topUsers = User::withCount(['topics', 'replies'])
            ->orderByDesc('replies_count')
            ->take(10)
            ->get();
        $topTopics = Topic::withCount('replies')
            ->orderByDesc('replies_count')
            ->take(10)
            ->get();
        $topLevels = User::orderByDesc('level')
            ->orderByDesc('experience')
            ->take(5)
            ->get();
        $topReputation = User::orderByDesc('reputation')
            ->take(5)
            ->get();
        $popularTags = Tag::withCount('topics')
            ->orderByDesc('topics_count')
            ->take(10)
            ->get();
        $newsCount = Schema::hasTable('news_articles')
            ? NewsArticle::count()
            : 0;

        return view('stats.index', compact(
            'topicsCount',
            'repliesCount',
            'usersCount',
            'bannedUsers',
            'topUsers',
            'topTopics',
            'topLevels',
            'topReputation',
            'popularTags',
            'newsCount'
        ));
    }

    public function leaderboard(): View
    {
        $users = User::withCount(['topics', 'replies'])
            ->orderByDesc('replies_count')
            ->paginate(20);

        return view('stats.leaderboard', compact('users'));
    }
}
