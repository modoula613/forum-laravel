<?php

use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminBadgeController;
use App\Http\Controllers\AdminCategoryController;
use App\Http\Controllers\AdminAnnouncementController;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminReplyController;
use App\Http\Controllers\AdminTagController;
use App\Http\Controllers\AdminTopicController;
use App\Http\Controllers\AnnouncementController;
use App\Http\Controllers\BadgeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReplyController;
use App\Http\Controllers\ReplyBookmarkController;
use App\Http\Controllers\ReplyLikeController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SearchSuggestionController;
use App\Http\Controllers\SeoController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\TagFollowController;
use App\Http\Controllers\TopicController;
use App\Http\Controllers\UserFollowController;
use App\Http\Controllers\UserController;
use App\Models\Topic;
use Illuminate\Support\Facades\Route;

Route::get('/', [TopicController::class, 'index'])->name('home');

Route::get('/dashboard', function () {
    if (request()->user()?->role === 'admin') {
        return redirect()->route('admin.index');
    }

    $user = request()->user();
    $followedUserIds = $user->followingUsers()->pluck('users.id');

    $recommendedTopics = $followedUserIds->isNotEmpty()
        ? Topic::whereIn('user_id', $followedUserIds)
            ->where('is_draft', false)
            ->with(['user', 'tags'])
            ->latest()
            ->take(6)
            ->get()
        : collect();

    $overview = [
        'favorites' => $user->favorites()->count(),
        'following_members' => $user->followingUsers()->count(),
        'bookmarks' => $user->bookmarkedReplies()->count(),
        'unread_notifications' => $user->unreadNotifications()->count(),
        'unread_messages' => $user->unreadMessages()->count(),
    ];

    return view('dashboard', compact('recommendedTopics', 'overview'));
})->middleware(['auth', 'legacy_badge'])->name('dashboard');

Route::get('/topics', [TopicController::class, 'index'])->name('topics.index');
Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/users', [UserController::class, 'index'])->name('users.index');
Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
Route::get('/users/{user}/badges', [BadgeController::class, 'userBadges'])->name('users.badges');
Route::get('/users/{user}/activity', [UserController::class, 'activity'])->name('users.activity');
Route::get('/badges', [BadgeController::class, 'index'])->name('badges.index');
Route::get('/tags', [TagController::class, 'index'])->name('tags.index');
Route::get('/tags/{tag}', [TagController::class, 'show'])->name('tags.show');
Route::get('/announcements', [AnnouncementController::class, 'index'])->name('announcements.index');
Route::get('/news', [NewsController::class, 'index'])->name('news.index');
Route::get('/search/suggestions', SearchSuggestionController::class)->name('search.suggestions');
Route::get('/stats', [StatsController::class, 'index'])->name('stats.index');
Route::get('/leaderboard', [StatsController::class, 'leaderboard'])->name('leaderboard');
Route::get('/sitemap', [SeoController::class, 'sitemap'])->name('sitemap');

Route::middleware(['auth', 'legacy_badge'])->group(function () {
    Route::get('/topics/create', [TopicController::class, 'create'])->name('topics.create');
    Route::post('/topics', [TopicController::class, 'store'])->name('topics.store');
    Route::get('/drafts', [TopicController::class, 'drafts'])->name('topics.drafts');
    Route::get('/topics/{topic}/history', [TopicController::class, 'history'])->name('topics.history');
    Route::get('/topics/{topic}/edit', [TopicController::class, 'edit'])->name('topics.edit');
    Route::put('/topics/{topic}', [TopicController::class, 'update'])->name('topics.update');
    Route::patch('/topics/{topic}/publish', [TopicController::class, 'publish'])->name('topics.publish');
    Route::delete('/topics/{topic}', [TopicController::class, 'destroy'])->name('topics.destroy');

    Route::post('/topics/{topic}/replies', [ReplyController::class, 'store'])
        ->name('replies.store');
    Route::post('/topics/{topic}/favorite', [FavoriteController::class, 'toggle'])
        ->name('topics.favorite');
    Route::post('/topics/{topic}/report', [ReportController::class, 'reportTopic'])
        ->name('topics.report');
    Route::put('/replies/{reply}', [ReplyController::class, 'update'])
        ->name('replies.update');
    Route::get('/replies/{reply}/history', [ReplyController::class, 'history'])
        ->name('replies.history');
    Route::delete('/replies/{reply}', [ReplyController::class, 'destroy'])
        ->name('replies.destroy');
    Route::post('/replies/{reply}/bookmark', [ReplyBookmarkController::class, 'toggle'])
        ->name('replies.bookmark');
    Route::get('/bookmarks', [ReplyBookmarkController::class, 'index'])
        ->name('replies.bookmarks');
    Route::post('/replies/{reply}/like', [ReplyLikeController::class, 'toggle'])
        ->name('replies.like');
    Route::post('/replies/{reply}/report', [ReportController::class, 'reportReply'])
        ->name('replies.report');
    Route::post('/tags/{tag}/follow', [TagFollowController::class, 'toggle'])
        ->name('tags.follow');
    Route::get('/my-tags', [TagFollowController::class, 'index'])
        ->name('tags.followed');
    Route::get('/feed', [TopicController::class, 'feed'])
        ->name('topics.feed');
    Route::post('/users/{user}/follow', [UserFollowController::class, 'toggle'])
        ->name('users.follow');

    Route::get('/notifications', function () {
        request()->user()->unreadNotifications->markAsRead();

        return view('notifications.index');
    })->name('notifications.index');
    Route::get('/favorites', [FavoriteController::class, 'index'])
        ->name('favorites.index');
    Route::get('/activity', [ActivityController::class, 'index'])
        ->name('activity.index');
    Route::get('/messages', [MessageController::class, 'index'])
        ->name('messages.index');
    Route::get('/messages/{user}', [MessageController::class, 'conversation'])
        ->name('messages.conversation');
    Route::post('/messages/send', [MessageController::class, 'send'])
        ->name('messages.send');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])
        ->name('messages.destroy');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])
        ->name('admin.index');
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');
    Route::patch('/admin/users/{user}/toggle-block', [AdminUserController::class, 'toggleBlock'])
        ->name('admin.users.toggleBlock');
    Route::patch('/admin/users/{user}/ban', [AdminUserController::class, 'ban'])
        ->name('admin.users.ban');
    Route::patch('/admin/users/{user}/unban', [AdminUserController::class, 'unban'])
        ->name('admin.users.unban');
    Route::get('/admin/replies', [AdminReplyController::class, 'index'])
        ->name('admin.replies.index');
    Route::delete('/admin/replies/{reply}', [AdminReplyController::class, 'destroy'])
        ->name('admin.replies.delete');
    Route::get('/admin/categories', [AdminCategoryController::class, 'index'])
        ->name('admin.categories.index');
    Route::post('/admin/categories', [AdminCategoryController::class, 'store'])
        ->name('admin.categories.store');
    Route::put('/admin/categories/{category}', [AdminCategoryController::class, 'update'])
        ->name('admin.categories.update');
    Route::delete('/admin/categories/{category}', [AdminCategoryController::class, 'destroy'])
        ->name('admin.categories.delete');
    Route::get('/admin/tags', [AdminTagController::class, 'index'])
        ->name('admin.tags.index');
    Route::delete('/admin/tags/{tag}', [AdminTagController::class, 'destroy'])
        ->name('admin.tags.delete');
    Route::get('/admin/logs', [AdminLogController::class, 'index'])
        ->name('admin.logs.index');
    Route::delete('/admin/logs', [AdminLogController::class, 'clear'])
        ->name('admin.logs.clear');
    Route::get('/admin/badges', [AdminBadgeController::class, 'index'])
        ->name('admin.badges.index');
    Route::post('/admin/users/{user}/badges/{badge}', [AdminBadgeController::class, 'assign'])
        ->name('admin.badges.assign');
    Route::get('/admin/announcements', [AdminAnnouncementController::class, 'index'])
        ->name('admin.announcements.index');
    Route::post('/admin/announcements', [AdminAnnouncementController::class, 'store'])
        ->name('admin.announcements.store');
    Route::put('/admin/announcements/{announcement}', [AdminAnnouncementController::class, 'update'])
        ->name('admin.announcements.update');
    Route::patch('/admin/announcements/{announcement}/toggle', [AdminAnnouncementController::class, 'toggle'])
        ->name('admin.announcements.toggle');
    Route::delete('/admin/announcements/{announcement}', [AdminAnnouncementController::class, 'destroy'])
        ->name('admin.announcements.destroy');
    Route::delete('/admin/topics/{topic}', [TopicController::class, 'adminDestroy'])
        ->name('admin.topics.destroy');
    Route::get('/admin/topics', [AdminTopicController::class, 'index'])
        ->name('admin.topics.index');
    Route::patch('/admin/topics/{topic}/lock', [TopicController::class, 'lock'])
        ->name('admin.topics.lock');
    Route::patch('/admin/topics/{topic}/pin', [TopicController::class, 'pin'])
        ->name('admin.topics.pin');
    Route::get('/admin/reports', [AdminReportController::class, 'index'])
        ->name('admin.reports.index');
    Route::delete('/admin/reports/{report}', [AdminReportController::class, 'destroy'])
        ->name('admin.reports.destroy');
    Route::patch('/admin/reports/{report}/resolve', [AdminReportController::class, 'resolve'])
        ->name('admin.reports.resolve');
    Route::patch('/admin/reports/{report}/ignore', [AdminReportController::class, 'ignore'])
        ->name('admin.reports.ignore');
    Route::delete('/admin/reports/{report}/topic', [AdminReportController::class, 'destroyReportedTopic'])
        ->name('admin.reports.destroyTopic');
    Route::delete('/admin/reports/{report}/reply', [AdminReportController::class, 'destroyReportedReply'])
        ->name('admin.reports.destroyReply');
});

Route::get('/topics/{topic:slug}', [TopicController::class, 'show'])->name('topics.show');

require __DIR__.'/auth.php';
