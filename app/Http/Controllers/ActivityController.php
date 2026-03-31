<?php

namespace App\Http\Controllers;

use App\Models\Reply;
use App\Models\Topic;
use Illuminate\View\View;

class ActivityController extends Controller
{
    public function index(): View
    {
        $topics = Topic::with(['user', 'category'])
            ->latest()
            ->take(5)
            ->get();

        $replies = Reply::with(['user', 'topic'])
            ->latest()
            ->take(5)
            ->get();

        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->take(5)
            ->get();

        return view('activity.index', compact('topics', 'replies', 'notifications'));
    }
}
