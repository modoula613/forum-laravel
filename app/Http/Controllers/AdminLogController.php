<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminLogController extends Controller
{
    public function index(): View
    {
        $action = request('action');
        $search = request('search');

        $logs = AdminLog::with('admin')
            ->when($action, fn ($query) => $query->where('action', $action))
            ->when($search, fn ($query) => $query->where('details', 'like', "%{$search}%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.logs.index', compact('logs'));
    }

    public function clear(): RedirectResponse
    {
        AdminLog::truncate();

        return back()->with('success', 'Logs admin supprimes.');
    }
}
