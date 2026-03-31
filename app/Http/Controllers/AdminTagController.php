<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\UserActivity;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminTagController extends Controller
{
    public function index(): View
    {
        $tags = Tag::withCount('topics')
            ->orderByDesc('topics_count')
            ->paginate(20);

        return view('admin.tags.index', compact('tags'));
    }

    public function destroy(Tag $tag): RedirectResponse
    {
        $name = $tag->name;

        $tag->delete();

        UserActivity::create([
            'user_id' => auth()->id(),
            'type' => 'admin_tag_deleted',
            'description' => "A supprime le tag : {$name}",
        ]);

        return back()->with('success', 'Tag supprime.');
    }
}
