<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReplyBookmarkController extends Controller
{
    public function toggle(Reply $reply): RedirectResponse
    {
        $user = auth()->user();

        if ($user->bookmarkedReplies()->where('reply_id', $reply->id)->exists()) {
            $user->bookmarkedReplies()->detach($reply->id);
        } else {
            $user->bookmarkedReplies()->attach($reply->id);
        }

        return back()->with('success', 'Reponse sauvegardee mise a jour.');
    }

    public function index(): View
    {
        $search = request('search');
        $topicFilter = request('topic');
        $authorFilter = request('author');

        $replies = auth()->user()
            ->bookmarkedReplies()
            ->with(['topic', 'user'])
            ->when($search, fn ($query) => $query->where('content', 'like', "%{$search}%"))
            ->when($topicFilter, fn ($query) => $query->whereHas('topic', fn ($topicQuery) => $topicQuery->where('title', 'like', "%{$topicFilter}%")))
            ->when($authorFilter, fn ($query) => $query->whereHas('user', fn ($userQuery) => $userQuery->where('name', 'like', "%{$authorFilter}%")))
            ->latest('reply_bookmarks.created_at')
            ->paginate(20)
            ->withQueryString();

        return view('replies.bookmarks', compact('replies'));
    }
}
