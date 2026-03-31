<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\View\View;

class AnnouncementController extends Controller
{
    public function index(): View
    {
        $announcements = Announcement::where('is_active', true)
            ->latest()
            ->paginate(10);

        return view('announcements.index', compact('announcements'));
    }
}
