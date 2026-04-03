<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use App\Models\Announcement;
use App\Models\Badge;
use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\Tag;
use App\Models\Topic;
use App\Models\TopicEdit;
use App\Models\UserActivity;
use App\Notifications\NewTopicForFollowedTagNotification;
use App\Notifications\UserWarnedNotification;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class TopicController extends Controller
{
    public function index(): View
    {
        $query = trim((string) request('search'));
        $order = request('order', 'latest');
        $category = request('category');
        $tag = request('tag');
        $followingOnly = request()->boolean('following') || request()->boolean('recommended');
        $categories = Category::query()
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();
        $tags = Tag::query()
            ->select(['id', 'name', 'slug'])
            ->orderBy('name')
            ->get();
        $topicsWithUnreadReplies = auth()->check()
            ? auth()->user()->unreadNotifications
                ->where('type', \App\Notifications\NewReplyNotification::class)
                ->pluck('data.topic_id')
                ->filter()
                ->map(fn ($topicId) => (int) $topicId)
                ->unique()
                ->all()
            : [];
        $followedUserIds = auth()->check()
            ? auth()->user()->followingUsers()->pluck('users.id')
            : collect();
        $followedAuthorIds = auth()->check()
            ? $followedUserIds->map(fn ($id) => (int) $id)->all()
            : [];

        $baseQuery = Topic::query()
            ->with([
                'user:id,name',
                'category:id,name,slug',
            ])
            ->withCount(['replies', 'favorites', 'edits'])
            ->where('is_draft', false)
            ->when(
                $query,
                fn ($builder) => $builder->where(function ($searchQuery) use ($query) {
                    $searchQuery
                        ->where('title', 'like', "%{$query}%")
                        ->orWhere('content', 'like', "%{$query}%");
                })
            )
            ->when($category, fn ($builder) => $builder->where('category_id', $category))
            ->when($tag, fn ($builder) => $builder->whereHas('tags', fn ($tagQuery) => $tagQuery->where('slug', $tag)))
            ->when(
                $followingOnly && auth()->check(),
                function ($builder) use ($followedUserIds) {
                    if ($followedUserIds->isEmpty()) {
                        $builder->whereRaw('0 = 1');

                        return;
                    }

                    $builder->whereIn('user_id', $followedUserIds);
                }
            );

        $pinnedTopics = (clone $baseQuery)
            ->where('is_pinned', true)
            ->when(
                $order === 'popular',
                fn ($builder) => $builder->orderByDesc('replies_count')->latest(),
                fn ($builder) => $builder->latest()
            )
            ->get();

        $topics = (clone $baseQuery)
            ->where('is_pinned', false)
            ->when(
                $order === 'popular',
                fn ($builder) => $builder->orderByDesc('replies_count')->latest(),
                fn ($builder) => $builder->latest()
            )
            ->paginate(10)
            ->withQueryString();

        $forumNews = Schema::hasTable('news_articles')
            ? NewsArticle::with('category')
                ->when($category, fn ($builder) => $builder->where('category_id', $category))
                ->latest('published_at')
                ->take(3)
                ->get()
            : collect();
        $announcements = Announcement::query()
            ->where('is_active', true)
            ->latest()
            ->take(3)
            ->get();

        return view('topics.index', compact(
            'topics',
            'pinnedTopics',
            'categories',
            'tags',
            'topicsWithUnreadReplies',
            'followedAuthorIds',
            'followedUserIds',
            'followingOnly',
            'forumNews',
            'announcements'
        ));
    }

    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('topics.create', compact('categories', 'tags'));
    }

    public function store(Request $request): RedirectResponse
    {
        abort_if($request->user()->is_banned, 403, 'Votre compte est suspendu.');

        if ($request->user()->is_blocked) {
            return redirect()
                ->route('topics.index')
                ->with('error', 'Votre compte est bloque suite a plusieurs infractions.');
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $tagIds = $validated['tags'] ?? [];
        unset($validated['tags']);

        $user = $request->user();
        $isDraft = $request->has('save_draft');

        $topic = $user->topics()->create([
            ...$validated,
            'is_draft' => $isDraft,
        ]);
        $topic->tags()->sync($tagIds);
        $topic->load('tags.followers');
        $user->addExperience(10);

        UserActivity::create([
            'user_id' => $user->id,
            'type' => 'topic_created',
            'description' => 'A cree un sujet : '.$topic->title,
        ]);

        if ($user->topics()->count() === 1) {
            $creatorBadge = Badge::where('name', 'Createur')->first();

            if ($creatorBadge) {
                $user->badges()->syncWithoutDetaching([$creatorBadge->id]);
            }
        }

        if ($isDraft) {
            return redirect()
                ->route('topics.drafts')
                ->with('success', 'Brouillon enregistre.');
        }

        foreach ($topic->tags as $tag) {
            foreach ($tag->followers as $follower) {
                if ($follower->id !== $user->id) {
                    $follower->notify(new NewTopicForFollowedTagNotification($topic, $tag));
                }
            }
        }

        return redirect()
            ->route('topics.show', $topic)
            ->with('success', 'Sujet cree avec succes.');
    }

    public function show(Topic $topic): View
    {
        if ($topic->is_draft) {
            abort_unless(
                auth()->check() && (auth()->id() === $topic->user_id || auth()->user()->role === 'admin'),
                403
            );
        }

        $topic->load([
            'user.badges',
            'category',
            'tags',
            'favorites',
            'replies' => fn ($query) => $query
                ->with(['user.badges', 'likes', 'bookmarkedBy'])
                ->withCount(['likes', 'edits', 'reports'])
                ->oldest(),
        ]);
        $topic->loadCount(['favorites', 'edits']);

        return view('topics.show', compact('topic'));
    }

    public function edit(Topic $topic): View
    {
        abort_unless($topic->user_id === auth()->id(), 403);

        $categories = Category::orderBy('name')->get();
        $tags = Tag::orderBy('name')->get();

        return view('topics.create', compact('topic', 'categories', 'tags'));
    }

    public function update(Request $request, Topic $topic): RedirectResponse
    {
        abort_unless($topic->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['exists:tags,id'],
        ]);

        $tagIds = $validated['tags'] ?? [];
        unset($validated['tags']);

        TopicEdit::create([
            'topic_id' => $topic->id,
            'old_content' => $topic->content,
        ]);

        $topic->update($validated);
        $topic->tags()->sync($tagIds);

        if ($request->has('save_draft')) {
            $topic->update(['is_draft' => true]);

            return redirect()
                ->route('topics.drafts')
                ->with('success', 'Brouillon mis a jour.');
        }

        return redirect()
            ->route('topics.show', $topic)
            ->with('success', 'Sujet mis a jour.');
    }

    public function drafts(): View
    {
        $topics = auth()->user()
            ->topics()
            ->where('is_draft', true)
            ->with(['category', 'tags'])
            ->latest()
            ->paginate(10);

        return view('topics.drafts', compact('topics'));
    }

    public function publish(Topic $topic): RedirectResponse
    {
        abort_unless($topic->user_id === auth()->id(), 403);

        $topic->update(['is_draft' => false]);

        return redirect()
            ->route('topics.show', $topic)
            ->with('success', 'Brouillon publie.');
    }

    public function history(Topic $topic): View
    {
        if ($topic->is_draft) {
            abort_unless(
                auth()->check() && (auth()->id() === $topic->user_id || auth()->user()->role === 'admin'),
                403
            );
        }

        $edits = $topic->edits()->latest()->get();

        return view('topics.history', compact('topic', 'edits'));
    }

    public function feed(): View
    {
        $followedUserIds = auth()->user()->followingUsers()->pluck('users.id');

        $topics = Topic::query()
            ->when(
                $followedUserIds->isEmpty(),
                fn ($builder) => $builder->whereRaw('0 = 1'),
                fn ($builder) => $builder->whereIn('user_id', $followedUserIds)
            )
            ->where('is_draft', false)
            ->with(['user', 'category', 'tags'])
            ->withCount(['replies', 'favorites'])
            ->latest()
            ->paginate(10);

        return view('topics.feed', compact('topics', 'followedUserIds'));
    }

    public function destroy(Topic $topic): RedirectResponse
    {
        $this->authorize('delete', $topic);

        $topic->delete();

        return redirect()
            ->route('topics.index')
            ->with('success', 'Sujet supprime.');
    }

    public function adminDestroy(Topic $topic): RedirectResponse
    {
        $this->authorize('delete', $topic);

        $admin = auth()->user();
        $author = $topic->user;
        $title = $topic->title;

        DB::transaction(function () use ($admin, $author, $topic, $title) {
            $author->increment('warning_count');
            $author->refresh();

            if ($author->warning_count >= 3 && ! $author->is_blocked) {
                $author->update(['is_blocked' => true]);
            }

            $author->notify(new UserWarnedNotification($author->warning_count));
            $topic->delete();

            AdminLog::create([
                'admin_id' => $admin->id,
                'action' => 'delete_topic',
                'details' => "Sujet ID {$topic->id} supprime ({$title})",
            ]);

            UserActivity::create([
                'user_id' => $admin->id,
                'type' => 'admin_topic_deleted',
                'description' => "A supprime le sujet : {$title}",
            ]);
        });

        $author->refresh();

        $message = $author->is_blocked
            ? "Sujet supprime. {$author->name} a atteint 3 avertissements et son compte est maintenant bloque."
            : "Sujet supprime. {$author->name} a recu un avertissement.";

        return redirect()
            ->route('topics.index')
            ->with('success', $message);
    }

    public function lock(Topic $topic): RedirectResponse
    {
        $topic->update([
            'is_locked' => ! $topic->is_locked,
        ]);

        UserActivity::create([
            'user_id' => auth()->id(),
            'type' => 'admin_topic_lock_toggled',
            'description' => $topic->is_locked
                ? "A verrouille le sujet : {$topic->title}"
                : "A deverrouille le sujet : {$topic->title}",
        ]);

        return back()->with(
            'success',
            $topic->is_locked ? 'Sujet verrouille.' : 'Sujet deverrouille.'
        );
    }

    public function pin(Topic $topic): RedirectResponse
    {
        $topic->update([
            'is_pinned' => ! $topic->is_pinned,
        ]);

        UserActivity::create([
            'user_id' => auth()->id(),
            'type' => 'admin_topic_pin_toggled',
            'description' => $topic->is_pinned
                ? "A epingle le sujet : {$topic->title}"
                : "A desepingle le sujet : {$topic->title}",
        ]);

        return back()->with(
            'success',
            $topic->is_pinned ? 'Sujet epingle.' : 'Sujet desepingle.'
        );
    }
}
