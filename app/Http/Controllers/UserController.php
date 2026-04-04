<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = request('search');
        $followingIds = [];
        $pendingOutgoingIds = [];
        $pendingIncomingIds = [];

        if (auth()->check()) {
            $viewer = auth()->user();
            $followingIds = $viewer->followingUsers()->pluck('users.id')->all();
            $pendingOutgoingIds = $viewer->sentFollowRequests()->pluck('users.id')->all();
            $pendingIncomingIds = $viewer->receivedFollowRequests()->pluck('users.id')->all();
        }

        $users = User::when($query, fn ($builder) => $builder->where('name', 'like', "%{$query}%"))
            ->withCount(['topics', 'replies', 'followingUsers', 'followerUsers'])
            ->orderByDesc('replies_count')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users', 'followingIds', 'pendingOutgoingIds', 'pendingIncomingIds'));
    }

    public function show(User $user): View
    {
        $user->loadCount(['topics', 'replies', 'followingUsers', 'followerUsers'])
            ->load('badges');

        $viewer = auth()->user();
        $isFollowing = $viewer ? $viewer->isFollowing($user) : false;
        $isFollowedBy = $viewer ? $viewer->isFollowedBy($user) : false;
        $isFriend = $viewer ? $viewer->isFriendWith($user) : false;
        $hasPendingRequestTo = $viewer ? $viewer->hasPendingFollowRequestTo($user) : false;
        $hasPendingRequestFrom = $viewer ? $viewer->hasPendingFollowRequestFrom($user) : false;
        $canMessage = $viewer
            ? $viewer->id !== $user->id && $user->isFollowing($viewer)
            : false;
        $friendsCount = $user->friendsCount();

        return view('users.show', compact(
            'user',
            'isFollowing',
            'isFollowedBy',
            'isFriend',
            'hasPendingRequestTo',
            'hasPendingRequestFrom',
            'canMessage',
            'friendsCount'
        ));
    }

    public function activity(User $user): View
    {
        $activities = $user->activities()
            ->latest()
            ->paginate(20);

        return view('users.activity', compact('user', 'activities'));
    }
}
