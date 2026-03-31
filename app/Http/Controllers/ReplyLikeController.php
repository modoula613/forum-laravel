<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Reply;
use App\Models\ReplyLike;
use Illuminate\Http\RedirectResponse;

class ReplyLikeController extends Controller
{
    public function toggle(Reply $reply): RedirectResponse
    {
        $like = ReplyLike::where('reply_id', $reply->id)
            ->where('user_id', auth()->id())
            ->first();

        if ($like) {
            $like->delete();
            $reply->user->addReputation(-2);

            return back()->with('success', 'Like retire.');
        }

        ReplyLike::create([
            'reply_id' => $reply->id,
            'user_id' => auth()->id(),
        ]);

        $replyAuthor = $reply->user;
        $replyAuthor->addReputation(2);
        $totalLikes = $replyAuthor->replies()
            ->withCount('likes')
            ->get()
            ->sum('likes_count');

        if ($totalLikes >= 25) {
            $appreciatedBadge = Badge::where('name', 'Utilisateur apprecie')->first();

            if ($appreciatedBadge) {
                $replyAuthor->badges()->syncWithoutDetaching([$appreciatedBadge->id]);
            }
        }

        return back()->with('success', 'Like ajoute.');
    }
}
