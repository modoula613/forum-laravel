<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Report;
use App\Models\Topic;
use App\Models\User;
use App\Notifications\ReplyReportedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function reportTopic(Request $request, Topic $topic): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['nullable', 'string'],
        ]);

        Report::create([
            'user_id' => auth()->id(),
            'topic_id' => $topic->id,
            'reason' => $validated['reason'] ?? null,
        ]);

        return back()->with('success', 'Sujet signale.');
    }

    public function reportReply(Request $request, Reply $reply): RedirectResponse
    {
        $validated = $request->validate([
            'reason' => ['required', 'string'],
        ]);

        if (Report::where('reply_id', $reply->id)
            ->where('user_id', auth()->id())
            ->exists()) {
            return back()->with('error', 'Vous avez deja signale cette reponse.');
        }

        Report::create([
            'user_id' => auth()->id(),
            'reply_id' => $reply->id,
            'reason' => $validated['reason'],
        ]);

        $reply->loadMissing('topic');

        $admins = User::where('role', 'admin')->get();

        foreach ($admins as $admin) {
            $admin->notify(new ReplyReportedNotification($reply, $validated['reason']));
        }

        return back()->with('success', 'Reponse signalee.');
    }
}
