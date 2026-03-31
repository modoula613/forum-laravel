<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
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
            ->take(10)
            ->get();
        $topReputation = User::orderByDesc('reputation')
            ->take(10)
            ->get();
        $popularTags = Tag::withCount('topics')
            ->orderByDesc('topics_count')
            ->take(10)
            ->get();

        return view('stats.index', compact(
            'topicsCount',
            'repliesCount',
            'usersCount',
            'bannedUsers',
            'topUsers',
            'topTopics',
            'topLevels',
            'topReputation',
            'popularTags'
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
