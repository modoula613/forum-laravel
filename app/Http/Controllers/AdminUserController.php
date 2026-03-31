<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    public function index(): View
    {
        $filter = request('banned');
        $search = request('search');

        $users = User::withCount(['topics', 'replies'])
            ->when($filter === '1', fn ($query) => $query->where('is_banned', true))
            ->when($search, fn ($query) => $query->where('name', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.users.index', compact('users'));
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        $user->update([
            'is_blocked' => ! $user->is_blocked,
        ]);

        return back()->with(
            'success',
            $user->is_blocked ? 'Utilisateur bloque.' : 'Utilisateur debloque.'
        );
    }

    public function ban(User $user): RedirectResponse
    {
        $user->update([
            'is_banned' => true,
            'banned_until' => null,
        ]);

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => 'ban_user',
            'details' => "Utilisateur ID {$user->id} banni",
        ]);

        return back()->with('success', 'Utilisateur banni.');
    }

    public function unban(User $user): RedirectResponse
    {
        $user->update([
            'is_banned' => false,
            'banned_until' => null,
        ]);

        AdminLog::create([
            'admin_id' => auth()->id(),
            'action' => 'unban_user',
            'details' => "Utilisateur ID {$user->id} debanni",
        ]);

        return back()->with('success', 'Utilisateur debanni.');
    }
}
