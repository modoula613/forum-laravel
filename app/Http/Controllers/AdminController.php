<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Category;
use App\Models\Reply;
use App\Models\Report;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\User;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $usersCount = User::count();
        $topicsCount = Topic::count();
        $repliesCount = Reply::count();
        $reportsCount = Report::count();
        $tagsCount = Tag::count();
        $categoriesCount = Category::count();
        $adminLogsCount = AdminLog::count();
        $latestReports = Report::with(['reply', 'topic', 'user', 'reply.user'])
            ->latest()
            ->take(5)
            ->get();
        $latestUsers = User::latest()
            ->take(5)
            ->get();
        $latestTopics = Topic::with('user')
            ->latest()
            ->take(5)
            ->get();
        $latestCategories = Category::latest()
            ->take(5)
            ->get();

        return view('admin.index', compact(
            'usersCount',
            'topicsCount',
            'repliesCount',
            'reportsCount',
            'tagsCount',
            'categoriesCount',
            'adminLogsCount',
            'latestReports',
            'latestUsers',
            'latestTopics',
            'latestCategories'
        ));
    }
}
