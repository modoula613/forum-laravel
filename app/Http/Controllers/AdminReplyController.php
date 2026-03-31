<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Reply;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminReplyController extends Controller
{
    public function index(): View
    {
        $search = request('search');
        $reported = request('reported');

        $replies = Reply::with(['user', 'topic'])
            ->withCount('reports')
            ->when($search, fn ($query) => $query->where('content', 'like', "%{$search}%"))
            ->when($reported === '1', fn ($query) => $query->whereHas('reports'))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.replies.index', compact('replies'));
    }

    public function destroy(Reply $reply): RedirectResponse
    {
        $admin = auth()->user();
        $content = $reply->content;

        $reply->delete();

        AdminLog::create([
            'admin_id' => $admin->id,
            'action' => 'delete_reply',
            'details' => 'Reponse ID '.$reply->id.' supprimee',
        ]);

        UserActivity::create([
            'user_id' => $admin->id,
            'type' => 'admin_reply_deleted',
            'description' => 'A supprime une reponse : '.str($content)->limit(80),
        ]);

        return back()->with('success', 'Reponse supprimee.');
    }
}
