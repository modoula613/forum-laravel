<?php

namespace App\Http\Controllers;

use App\Models\Badge;
use App\Models\User;
use Illuminate\View\View;

class BadgeController extends Controller
{
    public function index(): View
    {
        $badges = Badge::withCount('users')
            ->orderBy('name')
            ->get();

        return view('badges.index', compact('badges'));
    }

    public function userBadges(User $user): View
    {
        $user->load('badges');
        $badges = $user->badges->sortBy('name')->values();

        return view('badges.user', compact('user', 'badges'));
    }
}
