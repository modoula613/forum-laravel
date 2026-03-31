<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $query = request('search');

        $users = User::when($query, fn ($builder) => $builder->where('name', 'like', "%{$query}%"))
            ->withCount(['topics', 'replies'])
            ->orderByDesc('replies_count')
            ->paginate(20)
            ->withQueryString();

        return view('users.index', compact('users'));
    }

    public function show(User $user): View
    {
        $user->loadCount(['topics', 'replies'])
            ->load('badges');

        return view('users.show', compact('user'));
    }

    public function activity(User $user): View
    {
        $activities = $user->activities()
            ->latest()
            ->paginate(20);

        return view('users.activity', compact('user', 'activities'));
    }
}
