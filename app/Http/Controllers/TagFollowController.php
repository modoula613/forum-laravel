<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TagFollowController extends Controller
{
    public function index(): View
    {
        $tags = auth()->user()
            ->followedTags()
            ->withCount('topics')
            ->get();

        return view('tags.followed', compact('tags'));
    }

    public function toggle(Tag $tag): RedirectResponse
    {
        $user = auth()->user();

        if ($user->followedTags()->where('tag_id', $tag->id)->exists()) {
            $user->followedTags()->detach($tag->id);
        } else {
            $user->followedTags()->attach($tag->id);
        }

        return back()->with('success', 'Preference de tag mise a jour.');
    }
}
