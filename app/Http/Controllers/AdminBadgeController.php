<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminBadgeController extends Controller
{
    public function index(): View
    {
        $badges = Badge::withCount('users')
            ->orderBy('name')
            ->paginate(20);

        $users = User::orderBy('name')->get(['id', 'name']);

        return view('admin.badges.index', compact('badges', 'users'));
    }

    public function assign(User $user, Badge $badge): RedirectResponse
    {
        $user->badges()->syncWithoutDetaching([$badge->id]);

        return back()->with('success', 'Badge attribue.');
    }
}
