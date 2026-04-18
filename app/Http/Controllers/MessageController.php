<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use App\Notifications\NewPrivateMessageNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Illuminate\View\View;

class MessageController extends Controller
{
    public function index(Request $request): View
    {
        $currentUser = auth()->user();
        $search = trim((string) $request->string('search'));
        $unreadOnly = $request->boolean('unread');
        $visibleNotificationFilter = fn ($notification) => ($notification->data['type'] ?? null) !== 'new_topic_followed_tag';
        $unreadNotificationCount = $currentUser->unreadNotifications
            ->filter($visibleNotificationFilter)
            ->count();

        $allConversationItems = Message::with(['sender', 'receiver'])
            ->where(function ($query) use ($currentUser) {
                $query->where('sender_id', $currentUser->id)
                    ->orWhere('receiver_id', $currentUser->id);
            })
            ->latest()
            ->get()
            ->groupBy(function ($message) use ($currentUser) {
                return $message->sender_id === $currentUser->id
                    ? $message->receiver_id
                    : $message->sender_id;
            })
            ->map(function ($messages) use ($currentUser) {
                $lastMessage = $messages->first();
                $otherUser = $lastMessage->sender_id === $currentUser->id
                    ? $lastMessage->receiver
                    : $lastMessage->sender;

                return (object) [
                    'user' => $otherUser,
                    'last_message' => $lastMessage,
                    'unread_count' => $messages
                        ->where('receiver_id', $currentUser->id)
                        ->where('is_read', false)
                        ->count(),
                ];
            })
            ->filter(fn ($conversation) => $conversation->user !== null)
            ->values();

        $conversationSummary = [
            'total' => $allConversationItems->count(),
            'unread' => $allConversationItems
                ->filter(fn ($conversation) => $conversation->unread_count > 0)
                ->count(),
        ];

        $normalizedSearch = Str::lower($search);

        $conversationItems = $allConversationItems
            ->filter(function ($conversation) use ($normalizedSearch, $search, $unreadOnly) {
                if ($unreadOnly && $conversation->unread_count === 0) {
                    return false;
                }

                if ($search === '') {
                    return true;
                }

                $userName = Str::lower($conversation->user->name ?? '');
                $lastMessage = Str::lower($conversation->last_message->content ?? '');

                return Str::contains($userName, $normalizedSearch)
                    || Str::contains($lastMessage, $normalizedSearch);
            })
            ->values();

        $conversationSummary['displayed'] = $conversationItems->count();

        if ($currentUser->unreadNotifications->isNotEmpty()) {
            $currentUser->unreadNotifications->markAsRead();
        }

        $allNotifications = $currentUser->notifications()
            ->latest()
            ->get();

        $notifications = $allNotifications
            ->filter($visibleNotificationFilter)
            ->take(12)
            ->values();

        $inboxSummary = [
            'messages_unread' => $conversationSummary['unread'],
            'notifications_unread' => $unreadNotificationCount,
            'notifications_total' => $allNotifications->filter($visibleNotificationFilter)->count(),
        ];

        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $conversations = new LengthAwarePaginator(
            $conversationItems->forPage($currentPage, $perPage)->values(),
            $conversationItems->count(),
            $perPage,
            $currentPage,
            [
                'path' => route('messages.index'),
                'query' => $request->query(),
            ]
        );

        return view('messages.index', compact(
            'conversations',
            'conversationSummary',
            'notifications',
            'inboxSummary',
            'search',
            'unreadOnly'
        ));
    }

    public function conversation(User $user): View|RedirectResponse
    {
        if ($redirect = $this->ensureMessagingAccess($user)) {
            return $redirect;
        }

        $search = request('search');

        Message::where('receiver_id', auth()->id())
            ->where('sender_id', $user->id)
            ->update(['is_read' => true]);

        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', auth()->id())
                ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                ->where('receiver_id', auth()->id());
        })
            ->when($search, fn ($query) => $query->where('content', 'like', "%{$search}%"))
            ->with(['sender', 'receiver'])
            ->orderBy('created_at')
            ->get();

        return view('messages.conversation', compact('messages', 'user'));
    }

    public function send(Request $request): RedirectResponse
    {
        abort_if($request->user()->is_banned, 403, 'Votre compte est suspendu.');

        $validated = $request->validate([
            'receiver_id' => ['required', 'exists:users,id'],
            'content' => ['required', 'string', 'max:2000'],
        ]);

        abort_if((int) $validated['receiver_id'] === (int) auth()->id(), 403);

        $receiver = User::findOrFail($validated['receiver_id']);

        if ($redirect = $this->ensureMessagingAccess($receiver)) {
            return $redirect;
        }

        Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $validated['receiver_id'],
            'content' => $validated['content'],
        ]);

        $receiver->notify(new NewPrivateMessageNotification(auth()->user()));

        return back()->with('success', 'Message envoye.');
    }

    public function destroy(Message $message): RedirectResponse
    {
        abort_unless(
            $message->sender_id === auth()->id() || $message->receiver_id === auth()->id(),
            Response::HTTP_FORBIDDEN
        );

        $message->delete();

        return back()->with('success', 'Message supprime.');
    }

    protected function ensureMessagingAccess(User $user): ?RedirectResponse
    {
        if ((int) $user->id === (int) auth()->id()) {
            return redirect()
                ->route('users.show', $user)
                ->with('error', 'Tu ne peux pas t\'envoyer de message prive a toi-meme.');
        }

        if (! $user->isFollowing(auth()->user())) {
            return redirect()
                ->route('users.show', $user)
                ->with('error', 'Tu peux envoyer un message prive seulement si ce membre te suit aussi.');
        }

        return null;
    }
}
