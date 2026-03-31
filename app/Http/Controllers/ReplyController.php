<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\Reply;
use App\Models\ReplyEdit;
use App\Models\Topic;
use App\Models\UserActivity;
use App\Notifications\NewReplyNotification;
use App\Notifications\TopicFollowedNewReplyNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;

class ReplyController extends Controller
{
    public function store(Request $request, Topic $topic): RedirectResponse
    {
        abort_if($request->user()->is_banned, 403, 'Votre compte est suspendu.');

        if ($request->user()->is_blocked) {
            return redirect()
                ->route('topics.show', $topic)
                ->with('error', 'Votre compte est bloque suite a plusieurs infractions.');
        }

        abort_if($topic->is_locked, 403, 'Ce sujet est verrouille.');

        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        $reply = Reply::create([
            'topic_id' => $topic->id,
            'user_id' => Auth::id(),
            'content' => $validated['content'],
        ]);

        $user = $request->user();
        $user->addExperience(5);

        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'reply_created',
            'description' => 'A repondu a un sujet',
        ]);

        $repliesCount = $user->replies()->count();

        if ($repliesCount === 1) {
            $firstReplyBadge = Badge::where('name', 'Premier message')->first();

            if ($firstReplyBadge) {
                $user->badges()->syncWithoutDetaching([$firstReplyBadge->id]);
            }
        }

        if ($repliesCount === 10) {
            $activeParticipantBadge = Badge::where('name', 'Participant actif')->first();

            if ($activeParticipantBadge) {
                $user->badges()->syncWithoutDetaching([$activeParticipantBadge->id]);
            }
        }

        if ($repliesCount >= 50) {
            $topContributorBadge = Badge::where('name', 'Top contributeur')->first();

            if ($topContributorBadge) {
                $user->badges()->syncWithoutDetaching([$topContributorBadge->id]);
            }
        }

        if ($topic->replies()->count() >= 20) {
            $popularTopicBadge = Badge::where('name', 'Sujet populaire')->first();

            if ($popularTopicBadge) {
                $topic->user->badges()->syncWithoutDetaching([$popularTopicBadge->id]);
            }
        }

        if ($topic->replies()->count() >= 5) {
            $topic->user->addReputation(5);
        }

        if ($topic->user_id !== $user->id) {
            $topic->user->notify(new NewReplyNotification($topic, $user));
        }

        $followers = $topic->favorites()
            ->with('user')
            ->get()
            ->pluck('user')
            ->filter(fn ($user) => $user !== null)
            ->reject(fn ($follower) => $follower->id === $user->id || $follower->id === $topic->user_id)
            ->unique('id');

        foreach ($followers as $follower) {
            $follower->notify(new TopicFollowedNewReplyNotification($topic, $user));
        }

        return redirect()->route('topics.show', $topic)
            ->with('success', 'Reponse ajoutee avec succes.');
    }

    public function update(Request $request, Reply $reply): RedirectResponse
    {
        abort_unless($reply->user_id === auth()->id(), 403);

        $validated = $request->validate([
            'content' => ['required', 'string'],
        ]);

        ReplyEdit::create([
            'reply_id' => $reply->id,
            'old_content' => $reply->content,
        ]);

        $reply->update($validated);

        return back()->with('success', 'Reponse mise a jour.');
    }

    public function history(Reply $reply)
    {
        abort_unless(
            auth()->check() && (auth()->id() === $reply->user_id || auth()->user()->role === 'admin'),
            403
        );

        $edits = $reply->edits()->latest()->get();

        return view('replies.history', compact('reply', 'edits'));
    }

    public function destroy(Reply $reply): RedirectResponse
    {
        abort_unless(
            $reply->user_id === auth()->id() || auth()->user()->role === 'admin',
            403
        );

        $reply->delete();

        return back()->with('success', 'Reponse supprimee.');
    }
}
