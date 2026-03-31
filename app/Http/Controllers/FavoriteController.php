<?php

namespace App\Http\Controllers;

use App\Models\Favorite;
use App\Models\Topic;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class FavoriteController extends Controller
{
    public function toggle(Topic $topic): RedirectResponse
    {
        $favorite = Favorite::where('user_id', auth()->id())
            ->where('topic_id', $topic->id)
            ->first();

        if ($favorite) {
            $favorite->delete();

            return back()->with('success', 'Sujet retire de vos favoris.');
        }

        Favorite::create([
            'user_id' => auth()->id(),
            'topic_id' => $topic->id,
        ]);

        return back()->with('success', 'Sujet ajoute a vos favoris.');
    }

    public function index(): View
    {
        $topics = auth()->user()
            ->favorites()
            ->with(['topic.user', 'topic.category', 'topic.tags'])
            ->get()
            ->pluck('topic')
            ->filter();

        return view('favorites.index', compact('topics'));
    }
}
